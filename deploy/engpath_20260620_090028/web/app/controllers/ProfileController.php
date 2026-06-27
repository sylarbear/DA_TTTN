<?php
/**
 * ProfileController 
 * Trang hồ sơ cá nhân
 */
class ProfileController extends Controller {

    public function __construct() {
        Middleware::requireLogin();
    }

    /** Xem profile */
    public function index() {
        $db = getDB();
        $userId = $_SESSION['user_id'];
        
        // User info
        $stmt = $db->prepare("SELECT * FROM users WHERE id=:id");
        $stmt->execute(['id' => $userId]);
        $user = $stmt->fetch();

        // Stats
        $stats = [];
        $stats['total_tests'] = $db->prepare("SELECT COUNT(*) FROM test_results WHERE user_id=:id");
        $stats['total_tests']->execute(['id' => $userId]);
        $stats['total_tests'] = $stats['total_tests']->fetchColumn();

        $stats['avg_score'] = $db->prepare("SELECT ROUND(AVG(CASE WHEN total_points > 0 THEN (score/total_points)*100 ELSE 0 END), 1) FROM test_results WHERE user_id=:id");
        $stats['avg_score']->execute(['id' => $userId]);
        $stats['avg_score'] = $stats['avg_score']->fetchColumn() ?: 0;

        $stats['speaking_attempts'] = $db->prepare("SELECT COUNT(*) FROM speaking_attempts WHERE user_id=:id");
        $stats['speaking_attempts']->execute(['id' => $userId]);
        $stats['speaking_attempts'] = $stats['speaking_attempts']->fetchColumn();

        $stats['topics_studied'] = $db->prepare("SELECT COUNT(DISTINCT topic_id) FROM user_progress WHERE user_id=:id");
        $stats['topics_studied']->execute(['id' => $userId]);
        $stats['topics_studied'] = $stats['topics_studied']->fetchColumn();

        // Badges
        $badges = $this->getUserBadges($userId, $stats);

        // Recent activity
        $recent = $db->prepare("
            SELECT ta.score, ta.total_points, ta.completed_at, t.title, t.test_type,
                   CASE WHEN ta.total_points > 0 THEN ROUND((ta.score/ta.total_points)*100) ELSE 0 END as percentage
            FROM test_results ta JOIN tests t ON ta.test_id=t.id
            WHERE ta.user_id=:id ORDER BY ta.completed_at DESC LIMIT 10
        ");
        $recent->execute(['id' => $userId]);
        $recentTests = $recent->fetchAll();

        $this->view('profile/index', [
            'title' => 'Hồ sơ cá nhân - ' . APP_NAME,
            'user' => $user,
            'stats' => $stats,
            'badges' => $badges,
            'recentTests' => $recentTests
        ]);
    }

    /** Update profile (AJAX) */
    public function update() {
        if (!$this->isMethod('POST')) return $this->json(['error' => 'Method not allowed'], 405);
        $input = json_decode(file_get_contents('php://input'), true);
        
        $fullName = trim($input['full_name'] ?? '');
        $email = trim($input['email'] ?? '');

        if (empty($fullName)) {
            return $this->json(['error' => 'Họ tên không được để trống'], 400);
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->json(['error' => 'Email không hợp lệ'], 400);
        }

        $db = getDB();

        // Kiểm tra email đã được dùng bởi user khác chưa
        $emailCheck = $db->prepare("SELECT id FROM users WHERE email=:email AND id!=:id");
        $emailCheck->execute(['email' => $email, 'id' => $_SESSION['user_id']]);
        if ($emailCheck->fetch()) {
            return $this->json(['error' => 'Email này đã được sử dụng bởi tài khoản khác'], 400);
        }
        
        $stmt = $db->prepare("UPDATE users SET full_name=:name, email=:email WHERE id=:id");
        $stmt->execute([
            'name' => $fullName,
            'email' => $email,
            'id' => $_SESSION['user_id']
        ]);
        
        $_SESSION['full_name'] = $fullName;
        $_SESSION['email'] = $email;
        
        return $this->json(['success' => true, 'message' => 'Cập nhật thành công']);
    }

    /** Change password (AJAX) */
    public function changePassword() {
        if (!$this->isMethod('POST')) return $this->json(['error' => 'Method not allowed'], 405);
        $input = json_decode(file_get_contents('php://input'), true);
        $db = getDB();
        
        $stmt = $db->prepare("SELECT password_hash FROM users WHERE id=:id");
        $stmt->execute(['id' => $_SESSION['user_id']]);
        $user = $stmt->fetch();
        // Google OAuth users may not have a password set
        $hasPassword = !empty($user['password_hash']);
        
        if ($hasPassword && !password_verify($input['current_password'], $user['password_hash'])) {
            return $this->json(['error' => 'Mật khẩu hiện tại không đúng'], 400);
        }
        if (!$hasPassword && !empty($input['current_password'])) {
            return $this->json(['error' => 'Tài khoản Google chưa có mật khẩu. Để trống mật khẩu hiện tại để đặt mật khẩu mới.'], 400);
        }

        if (strlen($input['new_password'] ?? '') < 6) {
            return $this->json(['error' => 'Mật khẩu mới phải có ít nhất 6 ký tự'], 400);
        }
        
        $hash = password_hash($input['new_password'], PASSWORD_DEFAULT);
        $db->prepare("UPDATE users SET password_hash=:pw WHERE id=:id")->execute(['pw' => $hash, 'id' => $_SESSION['user_id']]);
        
        return $this->json(['success' => true, 'message' => 'Đổi mật khẩu thành công']);
    }

    /** Calculate badges */
    private function getUserBadges($userId, $stats) {
        $allBadges = [
            ['icon'=>'🌱','name'=>'Người mới','desc'=>'Hoàn thành bài test đầu tiên', 'earned' => $stats['total_tests'] >= 1],
            ['icon'=>'📚','name'=>'Bookworm','desc'=>'Học 3 chủ đề', 'earned' => $stats['topics_studied'] >= 3],
            ['icon'=>'🎯','name'=>'Perfect Score','desc'=>'Đạt 100% bài test', 'earned' => false],
            ['icon'=>'🗣️','name'=>'Speaker','desc'=>'Luyện nói 10 lần', 'earned' => $stats['speaking_attempts'] >= 10],
            ['icon'=>'⭐','name'=>'Star Student','desc'=>'Làm 10 bài test', 'earned' => $stats['total_tests'] >= 10],
            ['icon'=>'🏆','name'=>'Champion','desc'=>'Điểm TB ≥ 80', 'earned' => $stats['avg_score'] >= 80],
            ['icon'=>'💎','name'=>'Pro Member','desc'=>'Nâng cấp Pro', 'earned' => Middleware::isPro()],
            ['icon'=>'🔥','name'=>'Dedicated','desc'=>'Làm 30 bài test', 'earned' => $stats['total_tests'] >= 30],
        ];

        // Check perfect score
        $db = getDB();
        $perfect = $db->prepare("SELECT COUNT(*) FROM test_results WHERE user_id=:id AND score = total_points AND total_points > 0");
        $perfect->execute(['id' => $userId]);
        $allBadges[2]['earned'] = $perfect->fetchColumn() > 0;

        return $allBadges;
    }
}
