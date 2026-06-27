<?php
/**
 * MembershipController
 * Quản lý đăng ký hội viên Pro
 */
class MembershipController extends Controller {

    /**
     * Trang giới thiệu gói Pro + pricing
     */
    public function index() {
        Middleware::requireLogin();

        $db = getDB();

        // Lấy danh sách gói
        $stmt = $db->query("SELECT * FROM membership_plans ORDER BY duration_months ASC");
        $plans = $stmt->fetchAll();

        // Lấy lịch sử đăng ký của user
        $stmt = $db->prepare("
            SELECT mo.*, mp.name as plan_name, mp.duration_months 
            FROM membership_orders mo 
            JOIN membership_plans mp ON mo.plan_id = mp.id
            WHERE mo.user_id = :user_id 
            ORDER BY mo.activated_at DESC LIMIT 10
        ");
        $stmt->execute(['user_id' => $_SESSION['user_id']]);
        $orders = $stmt->fetchAll();

        // Kiểm tra đơn pending
        $stmt = $db->prepare("SELECT COUNT(*) FROM membership_orders WHERE user_id = :uid AND status = 'pending'");
        $stmt->execute(['uid' => $_SESSION['user_id']]);
        $hasPending = $stmt->fetchColumn() > 0;

        // Lấy balance từ DB (Middleware::user() không có balance)
        $balStmt = $db->prepare("SELECT balance FROM users WHERE id = :id");
        $balStmt->execute(['id' => $_SESSION['user_id']]);
        $userBalance = (int)$balStmt->fetchColumn();

        $userData = Middleware::user();
        $userData['balance'] = $userBalance;

        $this->view('membership/index', [
            'title'      => 'Nâng cấp Pro - ' . APP_NAME,
            'plans'      => $plans,
            'orders'     => $orders,
            'user'       => $userData,
            'isPro'      => Middleware::isPro(),
            'hasPending' => $hasPending
        ]);
    }

    /**
     * Thanh toán bằng ví (AJAX)
     * Trừ balance → tạo order completed → kích hoạt Pro
     */
    public function createOrder() {
        Middleware::requireLogin();

        if (!$this->isMethod('POST')) {
            return $this->json(['error' => 'Method not allowed'], 405);
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $planId = intval($input['plan_id'] ?? 0);

        if (!$planId) {
            return $this->json(['error' => 'Gói không hợp lệ.'], 400);
        }

        $db = getDB();

        // Kiểm tra gói tồn tại
        $stmt = $db->prepare("SELECT * FROM membership_plans WHERE id = :id");
        $stmt->execute(['id' => $planId]);
        $plan = $stmt->fetch();

        if (!$plan) {
            return $this->json(['error' => 'Gói không tồn tại.'], 400);
        }

        try {
            $db->beginTransaction();

            // Kiểm tra balance (FOR UPDATE phải trong transaction để lock row)
            $bal = $db->prepare("SELECT balance FROM users WHERE id = :id FOR UPDATE");
            $bal->execute(['id' => $_SESSION['user_id']]);
            $currentBalance = (int)$bal->fetchColumn();

            if ($currentBalance < $plan['price']) {
                $db->rollBack();
                return $this->json([
                    'error' => 'Số dư ví không đủ. Cần ' . number_format($plan['price']) . 'đ, hiện có ' . number_format($currentBalance) . 'đ.',
                    'need_deposit' => true,
                    'shortage' => $plan['price'] - $currentBalance
                ], 400);
            }

            // Tính ngày hết hạn
            $now = new DateTime();
            $currentExpiry = $_SESSION['membership_expired_at'] ?? null;
            if ($currentExpiry && strtotime($currentExpiry) > time()) {
                $baseDate = new DateTime($currentExpiry);
            } else {
                $baseDate = $now;
            }
            $expiredAt = $baseDate->modify('+' . $plan['duration_months'] . ' months')->format('Y-m-d H:i:s');

            // 1. Trừ balance
            $newBalance = $currentBalance - (int)$plan['price'];
            $db->prepare("UPDATE users SET balance=:bal, membership='pro', membership_expired_at=:exp WHERE id=:id")
               ->execute(['bal' => $newBalance, 'exp' => $expiredAt, 'id' => $_SESSION['user_id']]);

            // 2. Tạo order completed
            $db->prepare("
                INSERT INTO membership_orders (user_id, plan_id, amount, payment_method, status, expired_at)
                VALUES (:uid, :pid, :amount, 'wallet', 'completed', :exp)
            ")->execute([
                'uid' => $_SESSION['user_id'],
                'pid' => $planId,
                'amount' => $plan['price'],
                'exp' => $expiredAt
            ]);

            // 3. Ghi wallet transaction
            $db->prepare("
                INSERT INTO wallet_transactions (user_id, type, amount, balance_after, status, description, processed_at)
                VALUES (:uid, 'purchase', :amount, :bal, 'completed', :desc, NOW())
            ")->execute([
                'uid' => $_SESSION['user_id'],
                'amount' => $plan['price'],
                'bal' => $newBalance,
                'desc' => 'Mua gói ' . $plan['name']
            ]);

            $db->commit();

            // Cập nhật session
            $_SESSION['membership'] = 'pro';
            $_SESSION['membership_expired_at'] = $expiredAt;

            return $this->json([
                'success' => true,
                'message' => '🎉 Mua gói ' . $plan['name'] . ' thành công! Tài khoản đã được nâng cấp Pro.',
                'expired_at' => date('d/m/Y', strtotime($expiredAt)),
                'new_balance' => $newBalance
            ]);

        } catch (Exception $e) {
            $db->rollBack();
            return $this->json(['error' => 'Có lỗi xảy ra. Vui lòng thử lại.'], 500);
        }
    }

