<?php
/**
 * UserAnswer Model
 * Lưu bài làm chi tiết của user
 */
class UserAnswer extends Model {
    protected $table = 'user_answers';

    /**
     * Lưu kết quả test + chi tiết từng câu
     * @param int $userId
     * @param int $testId
     * @param array $answers [question_id => user_answer]
     * @param int $timeSpent Thời gian (giây)
     * @return array Kết quả
     */
    public function submitTest($userId, $testId, $answers, $timeSpent = 0) {
        // Lấy tất cả câu hỏi của test
        $stmt = $this->db->prepare("SELECT * FROM questions WHERE test_id = :test_id ORDER BY sort_order ASC");
        $stmt->execute(['test_id' => $testId]);
        $questions = $stmt->fetchAll();

        $score = 0;
        $totalPoints = 0;
        $details = [];

        foreach ($questions as $q) {
            $totalPoints += $q['points'];
            $userAnswer = $answers[$q['id']] ?? '';
            
            // Normalize correct_answer for object-format options {"A":"..","B":".."}
            $correctAnswer = $q['correct_answer'];
            if ($q['options_json']) {
                $parsed = json_decode($q['options_json'], true);
                if ($parsed && isset($parsed['A']) && isset($parsed[$correctAnswer])) {
                    $correctAnswer = $parsed[$correctAnswer];
                }
            }
            
            // So sánh đáp án (không phân biệt hoa thường cho fill_blank)
            $isCorrect = false;
            if ($q['question_type'] === 'fill_blank') {
                $isCorrect = strtolower(trim($userAnswer)) === strtolower(trim($correctAnswer));
            } else {
                $isCorrect = trim($userAnswer) === trim($correctAnswer);
            }

            if ($isCorrect) {
                $score += $q['points'];
            }

            $details[] = [
                'question_id' => $q['id'],
                'user_answer' => $userAnswer,
                'is_correct'  => $isCorrect ? 1 : 0
            ];
        }

        // Lưu test_result
        $stmt = $this->db->prepare("
            INSERT INTO test_results (user_id, test_id, score, total_points, time_spent)
            VALUES (:user_id, :test_id, :score, :total_points, :time_spent)
        ");
        $stmt->execute([
            'user_id'      => $userId,
            'test_id'      => $testId,
            'score'        => $score,
            'total_points' => $totalPoints,
            'time_spent'   => $timeSpent
        ]);
        $resultId = $this->db->lastInsertId();

        // Award XP for completing test (only if score >= 30%)
        $percentage = $totalPoints > 0 ? round(($score / $totalPoints) * 100) : 0;
        require_once APP_PATH . '/core/StreakService.php';
        StreakService::updateStreak($userId);
        if ($percentage >= 30) {
            StreakService::addXP($userId, 50, 'test_complete', "Hoàn thành bài test ({$percentage}%)");
        }

        // Lưu chi tiết từng câu
        $stmt = $this->db->prepare("
            INSERT INTO user_answers (test_result_id, question_id, user_answer, is_correct)
            VALUES (:result_id, :question_id, :user_answer, :is_correct)
        ");
        foreach ($details as $d) {
            $stmt->execute([
                'result_id'   => $resultId,
                'question_id' => $d['question_id'],
                'user_answer' => $d['user_answer'],
                'is_correct'  => $d['is_correct']
            ]);
        }

        return [
            'result_id'    => $resultId,
            'score'        => $score,
            'total_points' => $totalPoints,
            'percentage'   => $totalPoints > 0 ? round(($score / $totalPoints) * 100) : 0,
            'details'      => $details
        ];
    }

    /**
     * Lấy chi tiết kết quả test
     * @param int $resultId
     * @return array
     */
    public function getResultDetails($resultId) {
        $stmt = $this->db->prepare("
            SELECT ua.*, q.question_text, q.question_type, q.options_json, 
                   q.correct_answer, q.audio_url, q.passage, q.points
            FROM user_answers ua
            JOIN questions q ON ua.question_id = q.id
            WHERE ua.test_result_id = :result_id
            ORDER BY q.sort_order ASC
        ");
        $stmt->execute(['result_id' => $resultId]);
        $details = $stmt->fetchAll();

        foreach ($details as &$d) {
            if ($d['options_json']) {
                $parsed = json_decode($d['options_json'], true);
                // Normalize object format and resolve correct_answer
                if ($parsed && isset($parsed['A'])) {
                    if (isset($parsed[$d['correct_answer']])) {
                        $d['correct_answer'] = $parsed[$d['correct_answer']];
                    }
                    $d['options'] = array_values($parsed);
                } else {
                    $d['options'] = $parsed ?: [];
                }
            }
        }

        return $details;
    }
}
