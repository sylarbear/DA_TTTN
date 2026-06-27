<?php
/**
 * Test Model
 * Quản lý bài kiểm tra
 */
class Test extends Model {
    protected $table = 'tests';

    /**
     * Lấy tests theo topic
     * @param int $topicId
     * @return array
     */
    public function getByTopic($topicId) {
        $stmt = $this->db->prepare("
            SELECT t.*, 
                (SELECT COUNT(*) FROM questions WHERE test_id = t.id) as question_count
            FROM {$this->table} t
            WHERE t.topic_id = :topic_id AND t.is_active = 1 
            ORDER BY t.id ASC
        ");
        $stmt->execute(['topic_id' => $topicId]);
        return $stmt->fetchAll();
    }

    /**
     * Lấy tất cả tests kèm thông tin topic
     * @return array
     */
    public function getAllWithTopic() {
        $stmt = $this->db->query("
            SELECT t.*, tp.name as topic_name, tp.slug as topic_slug,
                (SELECT COUNT(*) FROM questions WHERE test_id = t.id) as question_count
            FROM {$this->table} t
            JOIN topics tp ON t.topic_id = tp.id
            WHERE t.is_active = 1
            ORDER BY tp.sort_order ASC, t.id ASC
        ");
        return $stmt->fetchAll();
    }

    /**
     * Lấy test kèm câu hỏi
     * @param int $id
     * @return array|false
     */
    public function getWithQuestions($id) {
        $test = $this->find($id);
        if (!$test) return false;

        $stmt = $this->db->prepare("
            SELECT * FROM questions 
            WHERE test_id = :test_id 
            ORDER BY sort_order ASC
        ");
        $stmt->execute(['test_id' => $id]);
        $test['questions'] = $stmt->fetchAll();

        // Parse JSON options
        foreach ($test['questions'] as &$q) {
            if ($q['options_json']) {
                $parsed = json_decode($q['options_json'], true);
                // Normalize: if object format {"A":"opt1","B":"opt2",...}, convert to array
                // and resolve correct_answer from key ("A") to actual text
                if ($parsed && isset($parsed['A'])) {
                    // correct_answer is a key like "A" — resolve to the text value
                    if (isset($parsed[$q['correct_answer']])) {
                        $q['correct_answer'] = $parsed[$q['correct_answer']];
                    }
                    $q['options'] = array_values($parsed);
                } else {
                    $q['options'] = $parsed ?: [];
                }
            } else {
                $q['options'] = [];
            }
        }

        return $test;
    }

    /**
     * Lấy lịch sử làm bài của user
     * @param int $userId
     * @param int|null $testId
     * @return array
     */
    public function getUserResults($userId, $testId = null) {
        $sql = "
            SELECT tr.*, t.title as test_title, t.test_type, tp.name as topic_name
            FROM test_results tr
            JOIN tests t ON tr.test_id = t.id
            JOIN topics tp ON t.topic_id = tp.id
            WHERE tr.user_id = :user_id
        ";
        $params = ['user_id' => $userId];

        if ($testId) {
            $sql .= " AND tr.test_id = :test_id";
            $params['test_id'] = $testId;
        }

        $sql .= " ORDER BY tr.completed_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
