<?php

/**
 * Course Model
 * Quản lý khóa học — thay thế topics làm đơn vị tổ chức nội dung chính
 */
class Course extends Model
{
    protected $table = 'courses';

    /**
     * Lấy tất cả khóa active theo cấp CEFR
     */
    public function getByCefrLevel(string $cefr): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM courses WHERE cefr_level = :level AND is_active = 1 ORDER BY sort_order ASC'
        );
        $stmt->execute(['level' => $cefr]);
        return $stmt->fetchAll();
    }

    /**
     * Lấy tất cả khóa active, grouped by CEFR
     */
    public function getAllGrouped(): array
    {
        $stmt = $this->db->query(
            'SELECT * FROM courses WHERE is_active = 1 ORDER BY FIELD(cefr_level, "A1","A2","B1","B2","C1"), sort_order ASC'
        );
        $rows = $stmt->fetchAll();

        $grouped = [];
        foreach ($rows as $row) {
            $grouped[$row['cefr_level']][] = $row;
        }
        return $grouped;
    }

    /**
     * Lấy 1 khóa kèm danh sách chương (topics) + bài thi cuối khóa
     */
    public function getWithChapters(int $courseId): ?array
    {
        $course = $this->find($courseId);
        if (!$course) return null;

        // Lấy chapters (topics thuộc khóa này)
        $stmt = $this->db->prepare(
            'SELECT t.*,
                (SELECT COUNT(*) FROM vocabularies v WHERE v.topic_id = t.id) AS vocab_count,
                (SELECT COUNT(*) FROM lessons l WHERE l.topic_id = t.id AND l.is_active = 1) AS lesson_count,
                (SELECT COUNT(*) FROM tests tt WHERE tt.topic_id = t.id AND tt.is_active = 1 AND tt.is_final = 0) AS test_count
             FROM topics t
             WHERE t.course_id = :cid AND t.is_active = 1
             ORDER BY t.sort_order ASC'
        );
        $stmt->execute(['cid' => $courseId]);
        $course['chapters'] = $stmt->fetchAll();

        return $course;
    }

    /**
     * Lấy bài thi cuối khóa (test có is_final=1, topic_id IS NULL)
     */
    public function getFinalExam(int $courseId): ?array
    {
        // Final exam được gán vào course thông qua convention: test liên quan đến course
        // Hiện tại chưa có course_id trực tiếp trong tests, dùng title lookup
        $course = $this->find($courseId);
        if (!$course) return null;

        $stmt = $this->db->prepare(
            'SELECT t.*, COUNT(q.id) AS question_count
             FROM tests t
             LEFT JOIN questions q ON q.test_id = t.id
             WHERE t.is_final = 1 AND t.title LIKE :pattern AND t.is_active = 1
             GROUP BY t.id
             LIMIT 1'
        );
        $stmt->execute(['pattern' => '%' . addcslashes($course['title'], '%_') . '%']);
        return $stmt->fetch() ?: null;
    }

    /**
     * Lấy tiến độ của user trong 1 khóa (từ course_progress)
     */
    public function getUserProgress(int $userId, int $courseId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM course_progress WHERE user_id = :uid AND course_id = :cid'
        );
        $stmt->execute(['uid' => $userId, 'cid' => $courseId]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Kiểm tra xem 1 chương đã hoàn thành chưa
     * Điều kiện: ít nhất 1 bài học đã xem HOẶC ít nhất 1 quiz đã pass
     */
    public function isChapterCompleted(int $userId, int $topicId): bool
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM user_progress WHERE user_id = :uid AND topic_id = :tid'
        );
        $stmt->execute(['uid' => $userId, 'tid' => $topicId]);
        $progress = $stmt->fetch();

        if (!$progress) return false;

        // Hoàn thành nếu đã học ít nhất 1 lesson HOẶC pass ít nhất 1 test
        return ($progress['lessons_completed'] > 0 || $progress['tests_passed'] > 0);
    }

    /**
     * Tính % hoàn thành của khóa
     */
    public function getCompletionPercent(int $userId, int $courseId): int
    {
        $course = $this->getWithChapters($courseId);
        if (!$course || empty($course['chapters'])) return 0;

        $total = count($course['chapters']);
        $completed = 0;

        foreach ($course['chapters'] as $chapter) {
            if ($this->isChapterCompleted($userId, $chapter['id'])) {
                $completed++;
            }
        }

        return $total > 0 ? round($completed / $total * 100) : 0;
    }

    /**
     * Kiểm tra tất cả chương đã hoàn thành → mở final exam
     */
    public function canTakeFinalExam(int $userId, int $courseId): bool
    {
        return $this->getCompletionPercent($userId, $courseId) >= 100;
    }
}
