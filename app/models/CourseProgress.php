<?php

/**
 * CourseProgress Model
 * Quản lý tiến độ khóa học của user: lock/unlock/complete/mastered
 */
class CourseProgress extends Model
{
    protected $table = 'course_progress';

    /**
     * Khởi tạo course_progress cho user sau khi có placement result
     * Gọi 1 lần duy nhất từ Placement::finalizeSession / markAsBeginner
     */
    public static function initializeForUser(int $userId, string $cefrLevel): void
    {
        $db = getDB();

        // Kiểm tra xem đã khởi tạo chưa
        $stmt = $db->prepare('SELECT COUNT(*) as cnt FROM course_progress WHERE user_id = :uid');
        $stmt->execute(['uid' => $userId]);
        if ($stmt->fetch()['cnt'] > 0) return; // Đã khởi tạo rồi, bỏ qua

        // Lấy tất cả khóa active, sắp xếp theo CEFR + sort_order
        $stmt = $db->query(
            'SELECT id, cefr_level, sort_order FROM courses WHERE is_active = 1 ORDER BY FIELD(cefr_level, "A1","A2","B1","B2","C1"), sort_order'
        );
        $courses = $stmt->fetchAll();

        $cefrOrder = ['A1' => 0, 'A2' => 1, 'B1' => 2, 'B2' => 3, 'C1' => 4];
        $userLevel = $cefrOrder[$cefrLevel] ?? 0;

        $insertStmt = $db->prepare(
            'INSERT INTO course_progress (user_id, course_id, status, unlocked_at) VALUES (:uid, :cid, :status, :unlocked)'
        );

        foreach ($courses as $course) {
            $courseLevel = $cefrOrder[$course['cefr_level']] ?? 0;

            if ($courseLevel < $userLevel) {
                // Cấp dưới → mastered
                $insertStmt->execute([
                    'uid' => $userId, 'cid' => $course['id'],
                    'status' => 'mastered', 'unlocked' => null,
                ]);
            } elseif ($courseLevel === $userLevel && $course['sort_order'] == 1) {
                // Khóa đầu tiên của cấp hiện tại → unlocked
                $insertStmt->execute([
                    'uid' => $userId, 'cid' => $course['id'],
                    'status' => 'unlocked', 'unlocked' => date('Y-m-d H:i:s'),
                ]);
            } elseif ($courseLevel === $userLevel) {
                // Các khóa còn lại trong cấp → locked
                $insertStmt->execute([
                    'uid' => $userId, 'cid' => $course['id'],
                    'status' => 'locked', 'unlocked' => null,
                ]);
            }
            // Cấp trên → không tạo row (ẩn)
        }
    }

    /**
     * Lấy tất cả khóa của user (có progress)
     */
    public function getUserCourses(int $userId): array
    {
        $stmt = $this->db->prepare(
            'SELECT c.*, cp.status, cp.unlocked_at, cp.completed_at
             FROM courses c
             INNER JOIN course_progress cp ON cp.course_id = c.id
             WHERE cp.user_id = :uid
             ORDER BY FIELD(c.cefr_level, "A1","A2","B1","B2","C1"), c.sort_order ASC'
        );
        $stmt->execute(['uid' => $userId]);
        return $stmt->fetchAll();
    }

