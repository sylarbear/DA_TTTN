<?php
/**
 * LessonController
 * Hiển thị bài học
 */
class LessonController extends Controller {

    /**
     * Danh sách bài học theo topic
     * @param int $topicId
     */
    public function index($topicId = null) {
        if (!$topicId) return $this->redirect('topic');

        $topicModel = $this->model('Topic');
        $lessonModel = $this->model('Lesson');

        $topic = $topicModel->find($topicId);
        if (!$topic) return $this->redirect('topic');

        $lessons = $lessonModel->getByTopic($topicId);

        $this->view('lessons/index', [
            'title'   => 'Bài học: ' . $topic['name'] . ' - ' . APP_NAME,
            'topic'   => $topic,
            'lessons' => $lessons,
            'user'    => Middleware::user()
        ]);
    }

    /**
     * Chi tiết bài học
     * @param int $id Lesson ID
     */
    public function show($id = null) {
        if (!$id) return $this->redirect('topic');

        $lessonModel = $this->model('Lesson');
        $lesson = $lessonModel->getWithContents($id);
        if (!$lesson) return $this->redirect('topic');

        $topicModel = $this->model('Topic');
        $topic = $topicModel->find($lesson['topic_id']);

        // Đánh dấu hoàn thành nếu đã đăng nhập (chỉ cộng 1 lần mỗi session)
        if (Middleware::isLoggedIn()) {
            $sessionKey = 'completed_lesson_ids';
            $completedIds = $_SESSION[$sessionKey] ?? [];
            if (!in_array($id, $completedIds)) {
                // Check if not already exceeding total lessons for this topic
                $progressModel = $this->model('UserProgress');
                $totalLessons = count($lessonModel->getByTopic($lesson['topic_id']));
                $progress = $progressModel->getOrCreate($_SESSION['user_id'], $lesson['topic_id']);
                if ($progress['lessons_completed'] < $totalLessons) {
                    $progressModel->increment($_SESSION['user_id'], $lesson['topic_id'], 'lessons_completed');
                }
                $completedIds[] = $id;
                $_SESSION[$sessionKey] = $completedIds;
            }
        }

        // Lấy bài học tiếp theo
        $allLessons = $lessonModel->getByTopic($lesson['topic_id']);
        $nextLesson = null;
        $prevLesson = null;
        foreach ($allLessons as $i => $l) {
            if ($l['id'] == $id) {
                $nextLesson = $allLessons[$i + 1] ?? null;
                $prevLesson = $allLessons[$i - 1] ?? null;
                break;
            }
        }

        // Lấy reviews cho bài học này
        $db = getDB();
        $reviewStmt = $db->prepare("
            SELECT lr.*, u.username, u.full_name, u.avatar 
            FROM lesson_reviews lr 
            JOIN users u ON lr.user_id = u.id 
            WHERE lr.lesson_id = :lid 
            ORDER BY lr.created_at DESC LIMIT 20
        ");
        $reviewStmt->execute(['lid' => $id]);
        $reviews = $reviewStmt->fetchAll();

        // Rating trung bình
        $avgStmt = $db->prepare("SELECT AVG(rating) as avg_rating, COUNT(*) as total_reviews FROM lesson_reviews WHERE lesson_id = :lid");
        $avgStmt->execute(['lid' => $id]);
        $reviewStats = $avgStmt->fetch();

        // Review của user hiện tại (nếu đã login)
        $userReview = null;
        if (Middleware::isLoggedIn()) {
            $myReview = $db->prepare("SELECT * FROM lesson_reviews WHERE user_id = :uid AND lesson_id = :lid");
            $myReview->execute(['uid' => $_SESSION['user_id'], 'lid' => $id]);
            $userReview = $myReview->fetch();
        }

        $this->view('lessons/show', [
            'title'       => $lesson['title'] . ' - ' . APP_NAME,
            'lesson'      => $lesson,
            'topic'       => $topic,
            'nextLesson'  => $nextLesson,
            'prevLesson'  => $prevLesson,
            'reviews'     => $reviews,
            'reviewStats' => $reviewStats,
            'userReview'  => $userReview,
            'user'        => Middleware::user()
        ]);
    }

    /**
     * Đánh giá bài học (AJAX POST)
     */
    public function review() {
        Middleware::requireLogin();
        if (!$this->isMethod('POST')) return $this->json(['error' => 'Method not allowed'], 405);

        $input = json_decode(file_get_contents('php://input'), true);
        $lessonId = intval($input['lesson_id'] ?? 0);
        $rating = intval($input['rating'] ?? 0);
        $comment = trim($input['comment'] ?? '');

        if (!$lessonId || $rating < 1 || $rating > 5) {
            return $this->json(['error' => 'Dữ liệu không hợp lệ. Rating phải từ 1-5.'], 400);
        }

        $db = getDB();

        // Check lesson exists
        $lesson = $db->prepare("SELECT id FROM lessons WHERE id = :id");
        $lesson->execute(['id' => $lessonId]);
        if (!$lesson->fetch()) return $this->json(['error' => 'Bài học không tồn tại.'], 404);

        // Upsert review
        $stmt = $db->prepare("
            INSERT INTO lesson_reviews (user_id, lesson_id, rating, comment) 
            VALUES (:uid, :lid, :r, :c)
            ON DUPLICATE KEY UPDATE rating = :r2, comment = :c2, updated_at = CURRENT_TIMESTAMP
        ");
        $stmt->execute([
            'uid' => $_SESSION['user_id'],
            'lid' => $lessonId,
            'r' => $rating,
            'c' => $comment ?: null,
            'r2' => $rating,
            'c2' => $comment ?: null
        ]);

        // Get updated stats
        $avg = $db->prepare("SELECT AVG(rating) as avg_rating, COUNT(*) as total_reviews FROM lesson_reviews WHERE lesson_id = :lid");
        $avg->execute(['lid' => $lessonId]);
        $stats = $avg->fetch();

        // Award XP
        require_once APP_PATH . '/core/StreakService.php';
        StreakService::addXP($_SESSION['user_id'], 5, 'lesson_review', 'Đánh giá bài học');

        return $this->json([
            'success' => true,
            'message' => 'Cảm ơn bạn đã đánh giá!',
            'avg_rating' => round($stats['avg_rating'], 1),
            'total_reviews' => (int)$stats['total_reviews']
        ]);
    }
}
