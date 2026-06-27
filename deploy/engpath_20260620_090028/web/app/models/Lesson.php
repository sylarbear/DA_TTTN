<?php
/**
 * Lesson Model
 * Quản lý bài học
 */
class Lesson extends Model {
    protected $table = 'lessons';

    /**
     * Lấy bài học theo topic
     * @param int $topicId
     * @return array
     */
    public function getByTopic($topicId) {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table} 
            WHERE topic_id = :topic_id AND is_active = 1 
            ORDER BY sort_order ASC
        ");
        $stmt->execute(['topic_id' => $topicId]);
        return $stmt->fetchAll();
    }

    /**
     * Lấy bài học kèm nội dung
     * @param int $id
     * @return array|false
     */
    public function getWithContents($id) {
        $lesson = $this->find($id);
        if (!$lesson) return false;

        $stmt = $this->db->prepare("
            SELECT * FROM lesson_contents 
            WHERE lesson_id = :lesson_id 
            ORDER BY sort_order ASC
        ");
        $stmt->execute(['lesson_id' => $id]);
        $lesson['contents'] = $stmt->fetchAll();

        return $lesson;
    }
}
