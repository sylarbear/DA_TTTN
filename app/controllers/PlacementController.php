<?php

/**
 * PlacementController
 * Xử lý bài kiểm tra đầu vào (placement test) kiểu Duolingo
 */
class PlacementController extends Controller
{
    /**
     * GET /placement — Redirect to intro
     */
    public function index()
    {
        return $this->redirect('placement/intro');
    }

    /**
     * GET /placement/intro
     * Trang hỏi "Bạn biết tiếng Anh ở mức nào?"
     */
    public function intro()
    {
        Middleware::requireLogin();

        // Nếu đã có placement, chuyển thẳng về dashboard
        $user = Middleware::user();
        if (!empty($user['placement_level'])) {
            return $this->redirect('');
        }

        $this->view('placement/intro', [
            'title' => 'Xác định trình độ - ' . APP_NAME,
            'user'  => $user,
        ]);
    }

    /**
     * GET /placement/start?level=some|advanced
     * Tạo session placement mới và redirect sang trang làm bài
     */
    public function start()
    {
        Middleware::requireLogin();

        $level = $this->query('level', 'some');
        if (!in_array($level, ['some', 'advanced'])) {
            $level = 'some';
        }

        $placementModel = $this->model('Placement');
        $sessionId = $placementModel->startSession($_SESSION['user_id'], $level);

        $_SESSION['placement_session_id'] = $sessionId;

        return $this->redirect('placement/take');
    }

    /**
     * POST /placement/beginner
     * User chọn "Mới bắt đầu" — gán A1 luôn
     */
    public function beginner()
    {
        Middleware::requireLogin();

        if (!$this->isMethod('POST')) {
            return $this->redirect('placement/intro');
        }

        $placementModel = $this->model('Placement');
        $placementModel->markAsBeginner($_SESSION['user_id']);

        $this->setFlash('success', 'Chào mừng bạn đến với EngPath! Hãy bắt đầu hành trình học tiếng Anh từ hôm nay.');

        return $this->redirect('');
    }

    /**
     * GET /placement/take
     * Giao diện làm bài một câu một lần
     */
    public function take()
    {
        Middleware::requireLogin();

        $placementModel = $this->model('Placement');
        $session = $placementModel->getActiveSession($_SESSION['user_id']);

        if (!$session) {
            // Không có session đang dở → quay về intro
            return $this->redirect('placement/intro');
        }

        // Lấy câu hỏi đầu tiên
        $excludeIds = $placementModel->getAskedQuestionIds($session['id']);
        $skillIndex = count($excludeIds) % 4;
        $skillTypes = ['vocabulary', 'grammar', 'reading', 'listening'];
        $skillType = $skillTypes[$skillIndex];

        $firstQuestion = $placementModel->selectNextQuestion(
            (float) $session['current_theta'],
            $excludeIds,
            $skillType
        );

        $this->view('placement/take', [
            'title'     => 'Kiểm tra đầu vào - ' . APP_NAME,
            'session'   => $session,
            'question'  => $firstQuestion,
            'user'      => Middleware::user(),
        ]);
    }

