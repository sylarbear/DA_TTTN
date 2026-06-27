<?php
/**
 * SupportController
 * Hệ thống gửi ticket hỗ trợ + hủy đơn
 * 
 * CHÍNH SÁCH HỦY ĐƠN:
 * 1. Chỉ đơn trạng thái "pending" (chưa kích hoạt) mới được hủy
 * 2. Đơn phải được tạo trong vòng 24 giờ
 * 3. Đơn đã completed (đã kích hoạt) → không thể hủy, chỉ có thể gửi ticket yêu cầu hoàn tiền
 * 4. Gói 1 tháng: hoàn 100% nếu hủy trong 24h, 0% sau 24h
 * 5. Gói 3 tháng trở lên: hoàn 100% nếu hủy trong 24h, 50% nếu hủy trong 7 ngày, 0% sau đó
 * 6. Mỗi đơn chỉ được tạo 1 ticket hủy
 */
class SupportController extends Controller {

    public function __construct() {
        Middleware::requireLogin();
    }

    /**
     * Kiểm tra điều kiện hủy đơn
     * Trả về mảng: ['can_cancel' => bool, 'reason' => string, 'refund_percent' => int, 'policy_note' => string]
     */
    public static function checkCancelEligibility($order, $plan = null) {
        $result = [
            'can_cancel' => false,
            'reason' => '',
            'refund_percent' => 0,
            'policy_note' => ''
        ];

        // 1. Đơn đã cancelled → không thể hủy lại
        if ($order['status'] === 'cancelled') {
            $result['reason'] = 'Đơn này đã được hủy trước đó.';
            return $result;
        }

        // 2. Đơn đã completed (đã kích hoạt) → cần ticket hoàn tiền đặc biệt
        if ($order['status'] === 'completed') {
            $result['reason'] = 'Đơn đã được kích hoạt. Bạn không thể hủy trực tiếp, nhưng có thể gửi ticket yêu cầu hoàn tiền.';
            $result['policy_note'] = 'refund_request';
            return $result;
        }

        // 3. Đơn pending → kiểm tra thời gian
        $createdAt = strtotime($order['activated_at']); // activated_at = created timestamp
        $hoursSinceOrder = (time() - $createdAt) / 3600;
        $daysSinceOrder = $hoursSinceOrder / 24;
        $durationMonths = $plan ? (int)$plan['duration_months'] : 1;

        // 4. Check đã có ticket hủy đơn này chưa
        $db = getDB();
        $existing = $db->prepare("SELECT id, status FROM support_tickets WHERE related_order_id = :oid AND type = 'cancel_order' AND status NOT IN ('closed')");
        $existing->execute(['oid' => $order['id']]);
        if ($existing->fetch()) {
            $result['reason'] = 'Bạn đã gửi yêu cầu hủy cho đơn này. Vui lòng chờ Admin xử lý.';
            return $result;
        }

        // 5. Tính phần trăm hoàn tiền (refund)
        if ($hoursSinceOrder <= 24) {
            // Trong 24h đầu → hoàn 100%
            $result['can_cancel'] = true;
            $result['refund_percent'] = 100;
            $result['reason'] = 'Đơn trong vòng 24 giờ — đủ điều kiện hủy.';
            $result['policy_note'] = 'Hoàn 100% giá trị đơn hàng.';
        } elseif ($daysSinceOrder <= 7 && $durationMonths >= 3) {
            // Gói >= 3 tháng, trong 7 ngày → hoàn 50%
            $result['can_cancel'] = true;
            $result['refund_percent'] = 50;
            $result['reason'] = 'Đơn gói ' . $durationMonths . ' tháng, trong 7 ngày — đủ điều kiện hủy.';
            $result['policy_note'] = 'Hoàn 50% giá trị đơn hàng (do đã quá 24 giờ).';
        } else {
            // Quá thời hạn hủy
            $result['can_cancel'] = false;
            $hoursText = $hoursSinceOrder < 48 ? round($hoursSinceOrder) . ' giờ' : round($daysSinceOrder) . ' ngày';
            $result['reason'] = 'Đã quá thời hạn hủy đơn (' . $hoursText . ' kể từ khi đặt).';
            if ($durationMonths >= 3) {
                $result['policy_note'] = 'Gói ' . $durationMonths . ' tháng chỉ được hủy trong 7 ngày đầu. Gói 1 tháng chỉ được hủy trong 24 giờ.';
            } else {
                $result['policy_note'] = 'Gói 1 tháng chỉ được hủy trong 24 giờ đầu.';
            }
        }

        return $result;
    }

