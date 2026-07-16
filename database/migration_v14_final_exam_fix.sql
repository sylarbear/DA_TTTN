-- ============================================
-- Migration v14: Fix Final Exam — course_id linking
-- ============================================

-- 1. Thêm course_id vào tests
ALTER TABLE `tests`
    ADD COLUMN `course_id` INT DEFAULT NULL COMMENT 'FK to courses (for final exams)',
    ADD INDEX `idx_course` (`course_id`);

-- 2. UPDATE: gán course_id dựa trên title matching
-- Final exams: title = 'Final Exam: English Starter 1' → course title = 'English Starter 1'
UPDATE `tests` t
JOIN `courses` c ON c.title = REPLACE(t.title, 'Final Exam: ', '')
SET t.course_id = c.id
WHERE t.is_final = 1 AND t.title LIKE 'Final Exam:%';

-- Verify
SELECT t.id, t.title, t.course_id, c.title as course_title, c.cefr_level
FROM tests t
LEFT JOIN courses c ON t.course_id = c.id
WHERE t.is_final = 1
ORDER BY t.id;
