<?php


/**
 * DashboardController
 * Hiển thị tiến độ học tập
 */
class DashboardController extends Controller
{
    /**
     * Trang dashboard
     */
    public function index()
    {
        Middleware::requireLogin();

        // Update streak + login bonus
        require_once APP_PATH . '/core/StreakService.php';
        StreakService::updateStreak($_SESSION['user_id']);
        $streakStats = StreakService::getUserStats($_SESSION['user_id']);

        $progressModel = $this->model('UserProgress');
        $dashboardData = $progressModel->getDashboardData($_SESSION['user_id']);

        // Lấy kết quả placement (nếu có)
        $placementModel = $this->model('Placement');
        $placement = $placementModel->getLastResult($_SESSION['user_id']);

        // Lấy placement_level từ DB (không có trong session)
        $userData = Middleware::user();
        $stmt = getDB()->prepare('SELECT placement_level FROM users WHERE id = :id');
        $stmt->execute(['id' => $_SESSION['user_id']]);
        $dbUser = $stmt->fetch();
        $userData['placement_level'] = $dbUser['placement_level'] ?? null;

        $this->view('dashboard/index', [
            'title' => 'Dashboard - ' . APP_NAME,
            'data' => $dashboardData,
            'streak' => $streakStats,
            'placement' => $placement,
            'user' => $userData,
        ]);
    }

    /**
     * API lấy dữ liệu chart (AJAX)
     */
    public function chartData()
    {
        Middleware::requireLogin();

        $progressModel = $this->model('UserProgress');
        $data = $progressModel->getDashboardData($_SESSION['user_id']);

        // Chuẩn bị dữ liệu cho Chart.js
        $chartData = [
            'topics' => [],
            'scores' => [],
            'vocab' => [],
            'tests' => [],
            'speaking' => [],
        ];

        foreach ($data['topic_progress'] as $tp) {
            $chartData['topics'][] = $tp['topic_name'];
            $chartData['scores'][] = $tp['total_score'];
            $chartData['vocab'][] = $tp['vocab_learned'];
            $chartData['tests'][] = $tp['tests_passed'];
            $chartData['speaking'][] = $tp['speaking_practiced'];
        }

        return $this->json($chartData);
    }
}