    /** Danh sách ticket của user */
    public function index() {
        $db = getDB();
        $stmt = $db->prepare("
            SELECT st.*, mo.status as order_status, mp.name as plan_name
            FROM support_tickets st
            LEFT JOIN membership_orders mo ON st.related_order_id = mo.id
            LEFT JOIN membership_plans mp ON mo.plan_id = mp.id
            WHERE st.user_id = :uid
            ORDER BY st.created_at DESC
        ");
        $stmt->execute(['uid' => $_SESSION['user_id']]);
        $tickets = $stmt->fetchAll();

        $this->view('support/index', [
            'title' => 'Hỗ trợ - ' . APP_NAME,
            'tickets' => $tickets,
            'user' => Middleware::user()
        ]);
    }

    /** Form tạo ticket */
    public function create() {
        $db = getDB();

        // Lấy đơn pending + plan info
        $stmt = $db->prepare("
            SELECT mo.id, mo.amount, mo.activated_at, mo.status,
                   mp.name as plan_name, mp.duration_months
            FROM membership_orders mo
            JOIN membership_plans mp ON mo.plan_id = mp.id
            WHERE mo.user_id = :uid AND mo.status = 'pending'
            ORDER BY mo.id DESC
        ");
        $stmt->execute(['uid' => $_SESSION['user_id']]);
        $pendingOrders = $stmt->fetchAll();

        // Kiểm tra điều kiện hủy cho từng đơn
        foreach ($pendingOrders as &$o) {
            $eligibility = self::checkCancelEligibility($o, $o);
            $o['can_cancel'] = $eligibility['can_cancel'];
            $o['cancel_reason'] = $eligibility['reason'];
            $o['refund_percent'] = $eligibility['refund_percent'];
            $o['policy_note'] = $eligibility['policy_note'];
        }

        $preType = $_GET['type'] ?? 'general';
        $preOrderId = intval($_GET['order_id'] ?? 0);

        // Nếu pre-select cancel_order, kiểm tra điều kiện ngay
        $preOrderEligibility = null;
        if ($preType === 'cancel_order' && $preOrderId > 0) {
            $orderCheck = $db->prepare("
                SELECT mo.*, mp.duration_months, mp.name as plan_name 
                FROM membership_orders mo 
                JOIN membership_plans mp ON mo.plan_id = mp.id 
                WHERE mo.id = :id AND mo.user_id = :uid
            ");
            $orderCheck->execute(['id' => $preOrderId, 'uid' => $_SESSION['user_id']]);
            $orderData = $orderCheck->fetch();
            if ($orderData) {
                $preOrderEligibility = self::checkCancelEligibility($orderData, $orderData);
            }
        }

        $this->view('support/create', [
            'title' => 'Gửi yêu cầu hỗ trợ - ' . APP_NAME,
            'pendingOrders' => $pendingOrders,
            'preType' => $preType,
            'preOrderId' => $preOrderId,
            'preOrderEligibility' => $preOrderEligibility,
            'user' => Middleware::user()
        ]);
    }

    /** Lưu ticket (AJAX POST) */
    public function store() {
        if (!$this->isMethod('POST')) return $this->json(['error' => 'Method not allowed'], 405);

        $input = json_decode(file_get_contents('php://input'), true);
        $type = $input['type'] ?? 'general';
        $subject = trim($input['subject'] ?? '');
        $message = trim($input['message'] ?? '');
        $orderId = intval($input['order_id'] ?? 0);

        // Validate
        $allowedTypes = ['general', 'cancel_order', 'bug_report', 'feedback'];
        if (!in_array($type, $allowedTypes)) $type = 'general';
        if (empty($subject)) return $this->json(['error' => 'Tiêu đề không được để trống.'], 400);
        if (strlen($subject) > 200) return $this->json(['error' => 'Tiêu đề tối đa 200 ký tự.'], 400);
        if (empty($message)) return $this->json(['error' => 'Nội dung không được để trống.'], 400);

        $db = getDB();

        // Nếu loại cancel_order → validate điều kiện hủy đơn
        $relatedOrderId = null;
        $refundPercent = 0;
        if ($type === 'cancel_order' && $orderId > 0) {
            // Lấy order + plan info
            $orderCheck = $db->prepare("
                SELECT mo.*, mp.duration_months, mp.name as plan_name 
                FROM membership_orders mo 
                JOIN membership_plans mp ON mo.plan_id = mp.id 
                WHERE mo.id = :id AND mo.user_id = :uid
            ");
            $orderCheck->execute(['id' => $orderId, 'uid' => $_SESSION['user_id']]);
            $orderData = $orderCheck->fetch();

            if (!$orderData) {
                return $this->json(['error' => 'Đơn hàng không tồn tại.'], 400);
            }

            $eligibility = self::checkCancelEligibility($orderData, $orderData);

            if (!$eligibility['can_cancel']) {
                return $this->json([
                    'error' => $eligibility['reason'],
                    'policy_note' => $eligibility['policy_note'] ?? ''
                ], 400);
            }

            $relatedOrderId = $orderId;
            $refundPercent = $eligibility['refund_percent'];

            // Auto append refund info vào message
            $refundNote = "\n\n--- Thông tin đơn ---\n";
            $refundNote .= "Gói: {$orderData['plan_name']}\n";
            $refundNote .= "Số tiền: " . number_format($orderData['amount']) . "đ\n";
            $refundNote .= "Tỷ lệ hoàn tiền: {$refundPercent}%\n";
            $refundNote .= "Số tiền hoàn: " . number_format($orderData['amount'] * $refundPercent / 100) . "đ";
            $message .= $refundNote;
        }

        // Kiểm tra giới hạn: tối đa 5 ticket open cùng lúc
        $openCount = $db->prepare("SELECT COUNT(*) FROM support_tickets WHERE user_id=:uid AND status IN ('open','in_progress')");
        $openCount->execute(['uid' => $_SESSION['user_id']]);
        if ($openCount->fetchColumn() >= 5) {
            return $this->json(['error' => 'Bạn đã có 5 ticket đang mở. Vui lòng đợi xử lý trước khi gửi thêm.'], 400);
        }

        $stmt = $db->prepare("
            INSERT INTO support_tickets (user_id, type, related_order_id, subject, message)
            VALUES (:uid, :type, :oid, :subject, :msg)
        ");
        $stmt->execute([
            'uid' => $_SESSION['user_id'],
            'type' => $type,
            'oid' => $relatedOrderId,
            'subject' => $subject,
            'msg' => $message
        ]);

        $successMsg = 'Ticket đã được gửi thành công! Admin sẽ phản hồi sớm nhất.';
        if ($type === 'cancel_order' && $refundPercent > 0) {
            $successMsg = "Yêu cầu hủy đơn đã gửi! Hoàn tiền {$refundPercent}% sau khi admin duyệt.";
        }

        return $this->json([
            'success' => true,
            'message' => $successMsg
        ]);
    }

    /** Shortcut: Tạo ticket hủy đơn nhanh */
    public function cancelOrder($orderId = null) {
        if (!$orderId) return $this->redirect('support');
        $this->redirect('support/create?type=cancel_order&order_id=' . intval($orderId));
    }

    /** API: Kiểm tra điều kiện hủy (AJAX) */
    public function checkCancel() {
        $orderId = intval($_GET['order_id'] ?? 0);
        if (!$orderId) return $this->json(['error' => 'Thiếu order_id'], 400);

        $db = getDB();
        $orderCheck = $db->prepare("
            SELECT mo.*, mp.duration_months, mp.name as plan_name 
            FROM membership_orders mo 
            JOIN membership_plans mp ON mo.plan_id = mp.id 
            WHERE mo.id = :id AND mo.user_id = :uid
        ");
        $orderCheck->execute(['id' => $orderId, 'uid' => $_SESSION['user_id']]);
        $orderData = $orderCheck->fetch();

        if (!$orderData) return $this->json(['error' => 'Đơn không tồn tại'], 404);

        return $this->json(self::checkCancelEligibility($orderData, $orderData));
    }
}
