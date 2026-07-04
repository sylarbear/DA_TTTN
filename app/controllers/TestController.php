<?php


/**
 * TestController
 * Làm bài kiểm tra (quiz, listening, reading)
 */
class TestController extends Controller
{
    /**
     * Danh sách bài test
     */
    public function index()
    {
        $testModel = $this->model('Test');
        $tests = $testModel->getAllWithTopic();

        $this->view('tests/index', [
            'title' => 'Bài kiểm tra - ' . APP_NAME,
            'tests' => $tests,
            'user' => Middleware::user(),
        ]);
    }

    /**
     * Bắt đầu làm bài test
     * @param int $id Test ID
     */
    public function take($id = null)
    {
        Middleware::requireLogin();
        if (!$id) {
            return $this->redirect('test');
        }

        $testModel = $this->model('Test');
        $test = $testModel->getWithQuestions($id);
        if (!$test) {
            return $this->redirect('test');
        }

        // Listening và Reading chỉ dành cho Pro
        if (in_array($test['test_type'], ['listening', 'reading']) && !Middleware::isPro()) {
            $_SESSION['flash'] = [
                'type' => 'warning',
                'message' => 'Bài test ' . ucfirst($test['test_type']) . ' là tính năng Pro. Vui lòng nâng cấp để làm bài.',
            ];

            return $this->redirect('membership');
        }

        $topicModel = $this->model('Topic');
        $topic = $topicModel->find($test['topic_id']);

        $this->view('tests/take', [
            'title' => $test['title'] . ' - ' . APP_NAME,
            'test' => $test,
            'topic' => $topic,
            'user' => Middleware::user(),
        ]);
    }

