-- =============================================
-- Migration: Features 9-14 (Streak, XP, Bookmark, Grammar, etc.)
-- =============================================

-- 1. User Streaks & XP
ALTER TABLE users ADD COLUMN IF NOT EXISTS current_streak INT DEFAULT 0;
ALTER TABLE users ADD COLUMN IF NOT EXISTS longest_streak INT DEFAULT 0;
ALTER TABLE users ADD COLUMN IF NOT EXISTS last_activity_date DATE DEFAULT NULL;
ALTER TABLE users ADD COLUMN IF NOT EXISTS total_xp INT DEFAULT 0;
ALTER TABLE users ADD COLUMN IF NOT EXISTS level INT DEFAULT 1;
ALTER TABLE users ADD COLUMN IF NOT EXISTS daily_goal INT DEFAULT 20;
ALTER TABLE users ADD COLUMN IF NOT EXISTS daily_xp_today INT DEFAULT 0;
ALTER TABLE users ADD COLUMN IF NOT EXISTS daily_goal_date DATE DEFAULT NULL;

-- 2. XP History
CREATE TABLE IF NOT EXISTS xp_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    xp_amount INT NOT NULL,
    activity_type ENUM('vocab_learn','test_complete','speaking_practice','flashcard','lesson_complete','login_bonus','streak_bonus') NOT NULL,
    description VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_date (user_id, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Vocabulary Reviews (Spaced Repetition)
CREATE TABLE IF NOT EXISTS vocab_reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    vocabulary_id INT NOT NULL,
    ease_factor FLOAT DEFAULT 2.5,
    interval_days INT DEFAULT 1,
    repetitions INT DEFAULT 0,
    next_review DATE NOT NULL,
    last_reviewed TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (vocabulary_id) REFERENCES vocabularies(id) ON DELETE CASCADE,
    UNIQUE KEY uk_user_vocab (user_id, vocabulary_id),
    INDEX idx_next_review (user_id, next_review)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Bookmarks
CREATE TABLE IF NOT EXISTS bookmarks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    vocabulary_id INT NOT NULL,
    note TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (vocabulary_id) REFERENCES vocabularies(id) ON DELETE CASCADE,
    UNIQUE KEY uk_user_bookmark (user_id, vocabulary_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Grammar Lessons
CREATE TABLE IF NOT EXISTS grammar_lessons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    category ENUM('tense','preposition','clause','article','modal','other') DEFAULT 'other',
    level ENUM('beginner','intermediate','advanced') DEFAULT 'beginner',
    content_html TEXT NOT NULL,
    examples TEXT,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Grammar Quiz Questions
CREATE TABLE IF NOT EXISTS grammar_questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    grammar_lesson_id INT NOT NULL,
    question_text VARCHAR(500) NOT NULL,
    options JSON NOT NULL,
    correct_answer CHAR(1) NOT NULL,
    explanation TEXT,
    FOREIGN KEY (grammar_lesson_id) REFERENCES grammar_lessons(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed grammar lessons
INSERT INTO grammar_lessons (title, slug, category, level, content_html, examples, sort_order) VALUES
('Present Simple', 'present-simple', 'tense', 'beginner',
 '<h3>Cấu trúc</h3><p><strong>S + V(s/es)</strong> (khẳng định)</p><p><strong>S + do/does + not + V</strong> (phủ định)</p><p><strong>Do/Does + S + V?</strong> (nghi vấn)</p><h3>Cách dùng</h3><ul><li>Thói quen hàng ngày: <em>I go to school every day.</em></li><li>Sự thật hiển nhiên: <em>The sun rises in the east.</em></li><li>Lịch trình cố định: <em>The train leaves at 9 AM.</em></li></ul><h3>Dấu hiệu</h3><p>always, usually, often, sometimes, rarely, never, every day/week/month</p>',
 'I play football every Sunday.\nShe doesn''t like coffee.\nDo you speak English?', 1),

('Present Continuous', 'present-continuous', 'tense', 'beginner',
 '<h3>Cấu trúc</h3><p><strong>S + am/is/are + V-ing</strong></p><h3>Cách dùng</h3><ul><li>Hành động đang xảy ra: <em>I am studying now.</em></li><li>Kế hoạch tương lai gần: <em>We are meeting tomorrow.</em></li><li>Thay đổi, xu hướng: <em>The weather is getting colder.</em></li></ul><h3>Dấu hiệu</h3><p>now, right now, at the moment, currently, Look! Listen!</p>',
 'She is reading a book right now.\nThey are not playing football.\nAre you listening to me?', 2),

('Present Perfect', 'present-perfect', 'tense', 'intermediate',
 '<h3>Cấu trúc</h3><p><strong>S + have/has + V3 (past participle)</strong></p><h3>Cách dùng</h3><ul><li>Kinh nghiệm: <em>I have visited Paris twice.</em></li><li>Hành động vừa hoàn thành: <em>She has just finished her homework.</em></li><li>Hành động bắt đầu trong quá khứ, kéo dài đến hiện tại: <em>We have lived here since 2010.</em></li></ul><h3>Dấu hiệu</h3><p>since, for, already, yet, just, ever, never, recently</p>',
 'I have never seen snow.\nHe has already eaten lunch.\nHave you ever been to Japan?', 3),

('Past Simple', 'past-simple', 'tense', 'beginner',
 '<h3>Cấu trúc</h3><p><strong>S + V2 (past tense)</strong> (khẳng định)</p><p><strong>S + did + not + V</strong> (phủ định)</p><h3>Cách dùng</h3><ul><li>Hành động đã hoàn thành: <em>I went to school yesterday.</em></li><li>Thói quen trong quá khứ: <em>She always walked to work.</em></li></ul><h3>Dấu hiệu</h3><p>yesterday, last week/month/year, ago, in 2020</p>',
 'They played tennis last weekend.\nShe didn''t go to the party.\nDid you see the movie?', 4),

('Prepositions of Time', 'prepositions-time', 'preposition', 'beginner',
 '<h3>IN / ON / AT</h3><ul><li><strong>IN</strong>: tháng, năm, mùa, buổi → <em>in January, in 2024, in summer, in the morning</em></li><li><strong>ON</strong>: ngày, thứ → <em>on Monday, on July 4th, on my birthday</em></li><li><strong>AT</strong>: giờ, thời điểm cụ thể → <em>at 9 AM, at noon, at night, at the weekend</em></li></ul>',
 'I wake up at 7 AM.\nThe meeting is on Friday.\nShe was born in 1995.', 5),

('Modal Verbs', 'modal-verbs', 'modal', 'intermediate',
 '<h3>Can / Could / Should / Must / May / Might</h3><ul><li><strong>Can</strong>: khả năng → <em>I can swim.</em></li><li><strong>Could</strong>: khả năng (quá khứ/lịch sự) → <em>Could you help me?</em></li><li><strong>Should</strong>: lời khuyên → <em>You should study harder.</em></li><li><strong>Must</strong>: bắt buộc → <em>You must wear a seatbelt.</em></li><li><strong>May/Might</strong>: khả năng xảy ra → <em>It might rain tomorrow.</em></li></ul>',
 'Can you speak French?\nYou should see a doctor.\nStudents must not use phones in class.', 6);

-- Seed grammar questions
INSERT INTO grammar_questions (grammar_lesson_id, question_text, options, correct_answer, explanation) VALUES
(1, 'She ___ to school every day.', '{"A":"go","B":"goes","C":"going","D":"went"}', 'B', 'Chủ ngữ "She" (ngôi 3 số ít) → V thêm s/es'),
(1, '___ you like coffee?', '{"A":"Does","B":"Do","C":"Is","D":"Are"}', 'B', '"You" dùng trợ động từ "Do"'),
(2, 'Look! The children ___ in the park.', '{"A":"play","B":"plays","C":"are playing","D":"played"}', 'C', '"Look!" → hành động đang xảy ra → Present Continuous'),
(2, 'She ___ a letter right now.', '{"A":"writes","B":"is writing","C":"wrote","D":"has written"}', 'B', '"right now" → Present Continuous'),
(3, 'I ___ to Paris three times.', '{"A":"go","B":"went","C":"have been","D":"am going"}', 'C', 'Kinh nghiệm → Present Perfect'),
(3, 'She ___ here since 2015.', '{"A":"lives","B":"lived","C":"has lived","D":"is living"}', 'C', '"since 2015" → Present Perfect'),
(4, 'They ___ football yesterday.', '{"A":"play","B":"plays","C":"played","D":"have played"}', 'C', '"yesterday" → Past Simple'),
(5, 'The class starts ___ 8 AM.', '{"A":"in","B":"on","C":"at","D":"by"}', 'C', 'Giờ cụ thể → at'),
(5, 'We go swimming ___ summer.', '{"A":"in","B":"on","C":"at","D":"by"}', 'A', 'Mùa → in'),
(6, 'You ___ wear a helmet when cycling.', '{"A":"can","B":"may","C":"should","D":"might"}', 'C', 'Lời khuyên → should');
