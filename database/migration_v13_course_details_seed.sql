-- ============================================
-- Migration v13 Seed: Dữ liệu chi tiết cho 15 khóa học
-- Mô tả: Cập nhật objectives, requirements, skill weights, target audience
-- ============================================

-- ============================================
-- A1 LEVEL (Cơ bản - tập trung Nghe 35%, Nói 30%)
-- ============================================
UPDATE `courses` SET
  `subtitle` = 'Khóa học tiếng Anh cho người mới bắt đầu',
  `objectives` = 'Chào hỏi và giới thiệu bản thân bằng tiếng Anh\nGiao tiếp cơ bản về gia đình, công việc hàng ngày\nĐếm số, nói ngày tháng, thời gian\nHỏi và trả lời các câu hỏi đơn giản\nĐọc hiểu các đoạn văn ngắn về chủ đề quen thuộc\nViết câu đơn về bản thân và gia đình',
  `requirements` = 'Không yêu cầu kiến thức tiếng Anh trước đó\nCó thiết bị kết nối internet (máy tính, điện thoại, máy tính bảng)\nTinh thần ham học hỏi và kiên trì luyện tập',
  `target_audience` = 'Người mới bắt đầu học tiếng Anh',
  `listening_weight` = 35, `speaking_weight` = 30, `reading_weight` = 20, `writing_weight` = 15
WHERE `slug` = 'english-starter-1';

UPDATE `courses` SET
  `subtitle` = 'Mở rộng vốn từ vựng cơ bản',
  `objectives` = 'Mở rộng từ vựng về cuộc sống hàng ngày, thời gian, màu sắc\nMiêu tả người, đồ vật và địa điểm đơn giản\nNói về thói quen hàng ngày và sở thích cá nhân\nĐọc hiểu biển báo, thực đơn, tin nhắn ngắn\nViết đoạn văn ngắn miêu tả bản thân và gia đình\nThực hành hội thoại ngắn trong các tình huống quen thuộc',
  `requirements` = 'Đã hoàn thành English Starter 1 hoặc có kiến thức tương đương\nBiết bảng chữ cái và cách phát âm cơ bản\nCó thể đọc hiểu các câu tiếng Anh đơn giản',
  `target_audience` = 'Người đã biết một ít tiếng Anh cơ bản',
  `listening_weight` = 35, `speaking_weight` = 30, `reading_weight` = 20, `writing_weight` = 15
WHERE `slug` = 'english-starter-2';

UPDATE `courses` SET
  `subtitle` = 'Hoàn thiện nền tảng A1',
  `objectives` = 'Giao tiếp tự tin trong các tình huống mua sắm, ăn uống\nMô tả thời tiết, trang phục và hoạt động hàng ngày\nKể về kỳ nghỉ và kế hoạch tương lai đơn giản\nĐọc hiểu email, tin nhắn và bài viết ngắn trên mạng\nViết đoạn văn kể về trải nghiệm cá nhân\nChuẩn bị cho bài kiểm tra trình độ A1',
  `requirements` = 'Đã hoàn thành English Starter 2 hoặc tương đương\nCó vốn từ vựng khoảng 300-500 từ\nCó thể giao tiếp các câu đơn giản hàng ngày',
  `target_audience` = 'Người học tiếng Anh ở trình độ sơ cấp',
  `listening_weight` = 30, `speaking_weight` = 30, `reading_weight` = 20, `writing_weight` = 20
WHERE `slug` = 'english-starter-3';

-- ============================================
-- A2 LEVEL (Cơ bản mở rộng - Nghe 30%, Nói 30%)
-- ============================================
UPDATE `courses` SET
  `subtitle` = 'Giao tiếp trong các tình huống thực tế',
  `objectives` = 'Giao tiếp khi đi du lịch: hỏi đường, đặt phòng, mua vé\nSử dụng phương tiện giao thông công cộng bằng tiếng Anh\nMiêu tả địa điểm, con người và trải nghiệm du lịch\nĐọc hiểu hướng dẫn du lịch, bản đồ và thông báo\nViết email, tin nhắn trong bối cảnh du lịch\nHiểu các đoạn hội thoại về chủ đề du lịch và văn hóa',
  `requirements` = 'Đã hoàn thành trình độ A1 hoặc tương đương\nCó thể giao tiếp cơ bản trong các tình huống hàng ngày\nVốn từ vựng khoảng 500-800 từ',
  `target_audience` = 'Người học muốn giao tiếp khi đi du lịch nước ngoài',
  `listening_weight` = 30, `speaking_weight` = 30, `reading_weight` = 20, `writing_weight` = 20
