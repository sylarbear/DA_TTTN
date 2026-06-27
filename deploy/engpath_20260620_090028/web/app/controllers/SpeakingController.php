<?php
/**
 * SpeakingController
 * Luyện nói + chấm điểm
 */
class SpeakingController extends Controller {

    /**
     * Danh sách bài speaking
     */
    public function index() {
        $speakingModel = $this->model('SpeakingAttempt');
        $prompts = $speakingModel->getAllPrompts();

        // Group theo topic
        $groupedPrompts = [];
        foreach ($prompts as $p) {
            $groupedPrompts[$p['topic_name']][] = $p;
        }

        $this->view('speaking/index', [
            'title'          => 'Luyện nói - ' . APP_NAME,
            'groupedPrompts' => $groupedPrompts,
            'user'           => Middleware::user()
        ]);
    }

    /**
     * Trang luyện phát âm tự do (nhập text bất kỳ)
     */
    public function freetext() {
        Middleware::requireLogin();

        $this->view('speaking/freetext', [
            'title' => 'Luyện phát âm tự do - ' . APP_NAME,
            'user'  => Middleware::user()
        ]);
    }

    /**
     * Trang luyện nói
     * @param int $promptId
     */
    public function practice($promptId = null) {
        Middleware::requireLogin();
        Middleware::requirePro();
        if (!$promptId) return $this->redirect('speaking');

        $speakingModel = $this->model('SpeakingAttempt');
        $prompt = $speakingModel->getPrompt($promptId);
        if (!$prompt) return $this->redirect('speaking');

        // Lấy lịch sử attempts
        $stmt = getDB()->prepare("
            SELECT * FROM speaking_attempts 
            WHERE user_id = :user_id AND prompt_id = :prompt_id 
            ORDER BY created_at DESC LIMIT 5
        ");
        $stmt->execute(['user_id' => $_SESSION['user_id'], 'prompt_id' => $promptId]);
        $history = $stmt->fetchAll();

        $this->view('speaking/practice', [
            'title'   => 'Luyện nói - ' . APP_NAME,
            'prompt'  => $prompt,
            'history' => $history,
            'user'    => Middleware::user()
        ]);
    }

    /**
     * Chấm điểm speaking (AJAX)
     * Ưu tiên dùng OpenAI, fallback về rule-based
     */
    public function score() {
        Middleware::requireLogin();

        if (!$this->isMethod('POST')) {
            return $this->json(['error' => 'Method not allowed'], 405);
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $promptId   = intval($input['prompt_id'] ?? 0);
        $transcript = $input['transcript'] ?? '';
        $confidence = floatval($input['confidence'] ?? 0.5);

        if (!$promptId || empty($transcript)) {
            return $this->json(['error' => 'Dữ liệu không hợp lệ'], 400);
        }

        $speakingModel = $this->model('SpeakingAttempt');
        $prompt = $speakingModel->getPrompt($promptId);

        if (!$prompt) {
            return $this->json(['error' => 'Prompt không tồn tại'], 404);
        }

        // Try AI scoring first
        $scores = null;
        $aiUsed = false;
        require_once APP_PATH . '/core/OpenAIService.php';
        
        if (OpenAIService::isAvailable()) {
            $scores = OpenAIService::scoreSpeaking($transcript, $prompt['sample_answer']);
            if ($scores) $aiUsed = true;
        }

        // Fallback to local scoring
        if (!$scores) {
            $scores = $speakingModel->scoreSpeaking($transcript, $prompt['sample_answer'], $confidence);
        }

        // Lưu attempt
        $attemptId = $speakingModel->saveAttempt($_SESSION['user_id'], $promptId, $transcript, $scores);

        // Cập nhật progress: chỉ increment speaking_practiced nếu chưa luyện prompt này
        $progressModel = $this->model('UserProgress');
        $existingAttempts = getDB()->prepare("SELECT COUNT(*) FROM speaking_attempts WHERE user_id=:uid AND prompt_id=:pid AND id != :aid");
        $existingAttempts->execute(['uid' => $_SESSION['user_id'], 'pid' => $promptId, 'aid' => $attemptId]);
        if ($existingAttempts->fetchColumn() == 0) {
            $progressModel->increment($_SESSION['user_id'], $prompt['topic_id'], 'speaking_practiced');
        }
        $progressModel->addScore($_SESSION['user_id'], $prompt['topic_id'], $scores['overall_score']);

        // Award XP chỉ khi overall_score >= 30
        require_once APP_PATH . '/core/StreakService.php';
        if ($scores['overall_score'] >= 30) {
            StreakService::addXP($_SESSION['user_id'], 30, 'speaking_practice', 'Luyện nói (' . $scores['overall_score'] . ' điểm)');
        }

        return $this->json([
            'success'    => true,
            'attempt_id' => $attemptId,
            'scores'     => $scores,
            'transcript' => $transcript,
            'ai_used'    => $aiUsed
        ]);
    }
}