    /**
     * Kiểm tra trạng thái đơn pending (AJAX polling)
     */
    public function checkStatus() {
        Middleware::requireLogin();

        $db = getDB();

        // Kiểm tra còn đơn pending không
        $stmt = $db->prepare("SELECT status FROM membership_orders WHERE user_id = :uid ORDER BY id DESC LIMIT 1");
        $stmt->execute(['uid' => $_SESSION['user_id']]);
        $order = $stmt->fetch();

        if (!$order) {
            return $this->json(['status' => 'none']);
        }

        if ($order['status'] === 'completed') {
            // Đơn đã được duyệt! Cập nhật session
            $stmt = $db->prepare("SELECT membership, membership_expired_at FROM users WHERE id = :id");
            $stmt->execute(['id' => $_SESSION['user_id']]);
            $user = $stmt->fetch();
            $_SESSION['membership'] = $user['membership'];
            $_SESSION['membership_expired_at'] = $user['membership_expired_at'];

            return $this->json([
                'status' => 'completed',
                'message' => '🎉 Thanh toán đã được xác nhận! Tài khoản đã nâng cấp Pro!',
                'expired_at' => date('d/m/Y', strtotime($user['membership_expired_at']))
            ]);
        }

        return $this->json(['status' => $order['status']]);
    }

    /**
     * Kích hoạt mã Pro (AJAX)
     */
    public function activate() {
        Middleware::requireLogin();

        if (!$this->isMethod('POST')) {
            return $this->json(['error' => 'Method not allowed'], 405);
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $code = trim($input['code'] ?? '');

        if (empty($code)) {
            return $this->json(['error' => 'Vui lòng nhập mã kích hoạt.'], 400);
        }

        $db = getDB();

        // Tìm mã kích hoạt
        $stmt = $db->prepare("
            SELECT ac.*, mp.name as plan_name, mp.duration_months 
            FROM activation_codes ac 
            JOIN membership_plans mp ON ac.plan_id = mp.id
            WHERE ac.code = :code
        ");
        $stmt->execute(['code' => strtoupper($code)]);
        $activation = $stmt->fetch();

        if (!$activation) {
            return $this->json(['error' => 'Mã kích hoạt không hợp lệ.'], 400);
        }

        if ($activation['is_used']) {
            return $this->json(['error' => 'Mã kích hoạt đã được sử dụng.'], 400);
        }

        // Tính ngày hết hạn
        $now = new DateTime();
        $currentExpiry = $_SESSION['membership_expired_at'] ?? null;
        if ($currentExpiry && strtotime($currentExpiry) > time()) {
            $baseDate = new DateTime($currentExpiry);
        } else {
            $baseDate = $now;
        }
        $expiredAt = $baseDate->modify('+' . $activation['duration_months'] . ' months')->format('Y-m-d H:i:s');

        try {
            $db->beginTransaction();

            // 1. Đánh dấu mã đã sử dụng
            $stmt = $db->prepare("UPDATE activation_codes SET is_used = 1, used_by = :user_id, used_at = NOW() WHERE id = :id");
            $stmt->execute(['user_id' => $_SESSION['user_id'], 'id' => $activation['id']]);

            // 2. Cập nhật user
            $stmt = $db->prepare("UPDATE users SET membership = 'pro', membership_expired_at = :expired WHERE id = :id");
            $stmt->execute(['expired' => $expiredAt, 'id' => $_SESSION['user_id']]);

            // 3. Tạo order
            $stmt = $db->prepare("
                INSERT INTO membership_orders (user_id, plan_id, activation_code, amount, payment_method, status, expired_at)
                VALUES (:user_id, :plan_id, :code, 0, 'activation_code', 'completed', :expired)
            ");
            $stmt->execute([
                'user_id' => $_SESSION['user_id'],
                'plan_id' => $activation['plan_id'],
                'code'    => $activation['code'],
                'expired' => $expiredAt
            ]);

            $db->commit();

            // Cập nhật session
            $_SESSION['membership'] = 'pro';
            $_SESSION['membership_expired_at'] = $expiredAt;

            return $this->json([
                'success'    => true,
                'plan_name'  => $activation['plan_name'],
                'expired_at' => date('d/m/Y', strtotime($expiredAt)),
                'message'    => 'Kích hoạt thành công! Bạn đã là hội viên ' . $activation['plan_name']
            ]);

        } catch (Exception $e) {
            $db->rollBack();
            return $this->json(['error' => 'Có lỗi xảy ra. Vui lòng thử lại.'], 500);
        }
    }
}