WHERE `slug` = 'english-basic-1';

UPDATE `courses` SET
  `subtitle` = 'Sở thích và cuộc sống hàng ngày',
  `objectives` = 'Thảo luận về sở thích: thể thao, âm nhạc, giải trí\nKể chuyện và miêu tả trải nghiệm cá nhân\nĐọc hiểu bài báo, blog về chủ đề giải trí và đời sống\nViết bài đăng, bình luận trên mạng xã hội bằng tiếng Anh\nNghe hiểu podcast, video về chủ đề yêu thích\nPhát triển kỹ năng hội thoại tự nhiên',
  `requirements` = 'Đã hoàn thành English Basic 1 hoặc tương đương\nCó khả năng đọc hiểu đoạn văn ngắn\nCó thể viết các câu ghép đơn giản',
  `target_audience` = 'Người muốn giao tiếp về sở thích và đời sống',
  `listening_weight` = 30, `speaking_weight` = 30, `reading_weight` = 20, `writing_weight` = 20
WHERE `slug` = 'english-basic-2';

UPDATE `courses` SET
  `subtitle` = 'Công việc và học tập cơ bản',
  `objectives` = 'Giao tiếp trong môi trường công sở cơ bản\nViết email công việc đơn giản, tin nhắn chuyên nghiệp\nThảo luận về nghề nghiệp, kế hoạch học tập\nĐọc hiểu thông báo, tài liệu hướng dẫn công việc\nSử dụng công nghệ cơ bản bằng tiếng Anh\nThực hành phỏng vấn xin việc đơn giản',
  `requirements` = 'Đã hoàn thành English Basic 2 hoặc tương đương\nCó thể đọc hiểu các văn bản đơn giản\nBiết cách viết câu và đoạn văn ngắn',
  `target_audience` = 'Người đi làm, sinh viên cần tiếng Anh cơ bản',
  `listening_weight` = 25, `speaking_weight` = 30, `reading_weight` = 25, `writing_weight` = 20
WHERE `slug` = 'english-basic-3';

-- ============================================
-- B1 LEVEL (Trung cấp - cân bằng 25% mỗi kỹ năng)
-- ============================================
UPDATE `courses` SET
  `subtitle` = 'Giao tiếp tự tin trong cuộc sống',
  `objectives` = 'Thảo luận về các mối quan hệ, sức khỏe và môi trường\nBày tỏ ý kiến cá nhân và tranh luận nhẹ nhàng\nĐọc hiểu bài báo, tin tức về các chủ đề xã hội\nViết bài luận ngắn bày tỏ quan điểm\nNghe hiểu podcast, bản tin thời sự cơ bản\nThực hành thuyết trình ngắn về chủ đề quen thuộc',
  `requirements` = 'Đã hoàn thành trình độ A2 hoặc tương đương\nCó thể giao tiếp trong hầu hết tình huống hàng ngày\nVốn từ vựng khoảng 1500-2000 từ',
  `target_audience` = 'Người học muốn giao tiếp tự tin về nhiều chủ đề',
  `listening_weight` = 25, `speaking_weight` = 25, `reading_weight` = 25, `writing_weight` = 25
WHERE `slug` = 'english-intermediate-1';

