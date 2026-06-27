<?php
/**
 * AdminController
 * Trang quản trị cho admin: quản lý users, topics, questions, codes
 */
class AdminController extends Controller {

    public function __construct() {
        Middleware::requireAdmin();
    }

    /** Dashboard admin - thống kê tổng quan */
    public function index() {
        $db = getDB();
        $stats = [];
        $stats['total_users'] = $db->query("SELECT COUNT(*) FROM users WHERE role='student'")->fetchColumn();
        $stats['pro_users'] = $db->query("SELECT COUNT(*) FROM users WHERE membership='pro' AND role='student'")->fetchColumn();
        $stats['total_topics'] = $db->query("SELECT COUNT(*) FROM topics")->fetchColumn();
        $stats['total_tests'] = $db->query("SELECT COUNT(*) FROM tests")->fetchColumn();
        $stats['total_questions'] = $db->query("SELECT COUNT(*) FROM questions")->fetchColumn();
        $stats['total_attempts'] = $db->query("SELECT COUNT(*) FROM test_results")->fetchColumn();
        $stats['unused_codes'] = $db->query("SELECT COUNT(*) FROM activation_codes WHERE is_used=0")->fetchColumn();
        $stats['used_codes'] = $db->query("SELECT COUNT(*) FROM activation_codes WHERE is_used=1")->fetchColumn();
        $stats['pending_orders'] = $db->query("SELECT COUNT(*) FROM membership_orders WHERE status='pending'")->fetchColumn();
        $stats['pending_tickets'] = $db->query("SELECT COUNT(*) FROM support_tickets WHERE status IN ('open','in_progress')")->fetchColumn();
        $stats['total_reviews'] = $db->query("SELECT COUNT(*) FROM lesson_reviews")->fetchColumn();

        // Recent users
        $stats['recent_users'] = $db->query("SELECT * FROM users WHERE role='student' ORDER BY created_at DESC LIMIT 5")->fetchAll();
        // Recent attempts
        $stats['recent_attempts'] = $db->query("
            SELECT ta.*, u.username, t.title as test_title,
                   CASE WHEN ta.total_points > 0 THEN ROUND((ta.score/ta.total_points)*100) ELSE 0 END as percentage
            FROM test_results ta 
            JOIN users u ON ta.user_id = u.id 
            JOIN tests t ON ta.test_id = t.id 
            ORDER BY ta.completed_at DESC LIMIT 5
        ")->fetchAll();

        $this->view('admin/dashboard', [
            'title' => 'Admin Dashboard - ' . APP_NAME,
            'stats' => $stats
        ]);
    }

    /** Quản lý Users */
    public function users() {
        $db = getDB();
        $search = $_GET['search'] ?? '';
        if ($search) {
            // Escape LIKE wildcards to prevent unintended pattern matching
            $escapedSearch = str_replace(['%', '_'], ['\%', '\_'], $search);
            $stmt = $db->prepare("SELECT * FROM users WHERE username LIKE :s OR full_name LIKE :s OR email LIKE :s ORDER BY created_at DESC");
            $stmt->execute(['s' => "%$escapedSearch%"]);
        } else {
            $stmt = $db->query("SELECT * FROM users ORDER BY created_at DESC");
        }
        $users = $stmt->fetchAll();

        $this->view('admin/users', [
            'title' => 'Quản lý Users - Admin',
            'users' => $users,
            'search' => $search
        ]);
    }

    /** Sửa user (AJAX) */
    public function updateUser() {
        if (!$this->isMethod('POST')) return $this->json(['error' => 'Method not allowed'], 405);
        $input = json_decode(file_get_contents('php://input'), true);
        
        // Validate inputs
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
        $targetStmt = $db->prepare("SELECT role FROM users WHERE id=:id");
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
            SET full_name=:name,
                email=:email,
                role=:role,
                membership=:mem,
                membership_expired_at=IF(:role_check='admin', NULL, membership_expired_at)
            WHERE id=:id
        ");
        $stmt->execute([
            'name' => trim($input['full_name']),
            'email' => trim($input['email']),
            'role' => $role,
            'role_check' => $role,
            'mem' => $membership,
            'id' => $userId
        ]);
        return $this->json(['success' => true, 'message' => 'Cập nhật thành công']);
    }

    /** Xóa user (AJAX) */
    public function deleteUser() {
        if (!$this->isMethod('POST')) return $this->json(['error' => 'Method not allowed'], 405);
        $input = json_decode(file_get_contents('php://input'), true);
        $userId = intval($input['id'] ?? 0);
        if (!$userId) return $this->json(['error' => 'ID không hợp lệ'], 400);
        if ($userId === $_SESSION['user_id']) return $this->json(['error' => 'Không thể tự xóa tài khoản của mình.'], 400);

        $db = getDB();
        // Xóa user_answers trước (phụ thuộc test_results)
        $db->prepare("DELETE ua FROM user_answers ua INNER JOIN test_results tr ON ua.test_result_id = tr.id WHERE tr.user_id=:id")->execute(['id' => $userId]);
        $db->prepare("DELETE FROM test_results WHERE user_id=:id")->execute(['id' => $userId]);
        $db->prepare("DELETE FROM speaking_attempts WHERE user_id=:id")->execute(['id' => $userId]);
        $db->prepare("DELETE FROM membership_orders WHERE user_id=:id")->execute(['id' => $userId]);
        $db->prepare("DELETE FROM user_progress WHERE user_id=:id")->execute(['id' => $userId]);
        $db->prepare("DELETE FROM bookmarks WHERE user_id=:id")->execute(['id' => $userId]);
        $db->prepare("DELETE FROM xp_history WHERE user_id=:id")->execute(['id' => $userId]);
        $db->prepare("DELETE FROM users WHERE id=:id AND role != 'admin'")->execute(['id' => $userId]);
        return $this->json(['success' => true, 'message' => 'Đã xóa user']);
    }

    /** Quản lý Topics */
    public function topics() {
        $db = getDB();
        $topics = $db->query("
            SELECT t.*, 
                (SELECT COUNT(*) FROM lessons WHERE topic_id=t.id) as lesson_count,
                (SELECT COUNT(*) FROM vocabularies WHERE topic_id=t.id) as vocab_count,
                (SELECT COUNT(*) FROM tests WHERE topic_id=t.id) as test_count
            FROM topics t ORDER BY t.id
        ")->fetchAll();

        $this->view('admin/topics', [
            'title' => 'Quản lý Chủ đề - Admin',
            'topics' => $topics
        ]);
    }

    /** Thêm/sửa topic (AJAX) */
    public function saveTopic() {
        if (!$this->isMethod('POST')) return $this->json(['error' => 'Method not allowed'], 405);
        $input = json_decode(file_get_contents('php://input'), true);
        $db = getDB();

        $name = trim($input['name'] ?? '');
        $description = trim($input['description'] ?? '');
        $level = $input['level'] ?? 'beginner';
        $allowedLevels = ['beginner', 'intermediate', 'advanced'];
        if (!in_array($level, $allowedLevels)) $level = 'beginner';

        if (empty($name)) {
            return $this->json(['error' => 'Tên chủ đề không được để trống.'], 400);
        }

        if (!empty($input['id'])) {
            $stmt = $db->prepare("UPDATE topics SET name=:name, description=:desc, level=:level WHERE id=:id");
            $stmt->execute(['name'=>$name, 'desc'=>$description, 'level'=>$level, 'id'=>intval($input['id'])]);
            return $this->json(['success' => true, 'message' => 'Cập nhật chủ đề thành công']);
        } else {
            $slug = strtolower(preg_replace('/[^a-z0-9]+/', '-', strtolower($name)));
            $stmt = $db->prepare("INSERT INTO topics (name, slug, description, level) VALUES (:name, :slug, :desc, :level)");
            $stmt->execute(['name'=>$name, 'slug'=>$slug, 'desc'=>$description, 'level'=>$level]);
            return $this->json(['success' => true, 'message' => 'Thêm chủ đề thành công', 'id' => $db->lastInsertId()]);
        }
    }

    /** Quản lý Questions theo Test */
    public function questions() {
        $db = getDB();
        $testId = $_GET['test_id'] ?? null;
        $tests = $db->query("SELECT t.*, tp.name as topic_name FROM tests t JOIN topics tp ON t.topic_id=tp.id ORDER BY t.id")->fetchAll();
        $questions = [];
        $currentTest = null;

        if ($testId) {
            $stmt = $db->prepare("SELECT * FROM questions WHERE test_id=:tid ORDER BY id");
            $stmt->execute(['tid' => $testId]);
            $questions = $stmt->fetchAll();
            foreach ($tests as $t) { if ($t['id'] == $testId) { $currentTest = $t; break; } }
        }

        $this->view('admin/questions', [
            'title' => 'Quản lý Câu hỏi - Admin',
            'tests' => $tests,
            'questions' => $questions,
            'currentTest' => $currentTest,
            'testId' => $testId
        ]);
    }

    /** Thêm/sửa question (AJAX) */
    public function saveQuestion() {
        if (!$this->isMethod('POST')) return $this->json(['error' => 'Method not allowed'], 405);
        $input = json_decode(file_get_contents('php://input'), true);
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
            'D' => $input['option_d'] ?? ''
        ], JSON_UNESCAPED_UNICODE);
        $passage = $input['passage'] ?? null;

        if (!empty($input['id'])) {
            $stmt = $db->prepare("UPDATE questions SET question_text=:q, options_json=:o, correct_answer=:a, passage=:p WHERE id=:id");
            $stmt->execute(['q'=>$questionText, 'o'=>$options, 'a'=>$correctAnswer, 'p'=>$passage, 'id'=>intval($input['id'])]);
            return $this->json(['success' => true, 'message' => 'Cập nhật câu hỏi thành công']);
        } else {
            $testId = intval($input['test_id'] ?? 0);
            if (!$testId) return $this->json(['error' => 'Test ID không hợp lệ.'], 400);
            $stmt = $db->prepare("INSERT INTO questions (test_id, question_text, options_json, correct_answer, passage) VALUES (:tid, :q, :o, :a, :p)");
            $stmt->execute(['tid'=>$testId, 'q'=>$questionText, 'o'=>$options, 'a'=>$correctAnswer, 'p'=>$passage]);
            return $this->json(['success' => true, 'message' => 'Thêm câu hỏi thành công', 'id' => $db->lastInsertId()]);
        }
    }

