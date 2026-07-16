-- ============================================
-- Migration v13: Chi tiết khóa học (Coursera-style)
-- Mô tả: Thêm objectives, requirements, skill weights, target audience
-- ============================================

ALTER TABLE `courses`
    ADD COLUMN `subtitle` VARCHAR(300) DEFAULT NULL COMMENT 'Dòng mô tả ngắn dưới tên khóa',
    ADD COLUMN `objectives` TEXT DEFAULT NULL COMMENT 'Mục tiêu khóa học (bullet points, phân cách bởi newline)',
    ADD COLUMN `requirements` TEXT DEFAULT NULL COMMENT 'Yêu cầu đầu vào (bullet points)',
    ADD COLUMN `target_audience` VARCHAR(255) DEFAULT NULL COMMENT 'Đối tượng phù hợp',
    ADD COLUMN `listening_weight` TINYINT DEFAULT 25 COMMENT 'Trọng số kỹ năng Nghe (%)',
    ADD COLUMN `speaking_weight` TINYINT DEFAULT 25 COMMENT 'Trọng số kỹ năng Nói (%)',
    ADD COLUMN `reading_weight` TINYINT DEFAULT 25 COMMENT 'Trọng số kỹ năng Đọc (%)',
    ADD COLUMN `writing_weight` TINYINT DEFAULT 25 COMMENT 'Trọng số kỹ năng Viết (%)';