UPDATE `courses` SET
  `subtitle` = 'Truyền thông và văn hóa',
  `objectives` = 'Phân tích tin tức, bài báo từ các nguồn truyền thông\nThảo luận về văn hóa, giáo dục và các vấn đề toàn cầu\nĐọc hiểu bài viết học thuật cấp độ trung cấp\nViết bài luận so sánh, phân tích văn hóa\nNghe hiểu phim, TV shows không cần phụ đề\nSo sánh các nền văn hóa khác nhau bằng tiếng Anh',
  `requirements` = 'Đã hoàn thành English Intermediate 1 hoặc tương đương\nCó khả năng đọc hiểu văn bản trung cấp\nCó thể viết đoạn văn 100-150 từ',
  `target_audience` = 'Người quan tâm đến văn hóa và truyền thông quốc tế',
  `listening_weight` = 25, `speaking_weight` = 25, `reading_weight` = 25, `writing_weight` = 25
WHERE `slug` = 'english-intermediate-2';

UPDATE `courses` SET
  `subtitle` = 'Tiếng Anh công sở chuyên nghiệp',
  `objectives` = 'Giao tiếp chuyên nghiệp trong môi trường kinh doanh\nThuyết trình tự tin trước đồng nghiệp và khách hàng\nViết báo cáo, đề xuất kinh doanh cơ bản\nĐọc hiểu tài liệu kinh doanh, hợp đồng đơn giản\nTham gia họp hành và đàm phán bằng tiếng Anh\nViết CV và thư xin việc chuyên nghiệp',
  `requirements` = 'Đã hoàn thành English Intermediate 2 hoặc tương đương\nCó kiến thức cơ bản về môi trường công sở\nKhả năng giao tiếp tiếng Anh ở mức trung cấp',
  `target_audience` = 'Người đi làm cần tiếng Anh trong công việc',
  `listening_weight` = 25, `speaking_weight` = 25, `reading_weight` = 25, `writing_weight` = 25
WHERE `slug` = 'english-intermediate-3';

-- ============================================
-- B2 LEVEL (Trung cấp cao - thiên học thuật, Viết 30%)
-- ============================================
UPDATE `courses` SET
  `subtitle` = 'Ngữ pháp và viết luận nâng cao',
  `objectives` = 'Nắm vững ngữ pháp nâng cao: câu phức, bị động, điều kiện\nViết bài luận học thuật 250-350 từ đúng cấu trúc\nĐọc hiểu văn bản học thuật và báo chí nâng cao\nTranh luận và bảo vệ quan điểm bằng lập luận logic\nNghe hiểu bài giảng, hội thảo chuyên ngành\nPhân tích và đánh giá các nguồn thông tin',
  `requirements` = 'Đã hoàn thành trình độ B1 hoặc tương đương\nCó thể giao tiếp lưu loát về hầu hết chủ đề\nVốn từ vựng khoảng 3000-4000 từ',
  `target_audience` = 'Sinh viên, người chuẩn bị du học',
  `listening_weight` = 25, `speaking_weight` = 20, `reading_weight` = 25, `writing_weight` = 30
WHERE `slug` = 'english-upper-1';

UPDATE `courses` SET
  `subtitle` = 'Tranh luận và phản biện',
  `objectives` = 'Tranh luận về các vấn đề thời sự, lịch sử, kinh tế\nXây dựng lập luận chặt chẽ và phản biện hiệu quả\nĐọc hiểu bài nghiên cứu, báo cáo phân tích chuyên sâu\nViết bài luận phân tích, đánh giá 350-500 từ\nNghe hiểu tranh luận, tọa đàm học thuật\nPhát triển tư duy phản biện bằng tiếng Anh',
  `requirements` = 'Đã hoàn thành English Upper 1 hoặc tương đương\nCó khả năng viết bài luận ngắn\nCó thể hiểu các chủ đề trừu tượng',
  `target_audience` = 'Người muốn phát triển kỹ năng tranh luận học thuật',
  `listening_weight` = 25, `speaking_weight` = 20, `reading_weight` = 25, `writing_weight` = 30
WHERE `slug` = 'english-upper-2';