    /**
     * Nộp bài test (AJAX)
     */
    public function submit()
    {
        Middleware::requireLogin();

        if (!$this->isMethod('POST')) {
            return $this->json(['error' => 'Method not allowed'], 405);
        }

        // Lấy dữ liệu từ AJAX
        $input = Request::json();
        $testId = intval($input['test_id'] ?? 0);
        $answers = $input['answers'] ?? [];
        $timeSpent = intval($input['time_spent'] ?? 0);

        if (!$testId || empty($answers)) {
            return $this->json(['error' => 'Dữ liệu không hợp lệ'], 400);
        }

        $userAnswerModel = $this->model('UserAnswer');
        $result = $userAnswerModel->submitTest($_SESSION['user_id'], $testId, $answers, $timeSpent);

        // Cập nhật progress
        $testModel = $this->model('Test');
        $test = $testModel->find($testId);
        $testPassScore = ($test && isset($test['pass_score'])) ? $test['pass_score'] : 60;
        $testIsFinal   = $test ? !empty($test['is_final']) : false;
        $testTitle     = $test ? ($test['title'] ?? '') : '';
        $testTopicId   = $test ? ($test['topic_id'] ?? null) : null;
        if ($test) {
            $progressModel = $this->model('UserProgress');
            if ($result['percentage'] >= $testPassScore) {
                // Chỉ increment tests_passed nếu user chưa pass test này trước đó
                $prevPassed = getDB()->prepare('
                    SELECT COUNT(*) FROM test_results
                    WHERE user_id=:uid AND test_id=:tid AND id != :rid
                    AND (score / NULLIF(total_points,0) * 100) >= :pass
                ');
                $prevPassed->execute([
                    'uid' => $_SESSION['user_id'],
                    'tid' => $testId,
                    'rid' => $result['result_id'],
                    'pass' => $test['pass_score'],
                ]);
                if ($prevPassed->fetchColumn() == 0 && $test['topic_id']) {
                    $progressModel->increment($_SESSION['user_id'], $test['topic_id'], 'tests_passed');
                }
            }
            if ($test['topic_id']) {
                $progressModel->addScore($_SESSION['user_id'], $test['topic_id'], $result['score']);
            }

            // Nếu là bài thi cuối khóa và đạt → hoàn thành khóa
            if ($testIsFinal && $result['percentage'] >= $testPassScore) {
                // Tìm course_id từ tên bài thi (title chứa tên khóa)
                $courseStmt = getDB()->prepare(
                    'SELECT id FROM courses WHERE title = REPLACE(?, "Final Exam: ", "") AND is_active = 1 LIMIT 1'
                );
                $courseStmt->execute([$testTitle]);
                $course = $courseStmt->fetch();

                if ($course) {
                    require_once APP_PATH . '/models/CourseProgress.php';
                    $cpModel = new CourseProgress();
                    $completeResult = $cpModel->completeCourse($_SESSION['user_id'], $course['id']);

                    $result['course_completed'] = true;
                    if ($completeResult['unlocked_next']) {
                        $result['unlocked_course'] = $completeResult['unlocked_next']['title'];
                    }
                    if ($completeResult['certificate']) {
                        $result['certificate'] = $completeResult['certificate'];
                    }
                }
            }
        }

        return $this->json([
            'success' => true,
            'result_id' => $result['result_id'],
            'score' => $result['score'],
            'total' => $result['total_points'],
            'percentage' => $result['percentage'],
            'passed' => $result['percentage'] >= $testPassScore,
            'is_final' => $testIsFinal,
            'course_completed' => $result['course_completed'] ?? false,
            'unlocked_course' => $result['unlocked_course'] ?? null,
            'certificate' => $result['certificate'] ?? null,
        ]);
    }

    /**
     * Xem kết quả test
     * @param int $resultId
     */
    public function result($resultId = null)
    {
        Middleware::requireLogin();
        if (!$resultId) {
            return $this->redirect('test');
        }

        // Lấy test result
        $stmt = getDB()->prepare('
            SELECT tr.*, t.title as test_title, t.test_type, t.pass_score,
                   tp.name as topic_name, tp.id as topic_id
            FROM test_results tr
            JOIN tests t ON tr.test_id = t.id
            JOIN topics tp ON t.topic_id = tp.id
            WHERE tr.id = :id AND tr.user_id = :user_id
        ');
        $stmt->execute(['id' => $resultId, 'user_id' => $_SESSION['user_id']]);
        $result = $stmt->fetch();

        if (!$result) {
            return $this->redirect('test');
        }

        // Lấy chi tiết từng câu
        $userAnswerModel = $this->model('UserAnswer');
        $details = $userAnswerModel->getResultDetails($resultId);

        $this->view('tests/result', [
            'title' => 'Kết quả: ' . $result['test_title'] . ' - ' . APP_NAME,
            'result' => $result,
            'details' => $details,
            'user' => Middleware::user(),
        ]);
    }

    /**
     * Làm bài thi cuối khóa (final exam — 2 section Reading/Listening)
     * @param int $id Test ID
     */
    public function finalTake($id = null)
    {
        Middleware::requireLogin();
        if (!$id) return $this->redirect('test');

        $testModel = $this->model('Test');
        $test = $testModel->getWithQuestions($id);
        if (!$test || empty($test['has_sections'])) {
            return $this->redirect('test');
        }

        // Tách câu hỏi theo section (reading sort 1-10, listening sort 11-20)
        $readingQuestions = [];
        $listeningQuestions = [];
        foreach ($test['questions'] as $q) {
            if ($q['sort_order'] <= 10) {
                $readingQuestions[] = $q;
            } else {
                $listeningQuestions[] = $q;
            }
        }

        $this->view('tests/final_take', [
            'title' => $test['title'] . ' - ' . APP_NAME,
            'test' => $test,
            'readingQuestions' => $readingQuestions,
            'listeningQuestions' => $listeningQuestions,
            'user' => Middleware::user(),
        ]);
    }

    /**
     * Nộp bài thi cuối khóa (AJAX)
     */
    public function finalSubmit()
    {
        Middleware::requireLogin();
        if (!$this->isMethod('POST')) {
            return $this->json(['error' => 'Method not allowed'], 405);
        }

        $input = Request::json();
        $testId = intval($input['test_id'] ?? 0);
        $answers = $input['answers'] ?? [];
        $timeSpent = intval($input['time_spent'] ?? 0);

        if (!$testId || empty($answers)) {
            return $this->json(['error' => 'Dữ liệu không hợp lệ'], 400);
        }

        $userAnswerModel = $this->model('UserAnswer');
        $result = $userAnswerModel->submitTest($_SESSION['user_id'], $testId, $answers, $timeSpent);

        // Tách điểm theo section
        $testModel = $this->model('Test');
        $test = $testModel->getWithQuestions($testId);
        if (!$test) {
            return $this->json(['error' => 'Không tìm thấy bài test'], 404);
        }
        $readingScore = 0; $readingTotal = 0;
        $listeningScore = 0; $listeningTotal = 0;

        foreach ($test['questions'] as $q) {
            $userAns = $answers[$q['id']] ?? '';
            $correct = strtolower(trim($userAns)) === strtolower(trim($q['correct_answer']));
            $pts = (int)$q['points'];

            if ($q['sort_order'] <= 10) {
                $readingTotal += $pts;
                if ($correct) $readingScore += $pts;
            } else {
                $listeningTotal += $pts;
                if ($correct) $listeningScore += $pts;
            }
        }

        $passed = $result['percentage'] >= 70;
        $response = [
            'success' => true,
            'result_id' => $result['result_id'],
            'score' => $result['score'],
            'total' => $result['total_points'],
            'percentage' => $result['percentage'],
            'passed' => $passed,
            'reading_score' => $readingScore,
            'reading_total' => $readingTotal,
            'listening_score' => $listeningScore,
            'listening_total' => $listeningTotal,
            'is_final' => true,
        ];

        // Nếu pass → completeCourse
        if ($passed) {
            $courseStmt = getDB()->prepare(
                "SELECT id FROM courses WHERE title = REPLACE(?, 'Final Exam: ', '') AND is_active = 1 LIMIT 1"
            );
            $courseStmt->execute([$test['title']]);
            $course = $courseStmt->fetch();

            if ($course) {
                require_once APP_PATH . '/models/CourseProgress.php';
                $cpModel = new CourseProgress();
                $completeResult = $cpModel->completeCourse($_SESSION['user_id'], $course['id']);

                $response['course_completed'] = true;
                if ($completeResult['unlocked_next']) {
                    $response['unlocked_course'] = $completeResult['unlocked_next']['title'];
                }
                if ($completeResult['certificate']) {
                    $response['certificate'] = $completeResult['certificate'];
                }
            }
        }

        return $this->json($response);
    }
}
