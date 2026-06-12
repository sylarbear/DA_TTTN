SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

UPDATE topics
SET description = 'Từ vựng và hội thoại về cuộc sống hằng ngày: chào hỏi, giới thiệu bản thân, thói quen hằng ngày.'
WHERE slug = 'daily-life';

UPDATE topics
SET description = 'Học tiếng Anh giao tiếp khi đi du lịch: đặt vé, khách sạn, hỏi đường, mua sắm.'
WHERE slug = 'travel-tourism';

UPDATE topics
SET description = 'Từ vựng về đồ ăn, nấu ăn, gọi món tại nhà hàng và công thức nấu ăn.'
WHERE slug = 'food-cooking';

UPDATE topics
SET description = 'Tiếng Anh trong môi trường học tập: lớp học, trường đại học, học online.'
WHERE slug = 'education';

UPDATE topics
SET description = 'Từ vựng về công nghệ, máy tính, internet và các thuật ngữ IT phổ biến.'
WHERE slug = 'technology';

UPDATE topics
SET description = 'Tiếng Anh về sức khỏe, bệnh viện, tập gym và dinh dưỡng.'
WHERE slug = 'health-fitness';