    /** Xóa question (AJAX) */
    public function deleteQuestion() {
        if (!$this->isMethod('POST')) return $this->json(['error' => 'Method not allowed'], 405);
        $input = json_decode(file_get_contents('php://input'), true);
        $questionId = intval($input['id'] ?? 0);
        if (!$questionId) return $this->json(['error' => 'ID không hợp lệ'], 400);
        $db = getDB();
        $db->prepare("DELETE FROM user_answers WHERE question_id=:id")->execute(['id' => $questionId]);
        $db->prepare("DELETE FROM questions WHERE id=:id")->execute(['id' => $questionId]);
        return $this->json(['success' => true, 'message' => 'Đã xóa câu hỏi']);
    }

    /** Quản lý mã kích hoạt */
    public function codes() {
        $db = getDB();
        $codes = $db->query("
            SELECT ac.*, mp.name as plan_name, u.username as used_by_name 
            FROM activation_codes ac 
            JOIN membership_plans mp ON ac.plan_id = mp.id
            LEFT JOIN users u ON ac.used_by = u.id
            ORDER BY ac.is_used ASC, ac.created_at DESC
        ")->fetchAll();
        $plans = $db->query("SELECT * FROM membership_plans ORDER BY duration_months")->fetchAll();

        $this->view('admin/codes', [
            'title' => 'Quản lý Mã kích hoạt - Admin',
            'codes' => $codes,
            'plans' => $plans
        ]);
    }

    /** Tạo mã kích hoạt (AJAX) */
    public function createCode() {
        if (!$this->isMethod('POST')) return $this->json(['error' => 'Method not allowed'], 405);
        $input = json_decode(file_get_contents('php://input'), true);
        $db = getDB();
        $code = strtoupper(trim($input['code'] ?? ''));
        $planId = intval($input['plan_id'] ?? 0);

        if (empty($code)) return $this->json(['error' => 'Mã không được để trống'], 400);
        if (!$planId) return $this->json(['error' => 'Vui lòng chọn gói'], 400);
        if (!preg_match('/^[A-Z0-9\-]{3,30}$/', $code)) {
            return $this->json(['error' => 'Mã chỉ chứa chữ, số và dấu gạch ngang (3-30 ký tự)'], 400);
        }
        
        // Check trùng
        $exists = $db->prepare("SELECT COUNT(*) FROM activation_codes WHERE code=:c");
        $exists->execute(['c' => $code]);
        if ($exists->fetchColumn() > 0) {
            return $this->json(['error' => 'Mã đã tồn tại'], 400);
        }

        $stmt = $db->prepare("INSERT INTO activation_codes (code, plan_id) VALUES (:code, :plan)");
        $stmt->execute(['code' => $code, 'plan' => $planId]);
        return $this->json(['success' => true, 'message' => 'Tạo mã thành công']);
    }

    /** Xóa code */
    public function deleteCode() {
        if (!$this->isMethod('POST')) return $this->json(['error' => 'Method not allowed'], 405);
        $input = json_decode(file_get_contents('php://input'), true);
        $codeId = intval($input['id'] ?? 0);
        if (!$codeId) return $this->json(['error' => 'ID không hợp lệ'], 400);
        $db = getDB();
        $db->prepare("DELETE FROM activation_codes WHERE id=:id AND is_used=0")->execute(['id' => $codeId]);
        return $this->json(['success' => true, 'message' => 'Đã xóa mã']);
    }

    /** Trang cài đặt hệ thống */
    public function settings() {
        require_once APP_PATH . '/core/OpenAIService.php';
        $currentKey = OpenAIService::getApiKey();
        $maskedKey = $currentKey ? (substr($currentKey, 0, 8) . '****' . substr($currentKey, -4)) : '';

        $this->view('admin/settings', [
            'title' => 'Cài đặt hệ thống - Admin',
            'hasKey' => !empty($currentKey),
            'maskedKey' => $maskedKey
        ]);
    }

    /** Lưu API key (AJAX) */
    public function saveSettings() {
        if (!$this->isMethod('POST')) return $this->json(['error' => 'Method not allowed'], 405);
        $input = json_decode(file_get_contents('php://input'), true);
        
        require_once APP_PATH . '/core/OpenAIService.php';
        
        $apiKey = trim($input['openai_key'] ?? '');
        
        // Validate API key format
        if (!empty($apiKey) && !preg_match('/^sk-[a-zA-Z0-9_-]{20,}$/', $apiKey)) {
            return $this->json(['error' => 'API key không hợp lệ. Key phải bắt đầu bằng "sk-".'], 400);
        }
        
        OpenAIService::saveApiKey($apiKey);
        
        return $this->json([
            'success' => true,
            'message' => $apiKey ? 'Đã lưu API key thành công. AI Speaking + Chatbot đã được kích hoạt!' : 'Đã xóa API key. AI features bị vô hiệu hóa.',
            'hasKey' => !empty($apiKey)
        ]);
    }

    /** Danh sách đơn chờ duyệt */
    public function orders() {
        $db = getDB();
        $orders = $db->query("
            SELECT mo.*, u.username, u.full_name, u.email, mp.name as plan_name, mp.duration_months, mp.price
            FROM membership_orders mo
            JOIN users u ON mo.user_id = u.id
            JOIN membership_plans mp ON mo.plan_id = mp.id
            ORDER BY mo.status ASC, mo.activated_at DESC
        ")->fetchAll();

        $this->view('admin/orders', [
            'title'  => 'Quản lý Đơn nâng cấp - Admin',
            'orders' => $orders
        ]);
    }

    /** Duyệt đơn chuyển khoản (AJAX) */
    public function approveOrder() {
        if (!$this->isMethod('POST')) return $this->json(['error' => 'Method not allowed'], 405);
        $input = json_decode(file_get_contents('php://input'), true);
        $orderId = intval($input['id'] ?? 0);

        $db = getDB();

        // Lấy đơn
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

        // Tính ngày hết hạn
        $userStmt = $db->prepare("SELECT membership_expired_at FROM users WHERE id = :id");
        $userStmt->execute(['id' => $order['user_id']]);
        $user = $userStmt->fetch();

        $now = new DateTime();
        $currentExpiry = $user['membership_expired_at'] ?? null;
        if ($currentExpiry && strtotime($currentExpiry) > time()) {
            $baseDate = new DateTime($currentExpiry);
        } else {
            $baseDate = $now;
        }
        $expiredAt = $baseDate->modify('+' . $order['duration_months'] . ' months')->format('Y-m-d H:i:s');

        try {
            $db->beginTransaction();

            // Cập nhật order
            $stmt = $db->prepare("UPDATE membership_orders SET status = 'completed', expired_at = :expired WHERE id = :id");
            $stmt->execute(['expired' => $expiredAt, 'id' => $orderId]);

            // Cập nhật user thành Pro
            $stmt = $db->prepare("UPDATE users SET membership = 'pro', membership_expired_at = :expired WHERE id = :uid");
            $stmt->execute(['expired' => $expiredAt, 'uid' => $order['user_id']]);

            $db->commit();

            return $this->json([
                'success' => true,
                'message' => 'Đã duyệt đơn! User đã được nâng cấp Pro đến ' . date('d/m/Y', strtotime($expiredAt))
            ]);
        } catch (Exception $e) {
            $db->rollBack();
            return $this->json(['error' => 'Có lỗi xảy ra khi duyệt đơn. Vui lòng thử lại.'], 500);
        }
    }

    /** Từ chối đơn (AJAX) */
    public function rejectOrder() {
        if (!$this->isMethod('POST')) return $this->json(['error' => 'Method not allowed'], 405);
        $input = json_decode(file_get_contents('php://input'), true);
        $orderId = intval($input['id'] ?? 0);

        $db = getDB();
        $stmt = $db->prepare("UPDATE membership_orders SET status = 'cancelled' WHERE id = :id AND status = 'pending'");
        $stmt->execute(['id' => $orderId]);

        return $this->json(['success' => true, 'message' => 'Đã từ chối đơn.']);
    }

    /** Quản lý Tickets */
    public function tickets() {
        $db = getDB();
        $tickets = $db->query("
            SELECT st.*, u.username, u.full_name, u.email,
                   mp.name as plan_name, mo.amount as order_amount, mo.status as order_status
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
            'openCount' => $openCount
        ]);
    }

    /** Phản hồi ticket (AJAX) */
    public function replyTicket() {
        if (!$this->isMethod('POST')) return $this->json(['error' => 'Method not allowed'], 405);
        $input = json_decode(file_get_contents('php://input'), true);
        $ticketId = intval($input['ticket_id'] ?? 0);
        $reply = trim($input['reply'] ?? '');

        if (!$ticketId || empty($reply)) return $this->json(['error' => 'Dữ liệu không hợp lệ'], 400);

        $db = getDB();
        $stmt = $db->prepare("UPDATE support_tickets SET admin_reply=:reply, replied_at=NOW(), status='resolved' WHERE id=:id");
        $stmt->execute(['reply' => $reply, 'id' => $ticketId]);

        return $this->json(['success' => true]);
    }

    /** Đổi trạng thái ticket (AJAX) */
    public function updateTicketStatus() {
        if (!$this->isMethod('POST')) return $this->json(['error' => 'Method not allowed'], 405);
        $input = json_decode(file_get_contents('php://input'), true);
        $ticketId = intval($input['ticket_id'] ?? 0);
        $status = $input['status'] ?? '';

        $allowed = ['open', 'in_progress', 'resolved', 'closed'];
        if (!$ticketId || !in_array($status, $allowed)) return $this->json(['error' => 'Dữ liệu không hợp lệ'], 400);

        $db = getDB();
        $db->prepare("UPDATE support_tickets SET status=:s WHERE id=:id")->execute(['s' => $status, 'id' => $ticketId]);
        return $this->json(['success' => true]);
    }

    /** Duyệt hủy đơn từ ticket (AJAX) */
    public function approveCancelOrder() {
        if (!$this->isMethod('POST')) return $this->json(['error' => 'Method not allowed'], 405);
        $input = json_decode(file_get_contents('php://input'), true);
        $ticketId = intval($input['ticket_id'] ?? 0);
        $orderId = intval($input['order_id'] ?? 0);

        if (!$ticketId || !$orderId) return $this->json(['error' => 'Dữ liệu không hợp lệ'], 400);

        $db = getDB();

        // Lấy order info
        $order = $db->prepare("SELECT mo.*, mp.duration_months FROM membership_orders mo JOIN membership_plans mp ON mo.plan_id=mp.id WHERE mo.id=:id");
        $order->execute(['id' => $orderId]);
        $orderData = $order->fetch();

        if (!$orderData) return $this->json(['error' => 'Đơn không tồn tại'], 400);

        try {
            $db->beginTransaction();

            // Tính refund
            require_once APP_PATH . '/controllers/SupportController.php';
            $eligibility = SupportController::checkCancelEligibility($orderData, $orderData);
            $refundPercent = $eligibility['can_cancel'] ? $eligibility['refund_percent'] : 100; // Admin override
            $refundAmount = intval($orderData['amount'] * $refundPercent / 100);

            // 1. Hủy đơn
            $db->prepare("UPDATE membership_orders SET status='cancelled' WHERE id=:id")->execute(['id' => $orderId]);

            // 2. Hoàn tiền vào ví
            if ($refundAmount > 0) {
                $userId = $orderData['user_id'];
                $bal = $db->prepare("SELECT balance FROM users WHERE id=:id FOR UPDATE");
                $bal->execute(['id' => $userId]);
                $currentBalance = (int)$bal->fetchColumn();
                $newBalance = $currentBalance + $refundAmount;

                $db->prepare("UPDATE users SET balance=:bal WHERE id=:id")->execute(['bal' => $newBalance, 'id' => $userId]);

                // Ghi wallet transaction
                $db->prepare("
                    INSERT INTO wallet_transactions (user_id, type, amount, balance_after, status, description, processed_at)
                    VALUES (:uid, 'refund', :amount, :bal, 'completed', :desc, NOW())
                ")->execute([
                    'uid' => $userId,
                    'amount' => $refundAmount,
                    'bal' => $newBalance,
                    'desc' => 'Hoàn tiền hủy đơn #' . $orderId . ' (' . $refundPercent . '%)'
                ]);
            }

            // 3. Đánh dấu ticket resolved
            $refundMsg = $refundAmount > 0 ? ' Hoàn ' . number_format($refundAmount) . 'đ (' . $refundPercent . '%) vào ví.' : '';
            $db->prepare("UPDATE support_tickets SET status='resolved', admin_reply=CONCAT(COALESCE(admin_reply,''), :msg), replied_at=NOW() WHERE id=:id")
               ->execute(['msg' => "\nĐã duyệt hủy đơn #" . $orderId . "." . $refundMsg, 'id' => $ticketId]);

            $db->commit();
            return $this->json(['success' => true, 'message' => 'Đã hủy đơn #' . $orderId . '.' . $refundMsg]);
        } catch (Exception $e) {
            $db->rollBack();
            return $this->json(['error' => 'Lỗi: ' . $e->getMessage()], 500);
        }
    }

