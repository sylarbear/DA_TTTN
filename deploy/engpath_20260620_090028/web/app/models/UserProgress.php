<?php
/**
 * UserProgress Model
 * Theo dõi tiến độ học tập
 */
class UserProgress extends Model {
    protected $table = 'user_progress';

    /**
     * Lấy hoặc tạo progress cho user-topic
     * @param int $userId
     * @param int $topicId
     * @return array
     */
    public function getOrCreate($userId, $topicId) {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table} 
            WHERE user_id = :user_id AND topic_id = :topic_id
        ");
        $stmt->execute(['user_id' => $userId, 'topic_id' => $topicId]);
        $progress = $stmt->fetch();

        if (!$progress) {
            $this->create([
                'user_id'  => $userId,
                'topic_id' => $topicId
            ]);
            return $this->getOrCreate($userId, $topicId);
        }

        return $progress;
    }

    /**
     * Cập nhật tiến độ
     * @param int $userId
     * @param int $topicId
     * @param string $field vocab_learned|lessons_completed|tests_passed|speaking_practiced
     * @param int $increment
     */
    public function increment($userId, $topicId, $field, $increment = 1) {
        // Whitelist allowed fields to prevent SQL injection
        $allowed = ['vocab_learned', 'lessons_completed', 'tests_passed', 'speaking_practiced'];
        if (!in_array($field, $allowed)) return;

        $this->getOrCreate($userId, $topicId);

        $stmt = $this->db->prepare("
            UPDATE {$this->table} 
            SET {$field} = {$field} + :inc, last_activity = CURRENT_TIMESTAMP
            WHERE user_id = :user_id AND topic_id = :topic_id
        ");
        $stmt->execute([
            'inc'      => $increment,
            'user_id'  => $userId,
            'topic_id' => $topicId
        ]);
    }

    /**
     * Cập nhật điểm
     */
    public function addScore($userId, $topicId, $score) {
        $this->getOrCreate($userId, $topicId);

        $stmt = $this->db->prepare("
            UPDATE {$this->table}
            SET total_score = total_score + :score, last_activity = CURRENT_TIMESTAMP
            WHERE user_id = :user_id AND topic_id = :topic_id
        ");
        $stmt->execute([
            'score'    => $score,
            'user_id'  => $userId,
            'topic_id' => $topicId
        ]);
    }

    /**
     * Dashboard - Lấy tổng hợp tiến độ của user
     * @param int $userId
     * @return array
     */
    public function getDashboardData($userId) {
        // Progress theo từng topic
        $stmt = $this->db->prepare("
            SELECT up.*, t.name as topic_name, t.slug as topic_slug, t.level,
                (SELECT COUNT(*) FROM vocabularies WHERE topic_id = t.id) as total_vocab,
                (SELECT COUNT(*) FROM lessons WHERE topic_id = t.id AND is_active = 1) as total_lessons,
                (SELECT COUNT(*) FROM tests WHERE topic_id = t.id AND is_active = 1) as total_tests,
                (SELECT COUNT(*) FROM speaking_prompts WHERE topic_id = t.id) as total_speaking
            FROM {$this->table} up
            JOIN topics t ON up.topic_id = t.id
            WHERE up.user_id = :user_id
            ORDER BY up.last_activity DESC
        ");
        $stmt->execute(['user_id' => $userId]);
        $topicProgress = $stmt->fetchAll();

        // Tổng hợp chung
        $stmt = $this->db->prepare("
            SELECT 
                COALESCE(SUM(vocab_learned), 0) as total_vocab_learned,
                COALESCE(SUM(lessons_completed), 0) as total_lessons_completed,
                COALESCE(SUM(tests_passed), 0) as total_tests_passed,
                COALESCE(SUM(speaking_practiced), 0) as total_speaking_practiced,
                COALESCE(SUM(total_score), 0) as grand_total_score
            FROM {$this->table}
            WHERE user_id = :user_id
        ");
        $stmt->execute(['user_id' => $userId]);
        $overall = $stmt->fetch();

        // Kết quả test gần đây
        $stmt = $this->db->prepare("
            SELECT tr.*, t.title as test_title, t.test_type, tp.name as topic_name
            FROM test_results tr
            JOIN tests t ON tr.test_id = t.id
            JOIN topics tp ON t.topic_id = tp.id
            WHERE tr.user_id = :user_id
            ORDER BY tr.completed_at DESC
            LIMIT 10
        ");
        $stmt->execute(['user_id' => $userId]);
        $recentTests = $stmt->fetchAll();

        // Speaking attempts gần đây
        $stmt = $this->db->prepare("
            SELECT sa.*, sp.prompt_text, t.name as topic_name
            FROM speaking_attempts sa
            JOIN speaking_prompts sp ON sa.prompt_id = sp.id
            JOIN topics t ON sp.topic_id = t.id
            WHERE sa.user_id = :user_id
            ORDER BY sa.created_at DESC
            LIMIT 10
        ");
        $stmt->execute(['user_id' => $userId]);
        $recentSpeaking = $stmt->fetchAll();

        return [
            'topic_progress'  => $topicProgress,
            'overall'         => $overall,
            'recent_tests'    => $recentTests,
            'recent_speaking' => $recentSpeaking
        ];
    }
}
