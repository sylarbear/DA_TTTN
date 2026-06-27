<?php
/**
 * WebhookController
 * Nhận webhook từ Casso để tự động xác nhận thanh toán
 * 
 * Endpoint: POST /webhook/casso
 * Casso sẽ gọi endpoint này mỗi khi có giao dịch mới vào tài khoản
 */
class WebhookController extends Controller {

    /**
     * Nhận webhook từ Casso
     * Không cần login - đây là API endpoint cho Casso gọi
     */
    public function casso() {
        // Chỉ chấp nhận POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->json(['error' => 'Method not allowed'], 405);
        }

        // Lấy raw body
        $rawBody = file_get_contents('php://input');
        $data = json_decode($rawBody, true);

        // Log webhook để debug
        $this->logWebhook('casso_incoming', $rawBody);

        // Verify webhook secret (nếu đã cấu hình)
        if (!empty(CASSO_WEBHOOK_SECRET)) {
            $signature = $_SERVER['HTTP_SECURE_TOKEN'] ?? $_SERVER['HTTP_X_CASSO_SIGNATURE'] ?? '';
            if ($signature !== CASSO_WEBHOOK_SECRET) {
                $this->logWebhook('casso_invalid_signature', $signature);
                return $this->json(['error' => 'Invalid signature'], 403);
            }
        }

        // Validate data
        if (!$data || !isset($data['data']) || !is_array($data['data'])) {
            return $this->json(['error' => 'Invalid data'], 400);
        }

        $processed = 0;
        $errors = [];

        // Xử lý từng giao dịch
        foreach ($data['data'] as $transaction) {
            try {
                $result = $this->processTransaction($transaction);
                if ($result) $processed++;
            } catch (Exception $e) {
                $errors[] = $e->getMessage();
                $this->logWebhook('casso_error', $e->getMessage());
            }
        }

