<?php
/**
 * Vocabulary Model
 * Quản lý từ vựng
 */
class Vocabulary extends Model {
    protected $table = 'vocabularies';

    /**
     * Lấy từ vựng theo topic
     * @param int $topicId
     * @return array
     */
    public function getByTopic($topicId) {
        return $this->where('topic_id', $topicId, 'id ASC');
    }

    /**
     * Tìm kiếm từ vựng
     * @param string $keyword
     * @return array
     */
    public function search($keyword) {
        $escapedKeyword = str_replace(['%', '_'], ['\\%', '\\_'], $keyword);
        $stmt = $this->db->prepare("
            SELECT v.*, t.name as topic_name 
            FROM {$this->table} v
            JOIN topics t ON v.topic_id = t.id
            WHERE v.word LIKE :keyword OR v.meaning_vi LIKE :keyword2
            ORDER BY v.word ASC
            LIMIT 50
        ");
        $stmt->execute([
            'keyword'  => "%{$escapedKeyword}%",
            'keyword2' => "%{$escapedKeyword}%"
        ]);
        return $stmt->fetchAll();
    }
}