    /** Chart data API (AJAX) */
    public function chartData() {
        $db = getDB();

        // 1. Tăng trưởng user 7 ngày gần nhất
        $userGrowth = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $label = date('d/m', strtotime("-$i days"));
            $count = $db->prepare("SELECT COUNT(*) FROM users WHERE DATE(created_at) = :d");
            $count->execute(['d' => $date]);
            $userGrowth[] = ['label' => $label, 'count' => (int)$count->fetchColumn()];
        }

        // 2. Phân bố điểm test
        $scoreDistribution = $db->query("
            SELECT
                SUM(CASE WHEN p BETWEEN 0 AND 20 THEN 1 ELSE 0 END) as 'r_0_20',
                SUM(CASE WHEN p BETWEEN 21 AND 40 THEN 1 ELSE 0 END) as 'r_21_40',
                SUM(CASE WHEN p BETWEEN 41 AND 60 THEN 1 ELSE 0 END) as 'r_41_60',
                SUM(CASE WHEN p BETWEEN 61 AND 80 THEN 1 ELSE 0 END) as 'r_61_80',
                SUM(CASE WHEN p BETWEEN 81 AND 100 THEN 1 ELSE 0 END) as 'r_81_100'
            FROM (
                SELECT CASE WHEN total_points > 0 THEN ROUND((score/total_points)*100) ELSE 0 END as p
                FROM test_results
            ) t
        ")->fetch();

        // 3. Đơn nâng cấp 6 tháng gần nhất
        $ordersByMonth = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-$i months"));
            $label = date('m/Y', strtotime("-$i months"));
            $count = $db->prepare("SELECT COUNT(*) FROM membership_orders WHERE DATE_FORMAT(activated_at, '%Y-%m') = :m");
            $count->execute(['m' => $month]);
            $ordersByMonth[] = ['label' => $label, 'count' => (int)$count->fetchColumn()];
        }

