<?php

/**
 * AdminController
 * Quản trị: users, topics, questions, orders, tickets, settings, dashboard
 */
class AdminController extends Controller
{
    public function __construct()
    {
        Middleware::requireAdmin();
    }

    /** Dashboard admin - thống kê tổng quan */
    public function index()
    {
        $db = getDB();
        $stats = [];
        $stats['total_users'] = $db->query("SELECT COUNT(*) FROM users WHERE role='student'")->fetchColumn();
        $stats['pro_users'] = $db->query("SELECT COUNT(*) FROM users WHERE membership='pro' AND role='student'")->fetchColumn();
        $stats['total_topics'] = $db->query('SELECT COUNT(*) FROM topics')->fetchColumn();
        $stats['total_tests'] = $db->query('SELECT COUNT(*) FROM tests')->fetchColumn();
        $stats['total_questions'] = $db->query('SELECT COUNT(*) FROM questions')->fetchColumn();
        $stats['total_attempts'] = $db->query('SELECT COUNT(*) FROM test_results')->fetchColumn();
        $stats['pending_orders'] = $db->query("SELECT COUNT(*) FROM membership_orders WHERE status='pending'")->fetchColumn();
        $stats['pending_tickets'] = $db->query("SELECT COUNT(*) FROM support_tickets WHERE status IN ('open','in_progress')")->fetchColumn();

        // Recent users
        $stats['recent_users'] = $db->query("SELECT * FROM users WHERE role='student' ORDER BY created_at DESC LIMIT 5")->fetchAll();
        // Recent attempts
        $stats['recent_attempts'] = $db->query('
            SELECT ta.*, u.username, t.title as test_title,
                   CASE WHEN ta.total_points > 0 THEN ROUND((ta.score/ta.total_points)*100) ELSE 0 END as percentage
            FROM test_results ta
            JOIN users u ON ta.user_id = u.id
            JOIN tests t ON ta.test_id = t.id
            ORDER BY ta.completed_at DESC LIMIT 5
        ')->fetchAll();

        $this->view('admin/dashboard', [
            'title' => 'Admin Dashboard - ' . APP_NAME,
            'stats' => $stats,
        ]);
    }

    // ─── Users ───────────────────────────────────

    public function users()
    {
        $db = getDB();
        $search = $_GET['search'] ?? '';
        if ($search) {
            $escapedSearch = str_replace(['%', '_'], ['\%', '\_'], $search);
            $stmt = $db->prepare('SELECT * FROM users WHERE username LIKE :s OR full_name LIKE :s OR email LIKE :s ORDER BY created_at DESC');
            $stmt->execute(['s' => "%$escapedSearch%"]);
        } else {
            $stmt = $db->query('SELECT * FROM users ORDER BY created_at DESC');
        }
        $users = $stmt->fetchAll();

        $this->view('admin/users', [
            'title' => 'Quản lý Users - Admin',
            'users' => $users,
            'search' => $search,
        ]);
    }

    public function updateUser()
    {
        if (!$this->isMethod('POST')) {
            return $this->json(['error' => 'Method not allowed'], 405);
        }
        $input = Request::json();

        $allowedRoles = ['student', 'admin'];
        $allowedMemberships = ['free', 'pro'];
        $role = in_array($input['role'] ?? '', $allowedRoles) ? $input['role'] : 'student';
        $membership = in_array($input['membership'] ?? '', $allowedMemberships) ? $input['membership'] : 'free';

        if (empty($input['full_name']) || empty($input['email'])) {
            return $this->json(['error' => 'Tên và email không được để trống'], 400);
        }
        if (!filter_var(trim($input['email']), FILTER_VALIDATE_EMAIL)) {
            return $this->json(['error' => 'Email không hợp lệ'], 400);
        }

        $db = getDB();
        $userId = intval($input['id'] ?? 0);
        $targetStmt = $db->prepare('SELECT role FROM users WHERE id=:id');
        $targetStmt->execute(['id' => $userId]);
        $targetRole = $targetStmt->fetchColumn();

        if (!$targetRole) {
            return $this->json(['error' => 'User không tồn tại'], 404);
        }

        if ($targetRole === 'admin' || $role === 'admin') {
            $role = 'admin';
            $membership = 'free';
        }

        $stmt = $db->prepare("
            UPDATE users
            SET full_name=:name, email=:email, role=:role, membership=:mem,
                membership_expired_at=IF(:role_check='admin', NULL, membership_expired_at)
            WHERE id=:id
        ");
        $stmt->execute([
            'name' => trim($input['full_name']),
            'email' => trim($input['email']),
            'role' => $role,
            'role_check' => $role,
            'mem' => $membership,
            'id' => $userId,
        ]);

        return $this->json(['success' => true, 'message' => 'Cập nhật thành công']);
    }

    public function deleteUser()
    {
        if (!$this->isMethod('POST')) {
            return $this->json(['error' => 'Method not allowed'], 405);
        }
        $input = Request::json();
        $userId = intval($input['id'] ?? 0);
        if (!$userId) {
            return $this->json(['error' => 'ID không hợp lệ'], 400);
        }
        if ($userId === $_SESSION['user_id']) {
            return $this->json(['error' => 'Không thể tự xóa tài khoản của mình.'], 400);
        }

        $db = getDB();
        $db->prepare('DELETE ua FROM user_answers ua INNER JOIN test_results tr ON ua.test_result_id = tr.id WHERE tr.user_id=:id')->execute(['id' => $userId]);
        $db->prepare('DELETE FROM test_results WHERE user_id=:id')->execute(['id' => $userId]);
        $db->prepare('DELETE FROM membership_orders WHERE user_id=:id')->execute(['id' => $userId]);
        $db->prepare('DELETE FROM user_progress WHERE user_id=:id')->execute(['id' => $userId]);
        $db->prepare('DELETE FROM xp_history WHERE user_id=:id')->execute(['id' => $userId]);
        $db->prepare("DELETE FROM users WHERE id=:id AND role != 'admin'")->execute(['id' => $userId]);

        return $this->json(['success' => true, 'message' => 'Đã xóa user']);
    }

    // ─── Topics ──────────────────────────────────

    public function topics()
    {
        $db = getDB();
        $topics = $db->query('
            SELECT t.*,
                (SELECT COUNT(*) FROM lessons WHERE topic_id=t.id) as lesson_count,
                (SELECT COUNT(*) FROM vocabularies WHERE topic_id=t.id) as vocab_count,
                (SELECT COUNT(*) FROM tests WHERE topic_id=t.id) as test_count
            FROM topics t ORDER BY t.id
        ')->fetchAll();

        $this->view('admin/topics', [
            'title' => 'Quản lý Chủ đề - Admin',
            'topics' => $topics,
        ]);
    }

    public function saveTopic()
    {
        if (!$this->isMethod('POST')) {
            return $this->json(['error' => 'Method not allowed'], 405);
        }
        $input = Request::json();
        $db = getDB();

        $name = trim($input['name'] ?? '');
        $description = trim($input['description'] ?? '');
        $level = $input['level'] ?? 'beginner';
        if (!in_array($level, ['beginner', 'intermediate', 'advanced'])) {
            $level = 'beginner';
        }

        if (empty($name)) {
            return $this->json(['error' => 'Tên chủ đề không được để trống.'], 400);
        }

        if (!empty($input['id'])) {
            $stmt = $db->prepare('UPDATE topics SET name=:name, description=:desc, level=:level WHERE id=:id');
            $stmt->execute(['name' => $name, 'desc' => $description, 'level' => $level, 'id' => intval($input['id'])]);

            return $this->json(['success' => true, 'message' => 'Cập nhật chủ đề thành công']);
        }

        $slug = strtolower(preg_replace('/[^a-z0-9]+/', '-', strtolower($name)));
        $stmt = $db->prepare('INSERT INTO topics (name, slug, description, level) VALUES (:name, :slug, :desc, :level)');
        $stmt->execute(['name' => $name, 'slug' => $slug, 'desc' => $description, 'level' => $level]);

        return $this->json(['success' => true, 'message' => 'Thêm chủ đề thành công', 'id' => $db->lastInsertId()]);
    }

    // ─── Questions ───────────────────────────────

    public function questions()
    {
        $db = getDB();
        $testId = $_GET['test_id'] ?? null;
        $tests = $db->query('SELECT t.*, tp.name as topic_name FROM tests t JOIN topics tp ON t.topic_id=tp.id ORDER BY t.id')->fetchAll();
        $questions = [];
        $currentTest = null;

        if ($testId) {
            $stmt = $db->prepare('SELECT * FROM questions WHERE test_id=:tid ORDER BY id');
            $stmt->execute(['tid' => $testId]);
            $questions = $stmt->fetchAll();
            foreach ($tests as $t) {
                if ($t['id'] == $testId) { $currentTest = $t; break; }
            }
        }

        $this->view('admin/questions', [
            'title' => 'Quản lý Câu hỏi - Admin',
            'tests' => $tests,
            'questions' => $questions,
            'currentTest' => $currentTest,
            'testId' => $testId,
        ]);
    }

    public function saveQuestion()
    {
        if (!$this->isMethod('POST')) {
            return $this->json(['error' => 'Method not allowed'], 405);
        }
        $input = Request::json();
        $db = getDB();

        $questionText = trim($input['question_text'] ?? '');
        if (empty($questionText)) {
            return $this->json(['error' => 'Câu hỏi không được để trống.'], 400);
        }

        $correctAnswer = $input['correct_answer'] ?? 'A';
        if (!in_array($correctAnswer, ['A', 'B', 'C', 'D'])) {
            return $this->json(['error' => 'Đáp án phải là A, B, C hoặc D.'], 400);
        }

        $options = json_encode([
            'A' => $input['option_a'] ?? '',
            'B' => $input['option_b'] ?? '',
            'C' => $input['option_c'] ?? '',
            'D' => $input['option_d'] ?? '',
        ], JSON_UNESCAPED_UNICODE);
        $passage = $input['passage'] ?? null;

        if (!empty($input['id'])) {
            $stmt = $db->prepare('UPDATE questions SET question_text=:q, options_json=:o, correct_answer=:a, passage=:p WHERE id=:id');
            $stmt->execute(['q' => $questionText, 'o' => $options, 'a' => $correctAnswer, 'p' => $passage, 'id' => intval($input['id'])]);

            return $this->json(['success' => true, 'message' => 'Cập nhật câu hỏi thành công']);
        }

        $testId = intval($input['test_id'] ?? 0);
        if (!$testId) {
            return $this->json(['error' => 'Test ID không hợp lệ.'], 400);
        }
        $stmt = $db->prepare('INSERT INTO questions (test_id, question_text, options_json, correct_answer, passage) VALUES (:tid, :q, :o, :a, :p)');
        $stmt->execute(['tid' => $testId, 'q' => $questionText, 'o' => $options, 'a' => $correctAnswer, 'p' => $passage]);

        return $this->json(['success' => true, 'message' => 'Thêm câu hỏi thành công', 'id' => $db->lastInsertId()]);
    }

    public function deleteQuestion()
    {
        if (!$this->isMethod('POST')) {
            return $this->json(['error' => 'Method not allowed'], 405);
        }
        $input = Request::json();
        $questionId = intval($input['id'] ?? 0);
        if (!$questionId) {
            return $this->json(['error' => 'ID không hợp lệ'], 400);
        }
        $db = getDB();
        $db->prepare('DELETE FROM user_answers WHERE question_id=:id')->execute(['id' => $questionId]);
        $db->prepare('DELETE FROM questions WHERE id=:id')->execute(['id' => $questionId]);

        return $this->json(['success' => true, 'message' => 'Đã xóa câu hỏi']);
    }

    // ─── Settings ────────────────────────────────

    public function settings()
    {
        require_once APP_PATH . '/core/OpenAIService.php';
        $currentKey = OpenAIService::getApiKey();
        $maskedKey = $currentKey ? (substr($currentKey, 0, 8) . '****' . substr($currentKey, -4)) : '';

        $this->view('admin/settings', [
            'title' => 'Cài đặt hệ thống - Admin',
            'hasKey' => !empty($currentKey),
            'maskedKey' => $maskedKey,
        ]);
    }

    public function saveSettings()
    {
        if (!$this->isMethod('POST')) {
            return $this->json(['error' => 'Method not allowed'], 405);
        }
        $input = Request::json();
        require_once APP_PATH . '/core/OpenAIService.php';

        $apiKey = trim($input['openai_key'] ?? '');
        if (!empty($apiKey) && !preg_match('/^sk-[a-zA-Z0-9_-]{20,}$/', $apiKey)) {
            return $this->json(['error' => 'API key không hợp lệ. Key phải bắt đầu bằng "sk-".'], 400);
        }

        OpenAIService::saveApiKey($apiKey);

        return $this->json([
            'success' => true,
            'message' => $apiKey ? 'Đã lưu API key thành công. AI Speaking + Chatbot đã được kích hoạt!' : 'Đã xóa API key. AI features bị vô hiệu hóa.',
            'hasKey' => !empty($apiKey),
        ]);
    }

    // ─── Orders ──────────────────────────────────

    public function orders()
    {
        $db = getDB();
        $orders = $db->query("
            SELECT mo.*, u.username, u.full_name, u.email, mp.name as plan_name, mp.duration_months, mp.price
            FROM membership_orders mo
            JOIN users u ON mo.user_id = u.id
            JOIN membership_plans mp ON mo.plan_id = mp.id
            ORDER BY FIELD(mo.status, 'pending', 'completed', 'cancelled'), mo.activated_at DESC
        ")->fetchAll();

        $this->view('admin/orders', [
            'title' => 'Quản lý Đơn nâng cấp - Admin',
            'orders' => $orders,
        ]);
    }

    public function approveOrder()
    {
        if (!$this->isMethod('POST')) {
            return $this->json(['error' => 'Method not allowed'], 405);
        }
        $input = Request::json();
        $orderId = intval($input['id'] ?? 0);

        $db = getDB();
        $stmt = $db->prepare("
            SELECT mo.*, mp.duration_months
            FROM membership_orders mo
            JOIN membership_plans mp ON mo.plan_id = mp.id
            WHERE mo.id = :id AND mo.status = 'pending'
        ");
        $stmt->execute(['id' => $orderId]);
        $order = $stmt->fetch();

        if (!$order) {
            return $this->json(['error' => 'Đơn không tồn tại hoặc đã được xử lý.'], 400);
        }

        $userStmt = $db->prepare('SELECT membership_expired_at FROM users WHERE id = :id');
        $userStmt->execute(['id' => $order['user_id']]);
        $user = $userStmt->fetch();

        $now = new DateTime();
        $currentExpiry = $user['membership_expired_at'] ?? null;

        // Lifetime plan (duration_months = -1) → không giới hạn thời gian
        if ((int)$order['duration_months'] === -1) {
            $expiredAt = null;
        } else {
            $baseDate = ($currentExpiry && strtotime($currentExpiry) > time()) ? new DateTime($currentExpiry) : $now;
            $expiredAt = $baseDate->modify('+' . $order['duration_months'] . ' months')->format('Y-m-d H:i:s');
        }

        try {
            $db->beginTransaction();

            $db->prepare("UPDATE membership_orders SET status = 'completed', expired_at = :expired WHERE id = :id")
               ->execute(['expired' => $expiredAt, 'id' => $orderId]);

            $db->prepare("UPDATE users SET membership = 'pro', membership_expired_at = :expired WHERE id = :uid")
               ->execute(['expired' => $expiredAt, 'uid' => $order['user_id']]);

            $db->commit();

            $expiryMsg = $expiredAt ? ' đến ' . date('d/m/Y', strtotime($expiredAt)) : ' (trọn đời)';
            return $this->json([
                'success' => true,
                'message' => 'Đã duyệt đơn! User được nâng cấp Pro' . $expiryMsg,
            ]);
        } catch (Exception $e) {
            $db->rollBack();

            return $this->json(['error' => 'Có lỗi xảy ra. Vui lòng thử lại.'], 500);
        }
    }

    public function rejectOrder()
    {
        if (!$this->isMethod('POST')) {
            return $this->json(['error' => 'Method not allowed'], 405);
        }
        $input = Request::json();
        $orderId = intval($input['id'] ?? 0);

        $db = getDB();
        $db->prepare("UPDATE membership_orders SET status = 'cancelled' WHERE id = :id AND status = 'pending'")
           ->execute(['id' => $orderId]);

        return $this->json(['success' => true, 'message' => 'Đã từ chối đơn.']);
    }

    // ─── Tickets ─────────────────────────────────

    public function tickets()
    {
        $db = getDB();
        $tickets = $db->query("
            SELECT st.*, u.username, u.full_name, u.email,
                   mp.name as plan_name, mo.amount as order_amount
            FROM support_tickets st
            JOIN users u ON st.user_id = u.id
            LEFT JOIN membership_orders mo ON st.related_order_id = mo.id
            LEFT JOIN membership_plans mp ON mo.plan_id = mp.id
            ORDER BY FIELD(st.status, 'open', 'in_progress', 'resolved', 'closed'), st.created_at DESC
        ")->fetchAll();

        $openCount = $db->query("SELECT COUNT(*) FROM support_tickets WHERE status IN ('open','in_progress')")->fetchColumn();

        $this->view('admin/tickets', [
            'title' => 'Quản lý Tickets - Admin',
            'tickets' => $tickets,
            'openCount' => $openCount,
        ]);
    }

    public function replyTicket()
    {
        if (!$this->isMethod('POST')) {
            return $this->json(['error' => 'Method not allowed'], 405);
        }
        $input = Request::json();
        $ticketId = intval($input['ticket_id'] ?? 0);
        $reply = trim($input['reply'] ?? '');

        if (!$ticketId || empty($reply)) {
            return $this->json(['error' => 'Dữ liệu không hợp lệ'], 400);
        }

        $db = getDB();
        $db->prepare("UPDATE support_tickets SET admin_reply=:reply, replied_at=NOW(), status='resolved' WHERE id=:id")
           ->execute(['reply' => $reply, 'id' => $ticketId]);

        return $this->json(['success' => true]);
    }

    public function updateTicketStatus()
    {
        if (!$this->isMethod('POST')) {
            return $this->json(['error' => 'Method not allowed'], 405);
        }
        $input = Request::json();
        $ticketId = intval($input['ticket_id'] ?? 0);
        $status = $input['status'] ?? '';

        if (!$ticketId || !in_array($status, ['open', 'in_progress', 'resolved', 'closed'])) {
            return $this->json(['error' => 'Dữ liệu không hợp lệ'], 400);
        }

        $db = getDB();
        $db->prepare('UPDATE support_tickets SET status=:s WHERE id=:id')
           ->execute(['s' => $status, 'id' => $ticketId]);

        return $this->json(['success' => true]);
    }

    /** Duyệt hủy đơn từ ticket (đơn giản — chỉ hủy, không hoàn tiền ví) */
    public function approveCancelOrder()
    {
        if (!$this->isMethod('POST')) {
            return $this->json(['error' => 'Method not allowed'], 405);
        }
        $input = Request::json();
        $ticketId = intval($input['ticket_id'] ?? 0);
        $orderId = intval($input['order_id'] ?? 0);

        if (!$ticketId || !$orderId) {
            return $this->json(['error' => 'Dữ liệu không hợp lệ'], 400);
        }

        $db = getDB();
        $order = $db->prepare('SELECT * FROM membership_orders WHERE id=:id');
        $order->execute(['id' => $orderId]);
        if (!$order->fetch()) {
            return $this->json(['error' => 'Đơn không tồn tại'], 400);
        }

        try {
            $db->beginTransaction();

            $db->prepare("UPDATE membership_orders SET status='cancelled' WHERE id=:id")->execute(['id' => $orderId]);
            $db->prepare("UPDATE support_tickets SET status='resolved', admin_reply=CONCAT(COALESCE(admin_reply,''), :msg), replied_at=NOW() WHERE id=:id")
               ->execute(['msg' => "\nĐã duyệt hủy đơn #" . $orderId . '.', 'id' => $ticketId]);

            $db->commit();

            return $this->json(['success' => true, 'message' => 'Đã hủy đơn #' . $orderId . '.']);
        } catch (Exception $e) {
            $db->rollBack();

            return $this->json(['error' => 'Lỗi: ' . $e->getMessage()], 500);
        }
    }

    // ─── Chart data ──────────────────────────────

    public function chartData()
    {
        $db = getDB();

        $userGrowth = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $label = date('d/m', strtotime("-$i days"));
            $count = $db->prepare('SELECT COUNT(*) FROM users WHERE DATE(created_at) = :d');
            $count->execute(['d' => $date]);
            $userGrowth[] = ['label' => $label, 'count' => (int)$count->fetchColumn()];
        }

        $scoreDistribution = $db->query("
            SELECT
                SUM(CASE WHEN p BETWEEN 0 AND 20 THEN 1 ELSE 0 END) as 'r_0_20',
                SUM(CASE WHEN p BETWEEN 21 AND 40 THEN 1 ELSE 0 END) as 'r_21_40',
                SUM(CASE WHEN p BETWEEN 41 AND 60 THEN 1 ELSE 0 END) as 'r_41_60',
                SUM(CASE WHEN p BETWEEN 61 AND 80 THEN 1 ELSE 0 END) as 'r_61_80',
                SUM(CASE WHEN p BETWEEN 81 AND 100 THEN 1 ELSE 0 END) as 'r_81_100'
            FROM (SELECT CASE WHEN total_points > 0 THEN ROUND((score/total_points)*100) ELSE 0 END as p FROM test_results) t
        ")->fetch();

        $ordersByMonth = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-$i months"));
            $label = date('m/Y', strtotime("-$i months"));
            $count = $db->prepare("SELECT COUNT(*) FROM membership_orders WHERE DATE_FORMAT(activated_at, '%Y-%m') = :m");
            $count->execute(['m' => $month]);
            $ordersByMonth[] = ['label' => $label, 'count' => (int)$count->fetchColumn()];
        }

        $freeCount = (int)$db->query("SELECT COUNT(*) FROM users WHERE membership='free' AND role='student'")->fetchColumn();
        $proCount = (int)$db->query("SELECT COUNT(*) FROM users WHERE membership='pro' AND role='student'")->fetchColumn();

        return $this->json([
            'userGrowth' => $userGrowth,
            'scoreDistribution' => $scoreDistribution,
            'ordersByMonth' => $ordersByMonth,
            'membershipRatio' => ['free' => $freeCount, 'pro' => $proCount],
        ]);
    }
}
