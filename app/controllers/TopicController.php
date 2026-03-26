<?php
/**
 * TopicController
 * Hiển thị chủ đề học tập + từ vựng
 */
class TopicController extends Controller {

    /**
     * Danh sách tất cả topics
     */
    public function index() {
        $topicModel = $this->model('Topic');
        $topics = $topicModel->getAllWithStats();

        // Lọc theo level nếu có
        $level = $this->query('level');

        $this->view('topics/index', [
            'title'  => 'Chủ đề học tập - ' . APP_NAME,
            'topics' => $topics,
            'level'  => $level,
            'user'   => Middleware::user()
        ]);
    }

    /**
     * Chi tiết topic + từ vựng
     * @param int $id Topic ID
     */
    public function show($id = null) {
        if (!$id) $this->redirect('topic');

        $topicModel = $this->model('Topic');
        $vocabModel = $this->model('Vocabulary');
        $lessonModel = $this->model('Lesson');

        $topic = $topicModel->getWithStats($id);
        if (!$topic) $this->redirect('topic');

        $vocabularies = $vocabModel->getByTopic($id);
        $lessons = $lessonModel->getByTopic($id);

        // Lấy progress nếu đã đăng nhập
        $progress = null;
        if (Middleware::isLoggedIn()) {
            $progressModel = $this->model('UserProgress');
            $progress = $progressModel->getOrCreate($_SESSION['user_id'], $id);
        }

        $this->view('topics/show', [
            'title'        => $topic['name'] . ' - ' . APP_NAME,
            'topic'        => $topic,
            'vocabularies' => $vocabularies,
            'lessons'      => $lessons,
            'progress'     => $progress,
            'user'         => Middleware::user()
        ]);
    }

    /**
     * Đánh dấu từ vựng đã học (AJAX)
     */
    public function learnVocab() {
        Middleware::requireLogin();

        if ($this->isMethod('POST')) {
            $topicId = intval($_POST['topic_id'] ?? 0);
            if ($topicId > 0) {
                $progressModel = $this->model('UserProgress');
                $progressModel->increment($_SESSION['user_id'], $topicId, 'vocab_learned');
                
                // Award XP
                require_once APP_PATH . '/core/StreakService.php';
                StreakService::updateStreak($_SESSION['user_id']);
                StreakService::addXP($_SESSION['user_id'], 10, 'vocab_learn', 'Học từ vựng');
                
                $this->json(['success' => true]);
            }
        }
        $this->json(['success' => false], 400);
    }

    /**
     * Flashcard - Luyện từ vựng bằng thẻ lật
     */
    public function flashcard($id = null) {
        if (!$id) $this->redirect('topic');
        Middleware::requireLogin();

        $topicModel = $this->model('Topic');
        $vocabModel = $this->model('Vocabulary');

        $topic = $topicModel->getWithStats($id);
        if (!$topic) $this->redirect('topic');

        $vocabularies = $vocabModel->getByTopic($id);

        $this->view('topics/flashcard', [
            'title' => 'Flashcard: ' . $topic['name'] . ' - ' . APP_NAME,
            'topic' => $topic,
            'vocabularies' => $vocabularies
        ]);
    }
}
