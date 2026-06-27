<?php
/**
 * GrammarController
 * Bài học ngữ pháp + Quiz
 */
class GrammarController extends Controller {

    public function __construct() {
        Middleware::requireLogin();
    }

    /** Danh sách bài ngữ pháp */
    public function index() {
        $db = getDB();
        $lessons = $db->query("
            SELECT gl.*, 
                (SELECT COUNT(*) FROM grammar_questions WHERE grammar_lesson_id=gl.id) as question_count
            FROM grammar_lessons gl ORDER BY gl.sort_order
        ")->fetchAll();

        // Group by category
        $grouped = [];
        foreach ($lessons as $l) {
            $grouped[$l['category']][] = $l;
        }

        $this->view('grammar/index', [
            'title' => 'Ngữ pháp - ' . APP_NAME,
            'grouped' => $grouped,
            'lessons' => $lessons,
            'user' => Middleware::user()
        ]);
    }

    /** Chi tiết bài ngữ pháp */
    public function show($id = null) {
        if (!$id) return $this->redirect('grammar');
        $db = getDB();

        $stmt = $db->prepare("SELECT * FROM grammar_lessons WHERE id=:id");
        $stmt->execute(['id' => $id]);
        $lesson = $stmt->fetch();
        if (!$lesson) return $this->redirect('grammar');

        $stmt = $db->prepare("SELECT * FROM grammar_questions WHERE grammar_lesson_id=:id");
        $stmt->execute(['id' => $id]);
        $questions = $stmt->fetchAll();

        $this->view('grammar/show', [
            'title' => $lesson['title'] . ' - ' . APP_NAME,
            'lesson' => $lesson,
            'questions' => $questions,
            'user' => Middleware::user()
        ]);
    }

    /** Chấm quiz ngữ pháp (AJAX) */
    public function submitQuiz() {
        if (!$this->isMethod('POST')) return $this->json(['error' => 'Method not allowed'], 405);
        $input = json_decode(file_get_contents('php://input'), true);
        $answers = $input['answers'] ?? [];
        $lessonId = intval($input['lesson_id'] ?? 0);

        $db = getDB();
        $stmt = $db->prepare("SELECT id, correct_answer, explanation FROM grammar_questions WHERE grammar_lesson_id=:id");
        $stmt->execute(['id' => $lessonId]);
        $questions = $stmt->fetchAll();

        $correct = 0;
        $results = [];
        foreach ($questions as $q) {
            $userAns = $answers[$q['id']] ?? '';
            $isCorrect = ($userAns === $q['correct_answer']);
            if ($isCorrect) $correct++;
            $results[] = [
                'id' => $q['id'],
                'correct' => $isCorrect,
                'correct_answer' => $q['correct_answer'],
                'explanation' => $q['explanation']
            ];
        }

        // Award XP only if score >= 50%
        $scorePercent = count($questions) > 0 ? round($correct / count($questions) * 100) : 0;
        require_once APP_PATH . '/core/StreakService.php';
        StreakService::updateStreak($_SESSION['user_id']);
        if ($scorePercent >= 50) {
            StreakService::addXP($_SESSION['user_id'], 20, 'lesson_complete', "Hoàn thành quiz ngữ pháp ({$scorePercent}%)");
        }

        return $this->json([
            'success' => true,
            'correct' => $correct,
            'total' => count($questions),
            'score' => count($questions) > 0 ? round($correct / count($questions) * 100) : 0,
            'results' => $results
        ]);
    }
}
