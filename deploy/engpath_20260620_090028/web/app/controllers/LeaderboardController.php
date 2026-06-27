<?php
/**
 * LeaderboardController
 * Bảng xếp hạng học viên
 */
class LeaderboardController extends Controller {

    public function __construct() {
        Middleware::requireLogin();
    }

    public function index() {
        $db = getDB();
        
        // Top users by total score and tests completed
        $leaders = $db->query("
            SELECT u.id, u.username, u.full_name, u.avatar, u.membership,
                COUNT(ta.id) as total_tests,
                ROUND(AVG(CASE WHEN ta.total_points > 0 THEN (ta.score/ta.total_points)*100 ELSE 0 END), 1) as avg_score,
                SUM(ta.score) as total_score,
                MAX(ta.score) as best_score
            FROM users u
            LEFT JOIN test_results ta ON u.id = ta.user_id
            WHERE u.role = 'student'
            GROUP BY u.id
            HAVING total_tests > 0
            ORDER BY total_score DESC, avg_score DESC
            LIMIT 20
        ")->fetchAll();

        // Current user rank
        $myRank = null;
        foreach ($leaders as $i => $l) {
            if ($l['id'] == $_SESSION['user_id']) {
                $myRank = $i + 1;
                break;
            }
        }

        $this->view('leaderboard/index', [
            'title' => 'Bảng xếp hạng - ' . APP_NAME,
            'leaders' => $leaders,
            'myRank' => $myRank,
            'user' => Middleware::user()
        ]);
    }
}