        // 4. Tỷ lệ Free vs Pro
        $freeCount = (int)$db->query("SELECT COUNT(*) FROM users WHERE membership='free' AND role='student'")->fetchColumn();
        $proCount = (int)$db->query("SELECT COUNT(*) FROM users WHERE membership='pro' AND role='student'")->fetchColumn();

        return $this->json([
            'userGrowth' => $userGrowth,
            'scoreDistribution' => $scoreDistribution,
            'ordersByMonth' => $ordersByMonth,
            'membershipRatio' => ['free' => $freeCount, 'pro' => $proCount]
        ]);
    }

    /** Quản lý giao dịch ví */
    public function walletTransactions() {
        $db = getDB();
        $transactions = $db->query("
            SELECT wt.*, u.username, u.full_name, u.balance
            FROM wallet_transactions wt
            JOIN users u ON wt.user_id = u.id
            ORDER BY FIELD(wt.status, 'pending', 'completed', 'rejected'), wt.created_at DESC
            LIMIT 50
        ")->fetchAll();

        $pendingCount = $db->query("SELECT COUNT(*) FROM wallet_transactions WHERE status='pending'")->fetchColumn();

        $this->view('admin/wallet', [
            'title' => 'Quản lý Ví - Admin',
            'transactions' => $transactions,
            'pendingCount' => $pendingCount
        ]);
    }

    public function wallet() {
        return $this->walletTransactions();
    }

    /** Duyệt giao dịch ví (nạp/rút) */
    public function approveTransaction() {
        if (!$this->isMethod('POST')) return $this->json(['error' => 'Method not allowed'], 405);
        $input = json_decode(file_get_contents('php://input'), true);
        $txId = intval($input['id'] ?? 0);
        if (!$txId) return $this->json(['error' => 'ID không hợp lệ'], 400);

        $db = getDB();
        $tx = $db->prepare("SELECT * FROM wallet_transactions WHERE id=:id AND status='pending'");
        $tx->execute(['id' => $txId]);
        $transaction = $tx->fetch();
        if (!$transaction) return $this->json(['error' => 'Giao dịch không tồn tại hoặc đã xử lý.'], 400);

        try {
            $db->beginTransaction();

            $userId = $transaction['user_id'];
            $amount = (int)$transaction['amount'];

            // Lấy balance hiện tại
            $bal = $db->prepare("SELECT balance FROM users WHERE id=:id FOR UPDATE");
            $bal->execute(['id' => $userId]);
            $currentBalance = (int)$bal->fetchColumn();

            if ($transaction['type'] === 'deposit') {
                // Nạp tiền → cộng balance
                $newBalance = $currentBalance + $amount;
            } elseif ($transaction['type'] === 'withdraw') {
                // Rút tiền → trừ balance
                if ($currentBalance < $amount) {
                    $db->rollBack();
                    return $this->json(['error' => 'Số dư user không đủ để rút.'], 400);
                }
                $newBalance = $currentBalance - $amount;
            } else {
                $db->rollBack();
                return $this->json(['error' => 'Loại giao dịch không hợp lệ.'], 400);
            }

            // Cập nhật balance
            $db->prepare("UPDATE users SET balance=:bal WHERE id=:id")->execute(['bal' => $newBalance, 'id' => $userId]);
            
            // Cập nhật transaction
            $db->prepare("UPDATE wallet_transactions SET status='completed', balance_after=:bal, processed_at=NOW() WHERE id=:id")
               ->execute(['bal' => $newBalance, 'id' => $txId]);

            $db->commit();
            return $this->json(['success' => true, 'message' => 'Duyệt thành công! Balance mới: ' . number_format($newBalance) . 'đ']);
        } catch (Exception $e) {
            $db->rollBack();
            return $this->json(['error' => 'Lỗi: ' . $e->getMessage()], 500);
        }
    }

    /** Từ chối giao dịch ví */
    public function rejectTransaction() {
        if (!$this->isMethod('POST')) return $this->json(['error' => 'Method not allowed'], 405);
        $input = json_decode(file_get_contents('php://input'), true);
        $txId = intval($input['id'] ?? 0);
        $note = trim($input['note'] ?? 'Từ chối bởi Admin');

        $db = getDB();
        $db->prepare("UPDATE wallet_transactions SET status='rejected', admin_note=:note, processed_at=NOW() WHERE id=:id AND status='pending'")
           ->execute(['note' => $note, 'id' => $txId]);
        return $this->json(['success' => true]);
    }
}
