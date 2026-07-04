-- ============================================
-- Migration v10: Course-based Learning (Khóa học)
-- Mô tả: Tổ chức nội dung theo khóa học, liên kết placement CEFR
-- ============================================

-- ============================================
-- 1. BẢNG COURSES - Khóa học
-- ============================================
CREATE TABLE IF NOT EXISTS `courses` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(200) NOT NULL,
    `slug` VARCHAR(100) UNIQUE,
    `cefr_level` ENUM('A1','A2','B1','B2','C1') NOT NULL,
    `description` TEXT DEFAULT NULL,
    `thumbnail` VARCHAR(255) DEFAULT NULL,
    `sort_order` INT DEFAULT 0 COMMENT 'Thứ tự trong cùng 1 cấp CEFR (1,2,3)',
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX `idx_cefr` (`cefr_level`),
    INDEX `idx_sort` (`cefr_level`, `sort_order`),
    INDEX `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 2. THÊM course_id VÀO TOPICS
-- ============================================
ALTER TABLE `topics`
    ADD COLUMN `course_id` INT DEFAULT NULL,
    ADD INDEX `idx_course` (`course_id`);

-- ============================================
-- 3. THÊM is_final VÀO TESTS
-- ============================================
ALTER TABLE `tests`
    ADD COLUMN `is_final` TINYINT(1) DEFAULT 0 COMMENT '1 = Bài thi cuối khóa';

-- ============================================
-- 4. BẢNG COURSE_PROGRESS - Tiến độ khóa học của user
-- ============================================
CREATE TABLE IF NOT EXISTS `course_progress` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `course_id` INT NOT NULL,
    `status` ENUM('locked','unlocked','in_progress','completed','mastered') NOT NULL DEFAULT 'locked',
    `unlocked_at` TIMESTAMP NULL DEFAULT NULL,
    `completed_at` TIMESTAMP NULL DEFAULT NULL,

    UNIQUE KEY `unique_user_course` (`user_id`, `course_id`),
    INDEX `idx_user_status` (`user_id`, `status`),
    INDEX `idx_course` (`course_id`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
