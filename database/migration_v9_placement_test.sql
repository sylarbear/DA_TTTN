-- ============================================
-- Migration v9: Placement Test (Bài kiểm tra đầu vào)
-- Mô tả: Thêm chức năng placement test kiểu Duolingo
-- ============================================

-- ============================================
-- 1. BẢNG PLACEMENT_QUESTIONS
-- ============================================
CREATE TABLE IF NOT EXISTS `placement_questions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `question_text` TEXT NOT NULL,
    `question_type` ENUM('multiple_choice','true_false','fill_blank') NOT NULL DEFAULT 'multiple_choice',
    `options_json` JSON DEFAULT NULL,
    `correct_answer` VARCHAR(255) NOT NULL,
    `cefr_level` ENUM('A1','A2','B1','B2','C1') NOT NULL,
    `skill_type` ENUM('vocabulary','grammar','reading','listening') NOT NULL DEFAULT 'vocabulary',
    `difficulty_weight` FLOAT DEFAULT 1.0,
    `audio_url` VARCHAR(255) DEFAULT NULL,
    `passage` TEXT DEFAULT NULL,
    `explanation` TEXT DEFAULT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_cefr` (`cefr_level`),
    INDEX `idx_skill` (`skill_type`),
    INDEX `idx_active` (`is_active`),
    INDEX `idx_cefr_skill` (`cefr_level`, `skill_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 2. BẢNG PLACEMENT_SESSIONS
-- ============================================
CREATE TABLE IF NOT EXISTS `placement_sessions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `status` ENUM('in_progress','completed','abandoned') NOT NULL DEFAULT 'in_progress',
    `initial_theta` FLOAT NOT NULL,
    `current_theta` FLOAT NOT NULL,
    `questions_answered` INT DEFAULT 0,
    `correct_count` INT DEFAULT 0,
    `total_questions` INT DEFAULT NULL,
    `final_cefr` ENUM('A1','A2','B1','B2','C1') DEFAULT NULL,
    `final_theta` FLOAT DEFAULT NULL,
    `confidence_score` FLOAT DEFAULT NULL,
    `started_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `completed_at` TIMESTAMP NULL DEFAULT NULL,
    INDEX `idx_user` (`user_id`),
    INDEX `idx_status` (`status`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 3. BẢNG PLACEMENT_RESPONSES
-- ============================================
CREATE TABLE IF NOT EXISTS `placement_responses` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `session_id` INT NOT NULL,
    `question_id` INT NOT NULL,
    `user_answer` TEXT DEFAULT NULL,
    `is_correct` TINYINT(1) NOT NULL DEFAULT 0,
    `theta_before` FLOAT DEFAULT NULL,
    `theta_after` FLOAT DEFAULT NULL,
    `response_time_ms` INT DEFAULT NULL,
    `question_order` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_session` (`session_id`),
    FOREIGN KEY (`session_id`) REFERENCES `placement_sessions`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`question_id`) REFERENCES `placement_questions`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 4. THÊM CỘT CHO BẢNG USERS
-- ============================================
ALTER TABLE `users`
    ADD COLUMN `placement_level` ENUM('A1','A2','B1','B2','C1') DEFAULT NULL,
    ADD COLUMN `placement_completed_at` DATETIME DEFAULT NULL,
    ADD COLUMN `placement_session_id` INT DEFAULT NULL;

-- ============================================
-- 5. MỞ RỘNG ENUM XP_HISTORY
-- ============================================
ALTER TABLE `xp_history`
    MODIFY COLUMN `activity_type` ENUM('vocab_learn','test_complete','speaking_practice','flashcard','lesson_complete','login_bonus','streak_bonus','placement_test') NOT NULL;