        return $this->json([
            'success' => true,
            'processed' => $processed,
            'errors' => $errors
        ]);
    }

    /**
     * Xử lý 1 giao dịch từ Casso
     * @param array $transaction Dữ liệu giao dịch
     * @return bool Đã xử lý thành công hay không
     */
    private function processTransaction($transaction) {
        $amount = intval($transaction['amount'] ?? 0);
        $description = trim($transaction['description'] ?? '');
        $tid = $transaction['tid'] ?? '';

        // Chỉ xử lý giao dịch tiền VÀO (amount > 0)
        if ($amount <= 0) {
            return false;
        }

        // Parse nội dung chuyển khoản để tìm thông tin
        // Format: EMPRO {userId} GOI{planId}
        // Lưu ý: Ngân hàng có thể thêm text trước/sau, nên dùng regex
        $userId = null;
        $planId = null;

        // Tìm pattern EMPRO trong nội dung CK
        if (preg_match('/EMPRO\s+(\d+)\s+GOI(\d+)/i', $description, $matches)) {
            $userId = intval($matches[1]);
            $planId = intval($matches[2]);
        }

        if (!$userId || !$planId) {
            $this->logWebhook('casso_no_match', "Could not parse: '$description'");
            return false;
        }

        $db = getDB();

        // Kiểm tra giao dịch đã xử lý chưa (tránh trùng lặp)
        $stmt = $db->prepare("SELECT COUNT(*) FROM membership_orders WHERE transfer_note = :tid AND status = 'completed' AND payment_method = 'casso_auto'");
        $stmt->execute(['tid' => 'TID:' . $tid]);
        if ($stmt->fetchColumn() > 0) {
            $this->logWebhook('casso_duplicate', "Transaction already processed: $tid");
            return false;
        }

        // Kiểm tra plan tồn tại
        $stmt = $db->prepare("SELECT * FROM membership_plans WHERE id = :id");
        $stmt->execute(['id' => $planId]);
        $plan = $stmt->fetch();

        if (!$plan) {
            $this->logWebhook('casso_invalid_plan', "Plan $planId not found");
            return false;
        }

        // Kiểm tra số tiền khớp (cho phép sai lệch ±1000đ do phí chuyển)
        if (abs($amount - $plan['price']) > 1000) {
            $this->logWebhook('casso_amount_mismatch', "Expected {$plan['price']}, got $amount for plan $planId");
            return false;
        }

        // Kiểm tra user tồn tại
        $stmt = $db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $userId]);
        $user = $stmt->fetch();

        if (!$user) {
            $this->logWebhook('casso_user_not_found', "User $userId not found");
            return false;
        }

        // Tính ngày hết hạn
        $now = new DateTime();
        $currentExpiry = $user['membership_expired_at'] ?? null;
        if ($currentExpiry && strtotime($currentExpiry) > time()) {
            $baseDate = new DateTime($currentExpiry);
        } else {
            $baseDate = $now;
        }
        $expiredAt = $baseDate->modify('+' . $plan['duration_months'] . ' months')->format('Y-m-d H:i:s');

        try {
            $db->beginTransaction();

            // Tìm và cập nhật đơn pending (nếu có)
            $stmt = $db->prepare("SELECT id FROM membership_orders WHERE user_id = :uid AND plan_id = :pid AND status = 'pending' LIMIT 1");
            $stmt->execute(['uid' => $userId, 'pid' => $planId]);
            $pendingOrder = $stmt->fetch();

            if ($pendingOrder) {
                // Cập nhật đơn pending thành completed
                $stmt = $db->prepare("UPDATE membership_orders SET status = 'completed', payment_method = 'casso_auto', transfer_note = :note, expired_at = :expired WHERE id = :id");
                $stmt->execute([
                    'note' => 'TID:' . $tid . ' | ' . $description,
                    'expired' => $expiredAt,
                    'id' => $pendingOrder['id']
                ]);
            } else {
                // Tạo đơn mới (user chuyển tiền mà chưa nhấn "Đã chuyển khoản")
                $stmt = $db->prepare("
                    INSERT INTO membership_orders (user_id, plan_id, transfer_note, amount, payment_method, status, expired_at) 
                    VALUES (:uid, :pid, :note, :amount, 'casso_auto', 'completed', :expired)
                ");
                $stmt->execute([
                    'uid' => $userId,
                    'pid' => $planId,
                    'note' => 'TID:' . $tid . ' | ' . $description,
                    'amount' => $amount,
                    'expired' => $expiredAt
                ]);
            }

            // Cập nhật user thành Pro
            $stmt = $db->prepare("UPDATE users SET membership = 'pro', membership_expired_at = :expired WHERE id = :id");
            $stmt->execute(['expired' => $expiredAt, 'id' => $userId]);

            $db->commit();

            $this->logWebhook('casso_success', "User $userId upgraded to Pro (Plan $planId) until $expiredAt. TID: $tid");
            return true;

        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    /**
     * Endpoint test: kiểm tra webhook endpoint hoạt động
     * GET /webhook/test
     */
    public function test() {
        return $this->json([
            'status' => 'ok',
            'message' => 'Webhook endpoint is active',
            'timestamp' => date('Y-m-d H:i:s'),
            'casso_configured' => !empty(CASSO_API_KEY)
        ]);
    }

    /**
     * Simulate webhook (chỉ cho admin test)
     * POST /webhook/simulate
     */
    public function simulate() {
        // Chỉ admin mới được simulate
        if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
            return $this->json(['error' => 'Admin only'], 403);
        }

        if (!$this->isMethod('POST')) {
            return $this->json(['error' => 'Method not allowed'], 405);
        }

        $input = json_decode(file_get_contents('php://input'), true);

        // Tạo dữ liệu giả lập giống Casso
        $description = preg_replace('/[^A-Za-z0-9\s]/', '', $input['description'] ?? 'EMPRO 1 GOI1');
        $amount = max(0, intval($input['amount'] ?? 50000));

        if ($amount <= 0) {
            return $this->json(['error' => 'Số tiền phải lớn hơn 0'], 400);
        }

        $fakeWebhook = [
            'error' => 0,
            'data' => [[
                'id' => rand(100000, 999999),
                'tid' => 'FT' . date('YmdHis') . rand(100, 999),
                'description' => $description,
                'amount' => $amount,
                'cusum_balance' => 1000000,
                'when' => date('Y-m-d H:i:s'),
                'bank_sub_acc_id' => BANK_ACCOUNT_NO
            ]]
        ];

        // Xử lý như webhook thật
        $processed = 0;
        $errors = [];

        foreach ($fakeWebhook['data'] as $transaction) {
            try {
                $result = $this->processTransaction($transaction);
                if ($result) $processed++;
            } catch (Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        return $this->json([
            'success' => $processed > 0,
            'processed' => $processed,
            'errors' => $errors,
            'simulated_data' => $fakeWebhook
        ]);
    }

    /**
     * Ghi log webhook để debug
     */
    private function logWebhook($type, $data) {
        $logDir = APP_PATH . '/../storage/logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        $logFile = $logDir . '/webhook_' . date('Y-m-d') . '.log';
        $entry = '[' . date('Y-m-d H:i:s') . '] [' . $type . '] ' . (is_string($data) ? $data : json_encode($data, JSON_UNESCAPED_UNICODE)) . PHP_EOL;
        file_put_contents($logFile, $entry, FILE_APPEND);
    }
}