    /**
     * POST /placement/next (AJAX)
     * Nhận câu trả lời, trả về feedback + câu hỏi tiếp theo
     */
    public function next()
    {
        Middleware::requireLogin();

        if (!$this->isMethod('POST')) {
            return $this->json(['error' => 'Method not allowed'], 405);
        }

        $input = Request::json();
        $sessionId = intval($input['session_id'] ?? 0);
        $questionId = intval($input['question_id'] ?? 0);
        $userAnswer = strval($input['answer'] ?? '');
        $responseTimeMs = intval($input['response_time_ms'] ?? 0);

        if (!$sessionId || !$questionId) {
            return $this->json(['error' => 'Dữ liệu không hợp lệ'], 400);
        }

        $placementModel = $this->model('Placement');
        $session = $placementModel->find($sessionId);

        if (!$session || $session['user_id'] != $_SESSION['user_id']) {
            return $this->json(['error' => 'Phiên làm bài không hợp lệ'], 403);
        }

        if ($session['status'] !== 'in_progress') {
            return $this->json(['error' => 'Phiên làm bài đã kết thúc'], 400);
        }

        // Xử lý câu trả lời
        $result = $placementModel->processResponse($sessionId, $questionId, $userAnswer, $responseTimeMs);

        // Kiểm tra điều kiện kết thúc
        $shouldTerminate = $placementModel->shouldTerminate($sessionId);
        $isMaxedOut = $placementModel->isMaxedOut($sessionId);

        if ($shouldTerminate || $isMaxedOut) {
            // Kết thúc phiên
            $finalResult = $placementModel->finalizeSession($sessionId);

            return $this->json([
                'done'     => true,
                'feedback' => [
                    'is_correct'     => $result['is_correct'],
                    'correct_answer' => $result['correct_answer'],
                    'explanation'    => $result['explanation'],
                ],
                'result'   => $finalResult,
                'redirect' => BASE_URL . '/placement/result',
            ]);
        }

        // Chọn câu hỏi tiếp theo (xoay vòng skill type)
        $excludeIds = $placementModel->getAskedQuestionIds($sessionId);
        $skillIndex = count($excludeIds) % 4;
        $skillTypes = ['vocabulary', 'grammar', 'reading', 'listening'];
        $skillType = $skillTypes[$skillIndex];

        $nextQuestion = $placementModel->selectNextQuestion(
            (float) $session['current_theta'],
            $excludeIds,
            $skillType
        );

        // Tính progress
        $progress = $session['total_questions'] > 0
            ? round($result['questions_answered'] / $session['total_questions'] * 100)
            : 0;

        return $this->json([
            'done'         => false,
            'feedback'     => [
                'is_correct'     => $result['is_correct'],
                'correct_answer' => $result['correct_answer'],
                'explanation'    => $result['explanation'],
            ],
            'nextQuestion' => $nextQuestion,
            'progress'     => $progress,
            'questionsAnswered' => $result['questions_answered'],
        ]);
    }

    /**
     * GET /placement/result
     * Trang kết quả cuối cùng
     */
    public function result()
    {
        Middleware::requireLogin();

        $placementModel = $this->model('Placement');
        $lastResult = $placementModel->getLastResult($_SESSION['user_id']);

        if (!$lastResult) {
            return $this->redirect('placement/intro');
        }

        // Lấy topic gợi ý
        $recommendedTopics = $placementModel->getRecommendedTopics($lastResult['final_cefr']);

        // Lấy chi tiết các câu trả lời
        $stmt = getDB()->prepare(
            'SELECT pr.*, pq.question_text, pq.cefr_level, pq.skill_type
             FROM placement_responses pr
             JOIN placement_questions pq ON pr.question_id = pq.id
             WHERE pr.session_id = :sid
             ORDER BY pr.question_order ASC'
        );
        $stmt->execute(['sid' => $lastResult['id']]);
        $responses = $stmt->fetchAll();

        // Map CEFR để hiển thị
        $cefrToLevel = [
            'A1' => 1, 'A2' => 2, 'B1' => 4, 'B2' => 6, 'C1' => 9,
        ];

        $this->view('placement/result', [
            'title'             => 'Kết quả kiểm tra - ' . APP_NAME,
            'result'            => $lastResult,
            'responses'         => $responses,
            'recommendedTopics' => $recommendedTopics,
            'appLevel'          => $cefrToLevel[$lastResult['final_cefr']] ?? 1,
            'user'              => Middleware::user(),
        ]);
    }

    /**
     * GET /placement/resume
     * Tiếp tục phiên đang dở
     */
    public function resume()
    {
        Middleware::requireLogin();

        $placementModel = $this->model('Placement');
        $session = $placementModel->getActiveSession($_SESSION['user_id']);

        if (!$session) {
            return $this->redirect('placement/intro');
        }

        return $this->redirect('placement/take');
    }
}
