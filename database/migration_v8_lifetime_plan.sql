-- ============================================
-- Migration v8: Lifetime Plan + Cleanup
-- Xóa gói Pro 12 tháng 400k, thêm gói Lifetime 1tr2
-- ============================================

-- 1. Chuyển activation codes từ plan 3 sang plan 4
UPDATE activation_codes SET plan_id = 4 WHERE plan_id = 3;

-- 2. Xóa gói Pro 12 tháng 400k (id=3)
DELETE FROM membership_plans WHERE id = 3;

-- 3. Thêm gói Pro Lifetime (duration_months = -1 nghĩa là trọn đời)
INSERT INTO `membership_plans` (`id`, `name`, `duration_months`, `price`, `description`, `features`, `is_popular`, `created_at`) VALUES
(5, 'Pro Lifetime', -1, 1200000, 'Trọn đời - Học không giới hạn thời gian', 'Mở khóa tất cả khóa học|Luyện nói với AI chấm điểm|Bài test Listening và Reading|Theo dõi tiến độ học tập|Ưu tiên hỗ trợ|Cập nhật nội dung mới vĩnh viễn', 1, NOW());
