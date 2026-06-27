<?php
/**
 * Topic Model
 * Quản lý chủ đề học tập
 */
class Topic extends Model {
    protected $table = 'topics';

    /**
     * Lấy tất cả topics đang active
     * @return array
     */
    public function getActive() {
        return $this->where('is_active', 1, 'sort_order ASC');
    }

    /**
     * Tìm topic theo slug
     * @param string $slug
     * @return array|false
     */
    public function findBySlug($slug) {
        return $this->findBy('slug', $slug);
    }

    /**
     * Lấy topics theo level
     * @param string $level beginner|intermediate|advanced
     * @return array
     */
    public function getByLevel($level) {
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} WHERE level = :level AND is_active = 1 ORDER BY sort_order ASC"
        );
        $stmt->execute(['level' => $level]);
        return $stmt->fetchAll();
    }

    /**
     * Lấy topic kèm thống kê số lượng
     * @param int $id
     * @return array|false
     */
    public function getWithStats($id) {
        $stmt = $this->db->prepare("
            SELECT t.*,
                (SELECT COUNT(*) FROM vocabularies WHERE topic_id = t.id) as vocab_count,
                (SELECT COUNT(*) FROM lessons WHERE topic_id = t.id AND is_active = 1) as lesson_count,
                (SELECT COUNT(*) FROM tests WHERE topic_id = t.id AND is_active = 1) as test_count,
                (SELECT COUNT(*) FROM speaking_prompts WHERE topic_id = t.id) as speaking_count
            FROM {$this->table} t
            WHERE t.id = :id
        ");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Lấy tất cả topics kèm thống kê
     * @return array
     */
    /**
     * Tìm kiếm tổng hợp (topics, vocab, lessons, tests, grammar)
     * @param string $keyword
     * @return array
     */
    public function search($keyword) {
        $like = '%' . addcslashes($keyword, '%_') . '%';
        $results = [];

        // Tìm trong topics
        $stmt = $this->db->prepare("
            SELECT id, name as title, description, level, 'topic' as type
            FROM topics WHERE is_active = 1 AND (name LIKE :k OR description LIKE :k2)
            ORDER BY sort_order ASC LIMIT 10
        ");
        $stmt->execute(['k' => $like, 'k2' => $like]);
        $results['topics'] = $stmt->fetchAll();

        // Tìm trong từ vựng
        $stmt = $this->db->prepare("
            SELECT v.id, v.word as title, v.meaning_vi as description, t.name as topic_name, t.id as topic_id, 'vocab' as type
            FROM vocabularies v JOIN topics t ON v.topic_id = t.id
            WHERE v.word LIKE :k OR v.meaning_vi LIKE :k2 OR v.pronunciation LIKE :k3
            LIMIT 15
        ");
        $stmt->execute(['k' => $like, 'k2' => $like, 'k3' => $like]);
        $results['vocabularies'] = $stmt->fetchAll();

        // Tìm trong bài test
        $stmt = $this->db->prepare("
            SELECT ts.id, ts.title, ts.test_type as description, t.name as topic_name, t.id as topic_id, 'test' as type
            FROM tests ts JOIN topics t ON ts.topic_id = t.id
            WHERE ts.is_active = 1 AND ts.title LIKE :k
            LIMIT 10
        ");
        $stmt->execute(['k' => $like]);
        $results['tests'] = $stmt->fetchAll();

        // Tìm trong bài học
        $stmt = $this->db->prepare("
            SELECT l.id, l.title, t.name as topic_name, t.id as topic_id, 'lesson' as type
            FROM lessons l JOIN topics t ON l.topic_id = t.id
            WHERE l.is_active = 1 AND l.title LIKE :k
            LIMIT 10
        ");
        $stmt->execute(['k' => $like]);
        $results['lessons'] = $stmt->fetchAll();

        // Tìm trong ngữ pháp
        $stmt = $this->db->prepare("
            SELECT id, title, category as description, level, 'grammar' as type
            FROM grammar_lessons WHERE title LIKE :k
            LIMIT 10
        ");
        $stmt->execute(['k' => $like]);
        $results['grammar'] = $stmt->fetchAll();

        return $results;
    }

    public function getAllWithStats() {
        $stmt = $this->db->query("
            SELECT t.*,
                (SELECT COUNT(*) FROM vocabularies WHERE topic_id = t.id) as vocab_count,
                (SELECT COUNT(*) FROM lessons WHERE topic_id = t.id AND is_active = 1) as lesson_count,
                (SELECT COUNT(*) FROM tests WHERE topic_id = t.id AND is_active = 1) as test_count,
                (SELECT COUNT(*) FROM speaking_prompts WHERE topic_id = t.id) as speaking_count
            FROM {$this->table} t
            WHERE t.is_active = 1
            ORDER BY t.sort_order ASC
        ");
        return $stmt->fetchAll();
    }
}
