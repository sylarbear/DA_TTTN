<?php
/**
 * WalletController
 * Quản lý ví điện tử: nạp tiền, rút tiền, lịch sử giao dịch
 */
class WalletController extends Controller {

    public function __construct() {
        Middleware::requireLogin();
    }

    /** Trang ví: số dư + lịch sử */
    public function index() {
        $db = getDB();
        $user = Middleware::user();

        // Lấy balance mới nhất
        $balanceStmt = $db->prepare("SELECT balance FROM users WHERE id = :id");
        $balanceStmt->execute(['id' => $_SESSION['user_id']]);
        $balance = (int)$balanceStmt->fetchColumn();

        // Lịch sử giao dịch
        $txStmt = $db->prepare("
            SELECT * FROM wallet_transactions 
            WHERE user_id = :uid 
            ORDER BY created_at DESC LIMIT 30
        ");
        $txStmt->execute(['uid' => $_SESSION['user_id']]);
        $transactions = $txStmt->fetchAll();

        // Có giao dịch pending?
        $pendingStmt = $db->prepare("SELECT COUNT(*) FROM wallet_transactions WHERE user_id=:uid AND status='pending'");
        $pendingStmt->execute(['uid' => $_SESSION['user_id']]);
        $hasPending = $pendingStmt->fetchColumn() > 0;

        $this->view('wallet/index', [
            'title' => 'Ví của tôi - ' . APP_NAME,
            'balance' => $balance,
            'transactions' => $transactions,
            'hasPending' => $hasPending,
            'user' => $user
        ]);
    }

    /** Trang nạp tiền */
    public function deposit() {
        $db = getDB();
        $balanceStmt = $db->prepare("SELECT balance FROM users WHERE id = :id");
        $balanceStmt->execute(['id' => $_SESSION['user_id']]);
        $balance = (int)$balanceStmt->fetchColumn();

        // Check pending deposit
        $pending = $db->prepare("SELECT * FROM wallet_transactions WHERE user_id=:uid AND type='deposit' AND status='pending' ORDER BY id DESC LIMIT 1");
        $pending->execute(['uid' => $_SESSION['user_id']]);
        $pendingDeposit = $pending->fetch();

        $this->view('wallet/deposit', [
            'title' => 'Nạp tiền - ' . APP_NAME,
            'balance' => $balance,
            'pendingDeposit' => $pendingDeposit,
            'user' => Middleware::user()
        ]);
    }

    /** Gửi yêu cầu nạp tiền (AJAX) */
    public function createDeposit() {
        if (!$this->isMethod('POST')) return $this->json(['error' => 'Method not allowed'], 405);

        $input = json_decode(file_get_contents('php://input'), true);
        $amount = intval($input['amount'] ?? 0);
        $transferNote = trim($input['transfer_note'] ?? '');

        // Validate
        if ($amount < 10000) return $this->json(['error' => 'Số tiền nạp tối thiểu 10,000đ.'], 400);
        if ($amount > 10000000) return $this->json(['error' => 'Số tiền nạp tối đa 10,000,000đ.'], 400);

        $db = getDB();

        // Check: tối đa 1 pending deposit
        $pending = $db->prepare("SELECT COUNT(*) FROM wallet_transactions WHERE user_id=:uid AND type='deposit' AND status='pending'");
        $pending->execute(['uid' => $_SESSION['user_id']]);
        if ($pending->fetchColumn() > 0) {
            return $this->json(['error' => 'Bạn đã có yêu cầu nạp đang chờ duyệt.'], 400);
        }

        $stmt = $db->prepare("
            INSERT INTO wallet_transactions (user_id, type, amount, description, transfer_note, status)
            VALUES (:uid, 'deposit', :amount, :desc, :note, 'pending')
        ");
        $stmt->execute([
            'uid' => $_SESSION['user_id'],
            'amount' => $amount,
            'desc' => 'Nạp tiền vào ví',
            'note' => $transferNote
        ]);

        return $this->json([
            'success' => true,
            'message' => 'Yêu cầu nạp tiền đã gửi! Admin sẽ duyệt sau khi xác nhận chuyển khoản.'
        ]);
    }

    /** Trang rút tiền */
    public function withdraw() {
        $db = getDB();
        $balanceStmt = $db->prepare("SELECT balance FROM users WHERE id = :id");
        $balanceStmt->execute(['id' => $_SESSION['user_id']]);
        $balance = (int)$balanceStmt->fetchColumn();

        $this->view('wallet/withdraw', [
            'title' => 'Rút tiền - ' . APP_NAME,
            'balance' => $balance,
            'user' => Middleware::user()
        ]);
    }

    /** Gửi yêu cầu rút tiền (AJAX) */
    public function createWithdraw() {
        if (!$this->isMethod('POST')) return $this->json(['error' => 'Method not allowed'], 405);

        $input = json_decode(file_get_contents('php://input'), true);
        $amount = intval($input['amount'] ?? 0);
        $bankName = trim($input['bank_name'] ?? '');
        $bankAccount = trim($input['bank_account'] ?? '');
        $bankHolder = trim($input['bank_holder'] ?? '');

        // Validate
        if ($amount < 50000) return $this->json(['error' => 'Số tiền rút tối thiểu 50,000đ.'], 400);
        if (empty($bankName) || empty($bankAccount) || empty($bankHolder)) {
            return $this->json(['error' => 'Vui lòng điền đầy đủ thông tin ngân hàng.'], 400);
        }

        $db = getDB();

        // Check balance
        $bal = $db->prepare("SELECT balance FROM users WHERE id = :id");
        $bal->execute(['id' => $_SESSION['user_id']]);
        $currentBalance = (int)$bal->fetchColumn();

        if ($amount > $currentBalance) {
            return $this->json(['error' => 'Số dư không đủ. Số dư hiện tại: ' . number_format($currentBalance) . 'đ'], 400);
        }

        // Check pending withdraw
        $pending = $db->prepare("SELECT COUNT(*) FROM wallet_transactions WHERE user_id=:uid AND type='withdraw' AND status='pending'");
        $pending->execute(['uid' => $_SESSION['user_id']]);
        if ($pending->fetchColumn() > 0) {
            return $this->json(['error' => 'Bạn đã có yêu cầu rút đang chờ duyệt.'], 400);
        }

        $stmt = $db->prepare("
            INSERT INTO wallet_transactions (user_id, type, amount, description, bank_name, bank_account, bank_holder, status)
            VALUES (:uid, 'withdraw', :amount, :desc, :bank, :acc, :holder, 'pending')
        ");
        $stmt->execute([
            'uid' => $_SESSION['user_id'],
            'amount' => $amount,
            'desc' => 'Rút tiền về ' . $bankName,
            'bank' => $bankName,
            'acc' => $bankAccount,
            'holder' => $bankHolder
        ]);

        return $this->json([
            'success' => true,
            'message' => 'Yêu cầu rút tiền đã gửi! Admin sẽ xử lý trong 1-3 ngày làm việc.'
        ]);
    }
}
