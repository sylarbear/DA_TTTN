<?php

/**
 * MembershipController
 * Nâng cấp Pro qua chuyển khoản ngân hàng (QR)
 * Admin duyệt đơn thủ công
 */
class MembershipController extends Controller
{
    /**
     * Trang chọn gói Pro + QR thanh toán
     */
    public function index()
    {
        Middleware::requireLogin();

        $db = getDB();

        // Danh sách gói
        $plans = $db->query('SELECT * FROM membership_plans ORDER BY duration_months ASC')->fetchAll();

        // Lịch sử đơn của user
        $stmt = $db->prepare('
            SELECT mo.*, mp.name as plan_name, mp.duration_months
            FROM membership_orders mo
            JOIN membership_plans mp ON mo.plan_id = mp.id
            WHERE mo.user_id = :user_id
            ORDER BY mo.activated_at DESC LIMIT 10
        ');
        $stmt->execute(['user_id' => $_SESSION['user_id']]);
        $orders = $stmt->fetchAll();

        // Kiểm tra có đơn pending không
        $stmt = $db->prepare("SELECT COUNT(*) FROM membership_orders WHERE user_id = :uid AND status = 'pending'");
        $stmt->execute(['uid' => $_SESSION['user_id']]);
        $hasPending = $stmt->fetchColumn() > 0;

        $this->view('membership/index', [
            'title' => 'Nâng cấp Pro - ' . APP_NAME,
            'plans' => $plans,
            'orders' => $orders,
            'user' => Middleware::user(),
            'isPro' => Middleware::isPro(),
            'hasPending' => $hasPending,
        ]);
    }

    /**
     * Tạo đơn chuyển khoản (AJAX)
     * Lưu order pending + transfer_note, chờ admin duyệt
     */
    public function createOrder()
    {
        Middleware::requireLogin();

        if (!$this->isMethod('POST')) {
            return $this->json(['error' => 'Method not allowed'], 405);
        }

        $input = Request::json();
        $planId = intval($input['plan_id'] ?? 0);
        $transferNote = trim($input['transfer_note'] ?? '');

        if (!$planId) {
            return $this->json(['error' => 'Vui lòng chọn gói.'], 400);
        }

        $db = getDB();

        // Kiểm tra gói tồn tại
        $stmt = $db->prepare('SELECT * FROM membership_plans WHERE id = :id');
        $stmt->execute(['id' => $planId]);
        $plan = $stmt->fetch();

        if (!$plan) {
            return $this->json(['error' => 'Gói không tồn tại.'], 400);
        }

        // Kiểm tra đã có đơn pending chưa
        $pending = $db->prepare("SELECT COUNT(*) FROM membership_orders WHERE user_id = :uid AND status = 'pending'");
        $pending->execute(['uid' => $_SESSION['user_id']]);
        if ($pending->fetchColumn() > 0) {
            return $this->json(['error' => 'Bạn đã có một đơn đang chờ duyệt. Vui lòng đợi admin xử lý.'], 400);
        }

        // Tạo đơn pending
        $stmt = $db->prepare('
            INSERT INTO membership_orders (user_id, plan_id, amount, payment_method, transfer_note, status)
            VALUES (:uid, :pid, :amount, :method, :note, :status)
        ');
        $stmt->execute([
            'uid' => $_SESSION['user_id'],
            'pid' => $planId,
            'amount' => $plan['price'],
            'method' => 'bank_transfer',
            'note' => $transferNote,
            'status' => 'pending',
        ]);

        return $this->json([
            'success' => true,
            'message' => 'Đơn đã được tạo! Vui lòng chuyển khoản với nội dung đã khai báo. Admin sẽ duyệt đơn trong thời gian sớm nhất.',
        ]);
    }
}
