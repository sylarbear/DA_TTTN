-- ============================================
-- Migration v12: lesson_progress — theo dõi hoàn thành bài học
-- Mục đích: Thay thế session-based tracking bằng DB persistent
-- ============================================

CREATE TABLE IF NOT EXISTS `lesson_progress` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `lesson_id` INT NOT NULL,
    `course_id` INT NOT NULL,
    `completed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `unique_user_lesson` (`user_id`, `lesson_id`),
    INDEX `idx_user_course` (`user_id`, `course_id`),
    INDEX `idx_lesson` (`lesson_id`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`lesson_id`) REFERENCES `lessons`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Thêm cột estimated_minutes vào lessons (cho hiển thị thời lượng)
ALTER TABLE `lessons`
    ADD COLUMN `estimated_minutes` INT DEFAULT 10 COMMENT 'Thời gian ước tính (phút)';
