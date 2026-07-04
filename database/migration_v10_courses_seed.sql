-- ============================================
-- Migration v10 Seed: Khóa học + Gán topic + Final tests
-- ============================================

-- ============================================
-- 1. SEED 15 KHÓA HỌC (3 mỗi cấp CEFR)
-- ============================================

-- A1 LEVEL
INSERT INTO `courses` (`title`, `slug`, `cefr_level`, `description`, `sort_order`) VALUES
('English Starter 1', 'english-starter-1', 'A1', 'Lam quen voi tieng Anh: chao hoi, gioi thieu ban than, gia dinh, con so. Danh cho nguoi moi bat dau.', 1),
('English Starter 2', 'english-starter-2', 'A1', 'Mo rong von tu co ban: cuoc song hang ngay, thoi gian, mau sac, mieu ta don gian.', 2),
('English Starter 3', 'english-starter-3', 'A1', 'Hoan thien nen tang A1: do an, mua sam, thoi tiet, hoi thoai ngan.', 3);

-- A2 LEVEL
INSERT INTO `courses` (`title`, `slug`, `cefr_level`, `description`, `sort_order`) VALUES
('English Basic 1', 'english-basic-1', 'A2', 'Giao tiep co ban: du lich, hoi duong, phuong tien giao thong, dat phong khach san.', 1),
('English Basic 2', 'english-basic-2', 'A2', 'So thich va cuoc song: the thao, am nhac, giai tri, ke chuyen don gian.', 2),
('English Basic 3', 'english-basic-3', 'A2', 'Cong viec va hoc tap: nghe nghiep, truong hoc, cong nghe co ban, viet email.', 3);

-- B1 LEVEL
INSERT INTO `courses` (`title`, `slug`, `cefr_level`, `description`, `sort_order`) VALUES
('English Intermediate 1', 'english-intermediate-1', 'B1', 'Giao tiep tu tin: cac moi quan he, suc khoe, moi truong, thao luan y kien.', 1),
('English Intermediate 2', 'english-intermediate-2', 'B1', 'Truyen thong va van hoa: bao chi, mang xa hoi, giao duc, so sanh van hoa.', 2),
('English Intermediate 3', 'english-intermediate-3', 'B1', 'Tieng Anh cong so: kinh doanh co ban, thuyet trinh, viet bao cao ngan.', 3);

-- B2 LEVEL
INSERT INTO `courses` (`title`, `slug`, `cefr_level`, `description`, `sort_order`) VALUES
('English Upper 1', 'english-upper-1', 'B2', 'Ngu phap nang cao: cau phuc, bi dong, cau dieu kien, viet luan ngan.', 1),
('English Upper 2', 'english-upper-2', 'B2', 'Tranh luan va phan bien: thoi su, lich su, kinh te, viet bai luan.', 2),
('English Upper 3', 'english-upper-3', 'B2', 'Giao tiep chuyen nghiep: phong van, dam phan, email cong viec, thuyet trinh nang cao.', 3);

-- C1 LEVEL
INSERT INTO `courses` (`title`, `slug`, `cefr_level`, `description`, `sort_order`) VALUES
('English Advanced 1', 'english-advanced-1', 'C1', 'Viet hoc thuat: nghien cuu, trich dan, lap luan, viet essay chuan academic.', 1),
('English Advanced 2', 'english-advanced-2', 'C1', 'Van hoc va nghe thuat: phan tich tac pham, phe binh, triet hoc, sang tao.', 2),
('English Advanced 3', 'english-advanced-3', 'C1', 'Thanh thao toan dien: sac thai ngon ngu, idiom, tranh luan hoc thuat, viet bao.', 3);

-- ============================================
-- 2. GAN TOPICS HIEN CO VAO KHOA
-- ============================================
UPDATE `topics` SET `course_id` = 1  WHERE `id` = 1;
UPDATE `topics` SET `course_id` = 4  WHERE `id` = 2;
UPDATE `topics` SET `course_id` = 3  WHERE `id` = 3;
UPDATE `topics` SET `course_id` = 6  WHERE `id` = 4;
UPDATE `topics` SET `course_id` = 9  WHERE `id` = 5;
UPDATE `topics` SET `course_id` = 5  WHERE `id` = 6;

-- ============================================
-- 3. TAO BAI THI CUOI KHOA (is_final=1, chua co cau hoi)
-- ============================================
INSERT INTO `tests` (`topic_id`, `title`, `test_type`, `duration_minutes`, `pass_score`, `is_final`) VALUES
(1, 'Final Exam: English Starter 1', 'quiz', 20, 70, 1),
(1, 'Final Exam: English Starter 2', 'quiz', 20, 70, 1),
(3, 'Final Exam: English Starter 3', 'quiz', 20, 70, 1),
(2, 'Final Exam: English Basic 1', 'quiz', 25, 70, 1),
(6, 'Final Exam: English Basic 2', 'quiz', 25, 70, 1),
(4, 'Final Exam: English Basic 3', 'quiz', 25, 70, 1),
(5, 'Final Exam: English Intermediate 1', 'quiz', 30, 70, 1),
(5, 'Final Exam: English Intermediate 2', 'quiz', 30, 70, 1),
(5, 'Final Exam: English Intermediate 3', 'quiz', 30, 70, 1),
(5, 'Final Exam: English Upper 1', 'quiz', 30, 70, 1),
(5, 'Final Exam: English Upper 2', 'quiz', 30, 70, 1),
(5, 'Final Exam: English Upper 3', 'quiz', 30, 70, 1),
(5, 'Final Exam: English Advanced 1', 'quiz', 35, 70, 1),
(5, 'Final Exam: English Advanced 2', 'quiz', 35, 70, 1),
(5, 'Final Exam: English Advanced 3', 'quiz', 35, 70, 1);
