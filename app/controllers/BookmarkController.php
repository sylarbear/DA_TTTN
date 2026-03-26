<?php
/**
 * BookmarkController
 * Đánh dấu & quản lý từ vựng yêu thích
 */
class BookmarkController extends Controller {

    public function __construct() {
        Middleware::requireLogin();
    }

    /** Trang từ vựng đã lưu */
    public function index() {
        $db = getDB();
        $stmt = $db->prepare("
            SELECT b.*, v.word, v.pronunciation, v.meaning_vi, v.example_sentence, t.name as topic_name
            FROM bookmarks b
            JOIN vocabularies v ON b.vocabulary_id = v.id
            JOIN topics t ON v.topic_id = t.id
            WHERE b.user_id = :uid
            ORDER BY b.created_at DESC
        ");
        $stmt->execute(['uid' => $_SESSION['user_id']]);
        $bookmarks = $stmt->fetchAll();

        $this->view('bookmark/index', [
            'title' => 'Từ vựng đã lưu - ' . APP_NAME,
            'bookmarks' => $bookmarks
        ]);
    }

    /** Toggle bookmark (AJAX) */
    public function toggle() {
        if (!$this->isMethod('POST')) $this->json(['error' => 'Method not allowed'], 405);
        $input = json_decode(file_get_contents('php://input'), true);
        $vocabId = intval($input['vocabulary_id'] ?? 0);
        if (!$vocabId) $this->json(['error' => 'Invalid'], 400);

        $db = getDB();
        $stmt = $db->prepare("SELECT id FROM bookmarks WHERE user_id=:uid AND vocabulary_id=:vid");
        $stmt->execute(['uid' => $_SESSION['user_id'], 'vid' => $vocabId]);
        $exists = $stmt->fetch();

        if ($exists) {
            $db->prepare("DELETE FROM bookmarks WHERE id=:id")->execute(['id' => $exists['id']]);
            $this->json(['success' => true, 'bookmarked' => false]);
        } else {
            $note = $input['note'] ?? '';
            $db->prepare("INSERT INTO bookmarks (user_id, vocabulary_id, note) VALUES (:uid,:vid,:note)")
               ->execute(['uid' => $_SESSION['user_id'], 'vid' => $vocabId, 'note' => $note]);
            $this->json(['success' => true, 'bookmarked' => true]);
        }
    }

    /** Cập nhật ghi chú (AJAX) */
    public function updateNote() {
        if (!$this->isMethod('POST')) $this->json(['error' => 'Method not allowed'], 405);
        $input = json_decode(file_get_contents('php://input'), true);
        $db = getDB();
        $db->prepare("UPDATE bookmarks SET note=:note WHERE user_id=:uid AND vocabulary_id=:vid")
           ->execute(['note' => $input['note'] ?? '', 'uid' => $_SESSION['user_id'], 'vid' => $input['vocabulary_id']]);
        $this->json(['success' => true]);
    }
}