    /**
     * Bắt đầu 1 khóa (chuyển unlocked → in_progress)
     */
    public function startCourse(int $userId, int $courseId): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE course_progress SET status = :new_status WHERE user_id = :uid AND course_id = :cid AND status = :cur_status'
        );
        return $stmt->execute([
            'new_status' => 'in_progress', 'uid' => $userId,
            'cid' => $courseId, 'cur_status' => 'unlocked',
        ]) && $stmt->rowCount() > 0;
    }

    /**
     * Hoàn thành 1 khóa (sau khi pass final exam)
     */
    public function completeCourse(int $userId, int $courseId): array
    {
        $db = $this->db;

        // Update khóa hiện tại → completed
        $stmt = $db->prepare(
            'UPDATE course_progress SET status = :s, completed_at = NOW() WHERE user_id = :uid AND course_id = :cid'
        );
        $stmt->execute(['s' => 'completed', 'uid' => $userId, 'cid' => $courseId]);

        // Lấy khóa hiện tại để biết cefr_level + sort_order
        $stmt = $db->prepare(
            'SELECT c.cefr_level, c.sort_order FROM courses c WHERE c.id = :cid'
        );
        $stmt->execute(['cid' => $courseId]);
        $currentCourse = $stmt->fetch();

        $unlockedNext = null;
        $certificate = null;

        // Tìm khóa tiếp theo cùng cấp
        $stmt = $db->prepare(
            'SELECT c.id, c.title FROM courses c
             INNER JOIN course_progress cp ON cp.course_id = c.id
             WHERE cp.user_id = :uid AND c.cefr_level = :level AND c.sort_order > :sort
             ORDER BY c.sort_order ASC LIMIT 1'
        );
        $stmt->execute(['uid' => $userId, 'level' => $currentCourse['cefr_level'], 'sort' => $currentCourse['sort_order']]);
        $nextCourse = $stmt->fetch();

        if ($nextCourse) {
            // Unlock khóa tiếp theo cùng cấp
            $db->prepare(
                'UPDATE course_progress SET status = :s, unlocked_at = NOW() WHERE user_id = :uid AND course_id = :cid'
            )->execute(['s' => 'unlocked', 'uid' => $userId, 'cid' => $nextCourse['id']]);
            $unlockedNext = $nextCourse;
        } else {
            // Đã hoàn thành cả 3 khóa → nhận Certificate + unlock cấp trên
            $certificate = $currentCourse['cefr_level'] . ' Certificate';

            // Thêm badge certificate (nếu chưa có)
            $this->awardCertificateBadge($userId, $currentCourse['cefr_level']);

            // Unlock cấp tiếp theo
            $cefrOrder = ['A1' => 0, 'A2' => 1, 'B1' => 2, 'B2' => 3, 'C1' => 4];
            $currentLevel = $cefrOrder[$currentCourse['cefr_level']] ?? 0;
            $nextLevels = array_flip($cefrOrder);

            if ($currentLevel < 4 && isset($nextLevels[$currentLevel + 1])) {
                $nextLevel = $nextLevels[$currentLevel + 1];

                // Tạo course_progress cho cấp trên nếu chưa có, khóa đầu unlocked
                $stmt = $db->prepare(
                    'SELECT c.id FROM courses c
                     WHERE c.cefr_level = :level AND c.sort_order = 1 AND c.is_active = 1'
                );
                $stmt->execute(['level' => $nextLevel]);
                $firstCourseNextLevel = $stmt->fetch();

                if ($firstCourseNextLevel) {
                    // Kiểm tra xem đã có row chưa
                    $check = $db->prepare('SELECT id FROM course_progress WHERE user_id = :uid AND course_id = :cid');
                    $check->execute(['uid' => $userId, 'cid' => $firstCourseNextLevel['id']]);

                    if (!$check->fetch()) {
                        // Tạo row với status unlocked
                        $db->prepare(
                            'INSERT INTO course_progress (user_id, course_id, status, unlocked_at) VALUES (:uid, :cid, :s, NOW())'
                        )->execute(['uid' => $userId, 'cid' => $firstCourseNextLevel['id'], 's' => 'unlocked']);

                        // Các khóa còn lại của cấp trên → locked
                        $db->prepare(
                            'INSERT INTO course_progress (user_id, course_id, status)
                             SELECT :uid, c.id, :s
                             FROM courses c
                             WHERE c.cefr_level = :level AND c.sort_order > 1 AND c.is_active = 1
                             AND NOT EXISTS (SELECT 1 FROM course_progress cp WHERE cp.user_id = :uid2 AND cp.course_id = c.id)'
                        )->execute(['uid' => $userId, 's' => 'locked', 'level' => $nextLevel, 'uid2' => $userId]);
                    }
                }
            }
        }

        return [
            'completed' => true,
            'unlocked_next' => $unlockedNext,
            'certificate' => $certificate,
        ];
    }

    /**
     * Hàm helper: thưởng Certificate badge
     */
    private function awardCertificateBadge(int $userId, string $cefr): void
    {
        // Ghi vào xp_history như 1 achievement
        $db = $this->db;
        $xpAmount = ['A1' => 50, 'A2' => 100, 'B1' => 200, 'B2' => 300, 'C1' => 500][$cefr] ?? 50;

        $stmt = $db->prepare(
            "INSERT INTO xp_history (user_id, xp_amount, activity_type, description)
             VALUES (:uid, :xp, 'certificate_earned', :desc)"
        );
        $stmt->execute([
            'uid' => $userId, 'xp' => $xpAmount,
            'desc' => "Dat chung chi $cefr Certificate — Hoan thanh toan bo $cefr Learning Path",
        ]);
    }

    /**
     * Kiểm tra user đã có course_progress chưa (nếu chưa → cần làm placement)
     */
    public static function hasProgress(int $userId): bool
    {
        $db = getDB();
        $stmt = $db->prepare('SELECT COUNT(*) as cnt FROM course_progress WHERE user_id = :uid');
        $stmt->execute(['uid' => $userId]);
        return $stmt->fetch()['cnt'] > 0;
    }
}
