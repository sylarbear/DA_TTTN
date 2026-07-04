-- ============================================
-- Migration v11: Final Exam Upgrade — 20 cau Reading + Listening
-- ============================================

ALTER TABLE `tests`
    ADD COLUMN `reading_passage` TEXT DEFAULT NULL COMMENT 'Đoạn văn cho phần Reading',
    ADD COLUMN `listening_transcript` TEXT DEFAULT NULL COMMENT 'Transcript cho phần Listening (TTS đọc)',
    ADD COLUMN `has_sections` TINYINT(1) DEFAULT 0 COMMENT '1 = Final exam dang 2 section';
