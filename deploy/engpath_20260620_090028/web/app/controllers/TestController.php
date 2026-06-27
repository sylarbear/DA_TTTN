<?php
/**
 * TestController
 * Làm bài kiểm tra (quiz, listening, reading)
 */
class TestController extends Controller {

    /**
     * Danh sách bài test
     */
    public function index() {
        $testModel = $this->model('Test');
        $tests = $testModel->getAllWithTopic();

        $this->view('tests/index', [
            'title' => 'Bài kiểm tra - ' . APP_NAME,
            'tests' => $tests,
            'user'  => Middleware::user()
        ]);
    }

    /**
     * Bắt đầu làm bài test
     * @param int $id Test ID
     */
    public function take($id = null) {
        Middleware::requireLogin();
        if (!$id) return $this->redirect('test');

        $testModel = $this->model('Test');
        $test = $testModel->getWithQuestions($id);
        if (!$test) return $this->redirect('test');

        // Listening và Reading chỉ dành cho Pro
        if (in_array($test['test_type'], ['listening', 'reading']) && !Middleware::isPro()) {
            $_SESSION['flash'] = [
                'type' => 'warning',
                'message' => 'Bài test ' . ucfirst($test['test_type']) . ' là tính năng Pro. Vui lòng nâng cấp để làm bài.'
            ];
            return $this->redirect('membership');
        }

        $topicModel = $this->model('Topic');
        $topic = $topicModel->find($test['topic_id']);

        $this->view('tests/take', [
            'title' => $test['title'] . ' - ' . APP_NAME,
            'test'  => $test,
            'topic' => $topic,
            'user'  => Middleware::user()
        ]);
    }

    /**
     * Nộp bài test (AJAX)
     */
    public function submit() {
        Middleware::requireLogin();

        if (!$this->isMethod('POST')) {
            return $this->json(['error' => 'Method not allowed'], 405);
        }

        // Lấy dữ liệu từ AJAX
        $input = json_decode(file_get_contents('php://input'), true);
        $testId   = intval($input['test_id'] ?? 0);
        $answers  = $input['answers'] ?? [];
        $timeSpent = intval($input['time_spent'] ?? 0);

        if (!$testId || empty($answers)) {
            return $this->json(['error' => 'Dữ liệu không hợp lệ'], 400);
        }

        $userAnswerModel = $this->model('UserAnswer');
        $result = $userAnswerModel->submitTest($_SESSION['user_id'], $testId, $answers, $timeSpent);

        // Cập nhật progress
        $testModel = $this->model('Test');
        $test = $testModel->find($testId);
        if ($test) {
            $progressModel = $this->model('UserProgress');
            if ($result['percentage'] >= $test['pass_score']) {
                // Chỉ increment tests_passed nếu user chưa pass test này trước đó
                $prevPassed = getDB()->prepare("
                    SELECT COUNT(*) FROM test_results 
                    WHERE user_id=:uid AND test_id=:tid AND id != :rid
                    AND (score / NULLIF(total_points,0) * 100) >= :pass
                ");
                $prevPassed->execute([
                    'uid' => $_SESSION['user_id'], 
                    'tid' => $testId, 
                    'rid' => $result['result_id'],
                    'pass' => $test['pass_score']
                ]);
                if ($prevPassed->fetchColumn() == 0) {
                    $progressModel->increment($_SESSION['user_id'], $test['topic_id'], 'tests_passed');
                }
            }
            $progressModel->addScore($_SESSION['user_id'], $test['topic_id'], $result['score']);
        }

        return $this->json([
            'success'    => true,
            'result_id'  => $result['result_id'],
            'score'      => $result['score'],
            'total'      => $result['total_points'],
            'percentage' => $result['percentage'],
            'passed'     => $result['percentage'] >= ($test['pass_score'] ?? 60)
        ]);
    }

    /**
     * Xem kết quả test
     * @param int $resultId
     */
    public function result($resultId = null) {
        Middleware::requireLogin();
        if (!$resultId) return $this->redirect('test');

        // Lấy test result
        $stmt = getDB()->prepare("
            SELECT tr.*, t.title as test_title, t.test_type, t.pass_score,
                   tp.name as topic_name, tp.id as topic_id
            FROM test_results tr
            JOIN tests t ON tr.test_id = t.id
            JOIN topics tp ON t.topic_id = tp.id
            WHERE tr.id = :id AND tr.user_id = :user_id
        ");
        $stmt->execute(['id' => $resultId, 'user_id' => $_SESSION['user_id']]);
        $result = $stmt->fetch();

        if (!$result) return $this->redirect('test');

        // Lấy chi tiết từng câu
        $userAnswerModel = $this->model('UserAnswer');
        $details = $userAnswerModel->getResultDetails($resultId);

        $this->view('tests/result', [
            'title'   => 'Kết quả: ' . $result['test_title'] . ' - ' . APP_NAME,
            'result'  => $result,
            'details' => $details,
            'user'    => Middleware::user()
        ]);
    }
}
