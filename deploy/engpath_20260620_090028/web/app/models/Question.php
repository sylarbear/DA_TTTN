<?php
/**
 * Question Model
 * Quản lý câu hỏi
 */
class Question extends Model {
    protected $table = 'questions';

    /**
     * Lấy câu hỏi theo test
     * @param int $testId
     * @return array
     */
    public function getByTest($testId) {
        $questions = $this->where('test_id', $testId, 'sort_order ASC');
        foreach ($questions as &$q) {
            if ($q['options_json']) {
                $q['options'] = json_decode($q['options_json'], true);
            } else {
                $q['options'] = [];
            }
        }
        return $questions;
    }
}