UPDATE `courses` SET
  `subtitle` = 'Giao tiếp chuyên nghiệp nâng cao',
  `objectives` = 'Thực hành phỏng vấn xin việc chuyên nghiệp bằng tiếng Anh\nĐàm phán hợp đồng và thỏa thuận kinh doanh\nViết email, báo cáo, đề xuất chuyên nghiệp\nThuyết trình chuyên sâu về chủ đề chuyên môn\nGiao tiếp hiệu quả trong môi trường đa văn hóa\nXây dựng thương hiệu cá nhân bằng tiếng Anh',
  `requirements` = 'Đã hoàn thành English Upper 2 hoặc tương đương\nCó kinh nghiệm làm việc hoặc học tập trong môi trường chuyên nghiệp\nKỹ năng viết và nói ở mức trung cấp cao',
  `target_audience` = 'Chuyên viên, quản lý cần tiếng Anh công việc nâng cao',
  `listening_weight` = 25, `speaking_weight` = 20, `reading_weight` = 25, `writing_weight` = 30
WHERE `slug` = 'english-upper-3';

-- ============================================
-- C1 LEVEL (Nâng cao - thiên học thuật, Đọc 30%, Viết 30%)
-- ============================================
UPDATE `courses` SET
  `subtitle` = 'Viết học thuật chuyên sâu',
  `objectives` = 'Viết bài luận học thuật chuẩn academic 500-1000 từ\nTrích dẫn và tham khảo tài liệu theo chuẩn quốc tế\nXây dựng lập luận học thuật đa chiều, chặt chẽ\nĐọc hiểu và phân tích bài nghiên cứu khoa học\nThuyết trình học thuật tại hội thảo chuyên ngành\nLàm quen với phong cách viết academic writing',
  `requirements` = 'Đã hoàn thành trình độ B2 hoặc tương đương\nCó thể viết bài luận phân tích 350+ từ\nVốn từ vựng khoảng 5000+ từ\nKhả năng đọc hiểu văn bản phức tạp',
  `target_audience` = 'Sinh viên đại học, nghiên cứu sinh, người chuẩn bị du học',
  `listening_weight` = 20, `speaking_weight` = 20, `reading_weight` = 30, `writing_weight` = 30
WHERE `slug` = 'english-advanced-1';

UPDATE `courses` SET
  `subtitle` = 'Văn học và nghệ thuật',
  `objectives` = 'Phân tích tác phẩm văn học Anh-Mỹ ở cấp độ chuyên sâu\nViết bài phê bình văn học, nghệ thuật bằng tiếng Anh\nThảo luận triết học, tư tưởng qua các thời kỳ\nĐọc hiểu văn bản văn học cổ điển và hiện đại\nSáng tạo nội dung bằng tiếng Anh (sáng tác, blog, báo)\nPhát triển phong cách ngôn ngữ cá nhân tinh tế',
  `requirements` = 'Đã hoàn thành English Advanced 1 hoặc tương đương\nCó kiến thức nền về văn học và nghệ thuật\nKhả năng đọc hiểu văn bản dài và phức tạp',
  `target_audience` = 'Người yêu thích văn học, nghệ thuật và sáng tạo nội dung',
  `listening_weight` = 20, `speaking_weight` = 20, `reading_weight` = 30, `writing_weight` = 30
WHERE `slug` = 'english-advanced-2';

UPDATE `courses` SET
  `subtitle` = 'Thành thạo tiếng Anh toàn diện',
  `objectives` = 'Sử dụng idiom, sắc thái ngôn ngữ và văn phong linh hoạt\nTranh luận học thuật ở cấp độ chuyên gia\nViết báo, bài nghiên cứu đạt chuẩn xuất bản quốc tế\nĐọc hiểu mọi thể loại văn bản từ học thuật đến văn học\nThuyết trình và giảng dạy bằng tiếng Anh\nĐạt trình độ tương đương CEFR C1-C2',
  `requirements` = 'Đã hoàn thành English Advanced 2 hoặc tương đương\nCó thể sử dụng tiếng Anh trong môi trường học thuật\nĐã có kinh nghiệm viết bài luận dài và phức tạp',
  `target_audience` = 'Người muốn đạt trình độ tiếng Anh chuyên nghiệp cao nhất',
  `listening_weight` = 20, `speaking_weight` = 20, `reading_weight` = 30, `writing_weight` = 30
WHERE `slug` = 'english-advanced-3';
