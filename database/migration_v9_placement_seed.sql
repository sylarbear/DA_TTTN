-- ============================================
-- Seed Data: Placement Test Questions (~91 cau)
-- LUU Y: JSON arrays dung dau nhay kep (") ben trong
-- ============================================

-- ============================================
-- A1 LEVEL (18 cau)
-- ============================================

-- A1 - Vocabulary (5)
INSERT INTO `placement_questions` (`question_text`, `question_type`, `options_json`, `correct_answer`, `cefr_level`, `skill_type`, `difficulty_weight`, `explanation`) VALUES
('What is the meaning of "apple"?', 'multiple_choice', '["Quả táo", "Quả cam", "Quả chuối", "Quả nho"]', 'Quả táo', 'A1', 'vocabulary', 0.8, '"Apple" có nghĩa là quả táo.'),
('Choose the correct word: "I ___ a student."', 'fill_blank', NULL, 'am', 'A1', 'vocabulary', 0.9, 'Dùng "am" với chủ ngữ "I".'),
('What is the opposite of "hot"?', 'multiple_choice', '["Cold", "Big", "Fast", "Happy"]', 'Cold', 'A1', 'vocabulary', 1.0, '"Hot" (nóng) trái nghĩa với "cold" (lạnh).'),
('"Hello" means ___ in Vietnamese.', 'fill_blank', NULL, 'Xin chào', 'A1', 'vocabulary', 0.8, '"Hello" có nghĩa là "Xin chào".'),
('Which word is a color?', 'multiple_choice', '["Blue", "Table", "Run", "Happy"]', 'Blue', 'A1', 'vocabulary', 0.9, '"Blue" là màu xanh da trời.');

-- A1 - Grammar (5)
INSERT INTO `placement_questions` (`question_text`, `question_type`, `options_json`, `correct_answer`, `cefr_level`, `skill_type`, `difficulty_weight`, `explanation`) VALUES
('She ___ a teacher.', 'multiple_choice', '["is", "are", "am", "be"]', 'is', 'A1', 'grammar', 0.8, 'Dùng "is" với chủ ngữ ngôi thứ ba số ít "She".'),
('___ name is John.', 'multiple_choice', '["My", "I", "Me", "Mine"]', 'My', 'A1', 'grammar', 0.9, '"My" là tính từ sở hữu, đứng trước danh từ "name".'),
('There ___ two books on the table.', 'multiple_choice', '["are", "is", "am", "be"]', 'are', 'A1', 'grammar', 0.8, 'Dùng "are" với danh từ số nhiều "books".'),
('"I am" viết tắt là ___.', 'fill_blank', NULL, 'I''m', 'A1', 'grammar', 1.0, '"I am" được viết tắt thành "I''m".'),
('Which is correct?', 'multiple_choice', '["He is happy", "He happy is", "Is he happy", "Happy he is"]', 'He is happy', 'A1', 'grammar', 0.9, 'Trật tự từ đúng: Chủ ngữ + động từ + tính từ.');

-- A1 - Reading (4)
INSERT INTO `placement_questions` (`question_text`, `question_type`, `options_json`, `correct_answer`, `cefr_level`, `skill_type`, `difficulty_weight`, `passage`, `explanation`) VALUES
('What is her name?', 'multiple_choice', '["Anna", "John", "Tom", "Mary"]', 'Anna', 'A1', 'reading', 0.8, 'My name is Anna. I am 20 years old.', 'Đoạn văn viết "My name is Anna".'),
('How old is Anna?', 'multiple_choice', '["20", "25", "30", "15"]', '20', 'A1', 'reading', 0.9, 'My name is Anna. I am 20 years old.', 'Đoạn văn viết "I am 20 years old".'),
('What color is the cat?', 'multiple_choice', '["Black", "White", "Brown", "Gray"]', 'Black', 'A1', 'reading', 0.9, 'I have a cat. The cat is black. It is very cute.', 'Đoạn văn viết "The cat is black".'),
('The dog is ___.', 'fill_blank', NULL, 'big', 'A1', 'reading', 1.0, 'I have a dog. The dog is big. It likes to run.', 'Đoạn văn viết "The dog is big".');

-- A1 - Listening (4)
INSERT INTO `placement_questions` (`question_text`, `question_type`, `options_json`, `correct_answer`, `cefr_level`, `skill_type`, `difficulty_weight`, `passage`, `explanation`) VALUES
('(Nghe) What is the weather like today?', 'multiple_choice', '["Sunny", "Rainy", "Cloudy", "Snowy"]', 'Sunny', 'A1', 'listening', 0.8, 'It is sunny today.', 'Câu hỏi nghe: "It is sunny today." Đáp án là "Sunny".'),
('(Nghe) How many people are there?', 'multiple_choice', '["Three", "Two", "Four", "Five"]', 'Three', 'A1', 'listening', 0.9, 'There are three people in the room.', 'Câu hỏi nghe: "There are three people in the room."'),
('(Nghe) What is the boy doing?', 'multiple_choice', '["Eating", "Running", "Sleeping", "Reading"]', 'Eating', 'A1', 'listening', 0.8, 'The boy is eating an apple.', 'Câu hỏi nghe: "The boy is eating an apple."'),
('(Nghe) Where is the book?', 'multiple_choice', '["On the table", "Under the chair", "In the bag", "On the shelf"]', 'On the table', 'A1', 'listening', 1.0, 'The book is on the table.', 'Câu hỏi nghe: "The book is on the table."');

-- ============================================
-- A2 LEVEL (18 cau)
-- ============================================

-- A2 - Vocabulary (5)
INSERT INTO `placement_questions` (`question_text`, `question_type`, `options_json`, `correct_answer`, `cefr_level`, `skill_type`, `difficulty_weight`, `explanation`) VALUES
('What does "delicious" mean?', 'multiple_choice', '["Ngon", "Đẹp", "Nhanh", "Cao"]', 'Ngon', 'A2', 'vocabulary', 0.9, '"Delicious" có nghĩa là ngon (đồ ăn).'),
('Choose the correct word: "She ___ to school every day."', 'multiple_choice', '["goes", "go", "going", "gone"]', 'goes', 'A2', 'vocabulary', 1.0, 'Hiện tại đơn với chủ ngữ "She" → động từ thêm -es: "goes".'),
('"Library" is a place where you can ___.', 'multiple_choice', '["Read books", "Buy food", "Play sports", "Watch movies"]', 'Read books', 'A2', 'vocabulary', 0.9, 'Library = thư viện, nơi đọc sách.'),
('Which word means "xinh đẹp"?', 'multiple_choice', '["Beautiful", "Ugly", "Tall", "Short"]', 'Beautiful', 'A2', 'vocabulary', 0.8, '"Beautiful" có nghĩa là xinh đẹp.'),
('She is very ___. She always helps everyone.', 'fill_blank', NULL, 'kind', 'A2', 'vocabulary', 1.0, '"Kind" = tốt bụng. Người luôn giúp đỡ mọi người là người tốt bụng.');

-- A2 - Grammar (5)
INSERT INTO `placement_questions` (`question_text`, `question_type`, `options_json`, `correct_answer`, `cefr_level`, `skill_type`, `difficulty_weight`, `explanation`) VALUES
('They ___ playing football now.', 'multiple_choice', '["are", "is", "was", "were"]', 'are', 'A2', 'grammar', 0.9, 'Hiện tại tiếp diễn: They + are + V-ing.'),
('She ___ to London last year.', 'multiple_choice', '["went", "goes", "go", "going"]', 'went', 'A2', 'grammar', 1.0, 'Quá khứ đơn của "go" là "went". "Last year" → dùng quá khứ.'),
('I have ___ finished my homework.', 'multiple_choice', '["already", "yet", "since", "for"]', 'already', 'A2', 'grammar', 1.0, '"Already" dùng trong câu khẳng định ở hiện tại hoàn thành.'),
('This book is ___ than that one.', 'multiple_choice', '["more interesting", "interesting", "most interesting", "interestinger"]', 'more interesting', 'A2', 'grammar', 0.9, 'So sánh hơn với tính từ dài: more + adj + than.'),
('___ you like some coffee?', 'multiple_choice', '["Would", "Will", "Are", "Do"]', 'Would', 'A2', 'grammar', 0.9, '"Would you like..." là cấu trúc lịch sự để mời/đề nghị.');

-- A2 - Reading (4)
INSERT INTO `placement_questions` (`question_text`, `question_type`, `options_json`, `correct_answer`, `cefr_level`, `skill_type`, `difficulty_weight`, `passage`, `explanation`) VALUES
('Where did Tom go last weekend?', 'multiple_choice', '["The beach", "The mountains", "The city", "The park"]', 'The beach', 'A2', 'reading', 0.9, 'Last weekend, Tom went to the beach with his family. They swam in the sea and built sandcastles. It was a great day.', 'Đoạn văn: "Tom went to the beach with his family."'),
('What did Tom do at the beach?', 'multiple_choice', '["Swam and built sandcastles", "Read a book", "Played football", "Went shopping"]', 'Swam and built sandcastles', 'A2', 'reading', 1.0, 'Last weekend, Tom went to the beach with his family. They swam in the sea and built sandcastles. It was a great day.', 'Đoạn văn: "They swam in the sea and built sandcastles."'),
('What does Sarah want to be?', 'multiple_choice', '["A doctor", "A teacher", "An engineer", "A nurse"]', 'A doctor', 'A2', 'reading', 0.9, 'Sarah is studying very hard. She wants to be a doctor. She loves helping sick people.', 'Đoạn văn: "She wants to be a doctor."'),
('Sarah wants to be a doctor because she ___.', 'multiple_choice', '["Loves helping sick people", "Wants money", "Likes studying", "Loves hospitals"]', 'Loves helping sick people', 'A2', 'reading', 1.0, 'Sarah is studying very hard. She wants to be a doctor. She loves helping sick people.', 'Đoạn văn: "She loves helping sick people."');

-- A2 - Listening (4)
INSERT INTO `placement_questions` (`question_text`, `question_type`, `options_json`, `correct_answer`, `cefr_level`, `skill_type`, `difficulty_weight`, `passage`, `explanation`) VALUES
('(Nghe) What time does the train leave?', 'multiple_choice', '["8:30", "9:00", "7:45", "10:15"]', '8:30', 'A2', 'listening', 0.9, 'The train leaves at half past eight.', 'Câu hỏi nghe: "The train leaves at half past eight." → 8:30.'),
('(Nghe) Where does the woman want to go?', 'multiple_choice', '["The supermarket", "The cinema", "The hospital", "The school"]', 'The supermarket', 'A2', 'listening', 0.9, 'Excuse me, where is the supermarket?', 'Câu hỏi nghe: "Excuse me, where is the supermarket?"'),
('(Nghe) How does the man feel?', 'multiple_choice', '["Tired", "Happy", "Angry", "Excited"]', 'Tired', 'A2', 'listening', 1.0, 'I am so tired. I worked all day.', 'Câu hỏi nghe: "I am so tired. I worked all day."'),
('(Nghe) What is the woman going to buy?', 'multiple_choice', '["A dress", "Shoes", "A bag", "A coat"]', 'A dress', 'A2', 'listening', 1.0, 'I am going to buy a new dress for the party.', 'Câu hỏi nghe: "I am going to buy a new dress for the party."');

-- ============================================
-- B1 LEVEL (19 cau)
-- ============================================

-- B1 - Vocabulary (5)
INSERT INTO `placement_questions` (`question_text`, `question_type`, `options_json`, `correct_answer`, `cefr_level`, `skill_type`, `difficulty_weight`, `explanation`) VALUES
('What does "opportunity" mean?', 'multiple_choice', '["Cơ hội", "Thử thách", "Kinh nghiệm", "Trách nhiệm"]', 'Cơ hội', 'B1', 'vocabulary', 0.9, '"Opportunity" = cơ hội.'),
('The company will ___ new employees next month.', 'multiple_choice', '["hire", "fire", "train", "promote"]', 'hire', 'B1', 'vocabulary', 1.0, '"Hire" = thuê/tuyển dụng. Ngữ cảnh: công ty tuyển nhân viên mới.'),
('Choose the synonym of "important":', 'multiple_choice', '["Significant", "Tiny", "Simple", "Quick"]', 'Significant', 'B1', 'vocabulary', 0.9, '"Important" (quan trọng) đồng nghĩa với "significant".'),
('The ___ of the story was surprising.', 'fill_blank', NULL, 'ending', 'B1', 'vocabulary', 1.0, '"Ending" = cái kết. "The ending of the story" = cái kết của câu chuyện.'),
('He needs to ___ a decision soon.', 'multiple_choice', '["make", "do", "take", "give"]', 'make', 'B1', 'vocabulary', 1.0, 'Collocation: "make a decision" = đưa ra quyết định.');

-- B1 - Grammar (5)
INSERT INTO `placement_questions` (`question_text`, `question_type`, `options_json`, `correct_answer`, `cefr_level`, `skill_type`, `difficulty_weight`, `explanation`) VALUES
('If I ___ rich, I would travel the world.', 'multiple_choice', '["were", "am", "will be", "was"]', 'were', 'B1', 'grammar', 1.0, 'Câu điều kiện loại 2: If + S + were/V-ed, S + would + V. Dùng "were" cho mọi chủ ngữ.'),
('The book ___ by J.K. Rowling was a bestseller.', 'multiple_choice', '["written", "wrote", "writing", "writes"]', 'written', 'B1', 'grammar', 1.0, 'Mệnh đề quan hệ rút gọn dạng bị động: "which was written" → "written".'),
('She asked me where ___.', 'multiple_choice', '["I lived", "did I live", "I live", "do I live"]', 'I lived', 'B1', 'grammar', 1.0, 'Câu tường thuật: Wh-word + S + V (lùi thì). "Where do you live?" → "where I lived".'),
('I wish I ___ more time to study.', 'multiple_choice', '["had", "have", "will have", "would have"]', 'had', 'B1', 'grammar', 1.0, 'Cấu trúc "I wish": ước điều không có thật ở hiện tại → dùng quá khứ đơn "had".'),
('By the time we arrived, the movie ___.', 'multiple_choice', '["had started", "started", "has started", "was starting"]', 'had started', 'B1', 'grammar', 1.0, 'Quá khứ hoàn thành: hành động xảy ra trước một hành động khác trong quá khứ.');

-- B1 - Reading (5)
INSERT INTO `placement_questions` (`question_text`, `question_type`, `options_json`, `correct_answer`, `cefr_level`, `skill_type`, `difficulty_weight`, `passage`, `explanation`) VALUES
('Why is recycling important according to the passage?', 'multiple_choice', '["It reduces waste and saves resources", "It makes money", "It is easy to do", "It is required by law"]', 'It reduces waste and saves resources', 'B1', 'reading', 0.9, 'Recycling is one of the most important things we can do to protect our environment. When we recycle, we reduce the amount of waste that goes to landfills. Recycling also saves natural resources like trees, water, and minerals.', 'Đoạn văn: "we reduce the amount of waste... saves natural resources."'),
('What happens if we do not recycle?', 'multiple_choice', '["Waste increases and resources are depleted", "Nothing happens", "The economy improves", "New jobs are created"]', 'Waste increases and resources are depleted', 'B1', 'reading', 1.0, 'Recycling is one of the most important things we can do to protect our environment. When we recycle, we reduce the amount of waste that goes to landfills. Recycling also saves natural resources like trees, water, and minerals.', 'Không tái chế → rác thải tăng, tài nguyên cạn kiệt.'),
('What is the main benefit of remote work?', 'multiple_choice', '["Flexibility and work-life balance", "Higher salary", "Less communication", "More meetings"]', 'Flexibility and work-life balance', 'B1', 'reading', 0.9, 'Remote work has become increasingly popular in recent years. Many employees appreciate the flexibility it offers, allowing them to balance their professional and personal lives more effectively. However, it also requires strong self-discipline and communication skills.', 'Đoạn văn: "flexibility... balance their professional and personal lives."'),
('What skill is important for remote workers?', 'multiple_choice', '["Self-discipline", "Typing speed", "Cooking", "Drawing"]', 'Self-discipline', 'B1', 'reading', 1.0, 'Remote work has become increasingly popular in recent years. Many employees appreciate the flexibility it offers, allowing them to balance their professional and personal lives more effectively. However, it also requires strong self-discipline and communication skills.', 'Đoạn văn: "it also requires strong self-discipline."'),
('According to the text, tourists should ___.', 'multiple_choice', '["Respect local customs", "Ignore local people", "Only visit big cities", "Avoid local food"]', 'Respect local customs', 'B1', 'reading', 1.0, 'When traveling to a new country, it is important to respect local customs and traditions. Learning a few phrases in the local language can also help you connect with people and have a more authentic experience.', 'Đoạn văn: "it is important to respect local customs and traditions."');

-- B1 - Listening (4)
INSERT INTO `placement_questions` (`question_text`, `question_type`, `options_json`, `correct_answer`, `cefr_level`, `skill_type`, `difficulty_weight`, `passage`, `explanation`) VALUES
('(Nghe) What is the main topic of the conversation?', 'multiple_choice', '["Planning a trip", "Ordering food", "Buying a car", "Applying for a job"]', 'Planning a trip', 'B1', 'listening', 0.9, 'We need to decide where to go this summer. I was thinking about visiting Da Nang for a week. What do you think?', 'Đoạn hội thoại về việc lên kế hoạch đi du lịch.'),
('(Nghe) What does the man suggest?', 'multiple_choice', '["Leaving earlier", "Taking a taxi", "Canceling the trip", "Bringing more luggage"]', 'Leaving earlier', 'B1', 'listening', 1.0, 'We should leave earlier tomorrow morning. The traffic is always bad around eight o''clock.', 'Người đàn ông đề nghị khởi hành sớm hơn để tránh tắc đường.'),
('(Nghe) Why is the woman upset?', 'multiple_choice', '["She lost her keys", "She missed the bus", "She failed the test", "She broke her phone"]', 'She lost her keys', 'B1', 'listening', 1.0, 'Oh no, I cannot find my keys anywhere. I have looked in my bag and on the table. I think I left them at the office.', 'Người phụ nữ buồn vì làm mất chìa khóa.'),
('(Nghe) What will they probably do next?', 'multiple_choice', '["Go to the cinema", "Stay at home", "Visit a museum", "Go shopping"]', 'Go to the cinema', 'B1', 'listening', 0.9, 'There is a new movie playing at the theater. The reviews are really good. Let us go see it tonight.', 'Họ quyết định đi xem phim sau khi thảo luận về các lựa chọn.');

-- ============================================
-- B2 LEVEL (19 cau)
-- ============================================

-- B2 - Vocabulary (5)
INSERT INTO `placement_questions` (`question_text`, `question_type`, `options_json`, `correct_answer`, `cefr_level`, `skill_type`, `difficulty_weight`, `explanation`) VALUES
('The new policy was ___ by the government.', 'multiple_choice', '["implemented", "destroyed", "ignored", "celebrated"]', 'implemented', 'B2', 'vocabulary', 1.0, '"Implement" = thực thi, triển khai. "Policy was implemented" = chính sách được thực thi.'),
('What does "inevitable" mean?', 'multiple_choice', '["Unavoidable", "Unlikely", "Impossible", "Uncertain"]', 'Unavoidable', 'B2', 'vocabulary', 0.9, '"Inevitable" = không thể tránh khỏi = unavoidable.'),
('The two companies decided to ___ into one.', 'fill_blank', NULL, 'merge', 'B2', 'vocabulary', 1.0, '"Merge" = sáp nhập. Hai công ty sáp nhập thành một.'),
('Her speech was very ___. Everyone felt inspired.', 'multiple_choice', '["compelling", "boring", "confusing", "ordinary"]', 'compelling', 'B2', 'vocabulary', 0.9, '"Compelling" = thuyết phục, hấp dẫn. Bài phát biểu truyền cảm hứng → compelling.'),
('Choose the antonym of "generous":', 'multiple_choice', '["Stingy", "Kind", "Wealthy", "Helpful"]', 'Stingy', 'B2', 'vocabulary', 0.8, '"Generous" (hào phóng) trái nghĩa với "stingy" (keo kiệt).');

-- B2 - Grammar (5)
INSERT INTO `placement_questions` (`question_text`, `question_type`, `options_json`, `correct_answer`, `cefr_level`, `skill_type`, `difficulty_weight`, `explanation`) VALUES
('Not only ___ the exam, but she also got the highest score.', 'multiple_choice', '["did she pass", "she passed", "she did pass", "passed she"]', 'did she pass', 'B2', 'grammar', 1.1, '"Not only" đứng đầu câu → đảo ngữ: Not only + trợ động từ + S + V.'),
('Had I known about the traffic, I ___ earlier.', 'multiple_choice', '["would have left", "would leave", "left", "had left"]', 'would have left', 'B2', 'grammar', 1.1, 'Đảo ngữ câu điều kiện loại 3: Had + S + V3 → S + would have + V3.'),
('The project, ___ details are confidential, will launch next year.', 'multiple_choice', '["whose", "which", "that", "whom"]', 'whose', 'B2', 'grammar', 1.0, '"Whose" là đại từ quan hệ sở hữu, thay cho "the project''s details".'),
('It is essential that every student ___ on time.', 'multiple_choice', '["be", "is", "will be", "was"]', 'be', 'B2', 'grammar', 1.1, 'Cấu trúc giả định (subjunctive): It is essential that + S + V (bare infinitive).'),
('She regretted ___ the job offer.', 'multiple_choice', '["turning down", "to turn down", "turn down", "turned down"]', 'turning down', 'B2', 'grammar', 1.0, '"Regret + V-ing": hối hận vì đã làm gì. "Regret turning down" = hối hận vì đã từ chối.');

-- B2 - Reading (5)
INSERT INTO `placement_questions` (`question_text`, `question_type`, `options_json`, `correct_answer`, `cefr_level`, `skill_type`, `difficulty_weight`, `passage`, `explanation`) VALUES
('What is the main argument of the passage?', 'multiple_choice', '["AI will transform industries but requires ethical oversight", "AI is dangerous and should be banned", "AI has no real impact on society", "AI will replace all human jobs"]', 'AI will transform industries but requires ethical oversight', 'B2', 'reading', 1.0, 'Artificial intelligence is rapidly transforming industries across the globe. While AI offers unprecedented opportunities for innovation and efficiency, it also raises significant ethical concerns regarding privacy, bias, and employment. Experts argue that careful regulation and ethical guidelines are essential to ensure AI benefits society as a whole.', 'Đoạn văn thảo luận cả cơ hội và thách thức của AI, nhấn mạnh cần có quy định đạo đức.'),
('According to the passage, what is a concern about AI?', 'multiple_choice', '["Privacy and bias issues", "Too much innovation", "Low efficiency", "Too many jobs"]', 'Privacy and bias issues', 'B2', 'reading', 1.0, 'Artificial intelligence is rapidly transforming industries across the globe. While AI offers unprecedented opportunities for innovation and efficiency, it also raises significant ethical concerns regarding privacy, bias, and employment.', 'Đoạn văn: "raises significant ethical concerns regarding privacy, bias, and employment."'),
('What caused the decline of the Roman Empire according to the text?', 'multiple_choice', '["Multiple factors including economic and military issues", "A single invasion", "Natural disasters", "Religious conflicts only"]', 'Multiple factors including economic and military issues', 'B2', 'reading', 1.1, 'Historians have long debated the causes of the Roman Empire''s decline. While no single factor can explain such a complex event, most scholars agree that a combination of economic instability, military overspending, political corruption, and external invasions contributed to the empire''s gradual collapse over several centuries.', 'Đoạn văn: "a combination of economic instability, military overspending, political corruption, and external invasions."'),
('What does the author imply about the decline?', 'multiple_choice', '["It was a gradual process with multiple causes", "It happened suddenly", "It was mainly caused by one emperor", "It was easily preventable"]', 'It was a gradual process with multiple causes', 'B2', 'reading', 1.1, 'Historians have long debated the causes of the Roman Empire''s decline. While no single factor can explain such a complex event, most scholars agree that a combination of economic instability, military overspending, political corruption, and external invasions contributed to the empire''s gradual collapse over several centuries.', 'Đoạn văn nhấn mạnh "gradual collapse" và "no single factor".'),
('According to the text, what should companies do to retain employees?', 'multiple_choice', '["Provide growth opportunities and recognition", "Increase working hours", "Reduce salaries", "Eliminate benefits"]', 'Provide growth opportunities and recognition', 'B2', 'reading', 1.0, 'Employee retention has become a critical challenge for modern organizations. Studies show that competitive salaries alone are insufficient; employees increasingly value professional development opportunities, work-life balance, and recognition for their contributions. Companies that invest in these areas tend to have significantly lower turnover rates.', 'Đoạn văn: "professional development opportunities... recognition for their contributions."');

-- B2 - Listening (4)
INSERT INTO `placement_questions` (`question_text`, `question_type`, `options_json`, `correct_answer`, `cefr_level`, `skill_type`, `difficulty_weight`, `passage`, `explanation`) VALUES
('(Nghe) What is the speaker''s opinion on climate change?', 'multiple_choice', '["Urgent action is needed immediately", "It is not a serious problem", "Technology will solve everything", "Only governments should act"]', 'Urgent action is needed immediately', 'B2', 'listening', 1.0, 'Climate change is no longer a distant threat. Scientists have confirmed that we have less than a decade to take decisive action before the most severe consequences become irreversible.', 'Diễn giả nhấn mạnh cần hành động khẩn cấp để đối phó với biến đổi khí hậu.'),
('(Nghe) What solution does the speaker propose?', 'multiple_choice', '["Investing in renewable energy", "Increasing fossil fuel use", "Reducing population", "Building more roads"]', 'Investing in renewable energy', 'B2', 'listening', 1.0, 'The most effective solution is to invest heavily in renewable energy sources such as solar, wind, and hydroelectric power. These technologies are now cost-competitive with fossil fuels.', 'Diễn giả đề xuất đầu tư vào năng lượng tái tạo.'),
('(Nghe) Why did the project fail according to the discussion?', 'multiple_choice', '["Poor planning and lack of communication", "Bad weather", "Technical issues only", "Budget surplus"]', 'Poor planning and lack of communication', 'B2', 'listening', 1.1, 'Looking back, the project failed because we did not plan properly from the start. There was also a serious lack of communication between the design team and the development team.', 'Dự án thất bại do lập kế hoạch kém và thiếu giao tiếp giữa các nhóm.'),
('(Nghe) What will the company do next quarter?', 'multiple_choice', '["Expand into Asian markets", "Close several branches", "Reduce product lines", "Hire fewer employees"]', 'Expand into Asian markets', 'B2', 'listening', 1.1, 'Next quarter, our company plans to expand into several Asian markets, starting with Vietnam and Indonesia. We have been preparing for this expansion for over a year.', 'Công ty có kế hoạch mở rộng sang thị trường châu Á vào quý tới.');

-- ============================================
-- C1 LEVEL (17 cau)
-- ============================================

-- C1 - Vocabulary (4)
INSERT INTO `placement_questions` (`question_text`, `question_type`, `options_json`, `correct_answer`, `cefr_level`, `skill_type`, `difficulty_weight`, `explanation`) VALUES
('The politician tried to ___ the scandal, but journalists kept investigating.', 'multiple_choice', '["suppress", "encourage", "publish", "celebrate"]', 'suppress', 'C1', 'vocabulary', 1.1, '"Suppress" = đàn áp, ngăn chặn (thông tin). Chính trị gia cố che giấu bê bối.'),
('Her ___ analysis of the situation impressed everyone in the boardroom.', 'multiple_choice', '["astute", "foolish", "vague", "careless"]', 'astute', 'C1', 'vocabulary', 1.0, '"Astute" = sắc sảo, tinh tường. Phân tích sắc sảo gây ấn tượng.'),
('The two theories are not ___. They can coexist.', 'fill_blank', NULL, 'mutually exclusive', 'C1', 'vocabulary', 1.2, '"Mutually exclusive" = loại trừ lẫn nhau. Hai lý thuyết không loại trừ nhau.'),
('What does "ubiquitous" mean?', 'multiple_choice', '["Everywhere at once", "Rare", "Expensive", "Complicated"]', 'Everywhere at once', 'C1', 'vocabulary', 1.0, '"Ubiquitous" = có mặt ở khắp mọi nơi = everywhere at once.');

-- C1 - Grammar (5)
INSERT INTO `placement_questions` (`question_text`, `question_type`, `options_json`, `correct_answer`, `cefr_level`, `skill_type`, `difficulty_weight`, `explanation`) VALUES
('Were it not for your help, I ___ the deadline.', 'multiple_choice', '["would have missed", "will miss", "would miss", "missed"]', 'would have missed', 'C1', 'grammar', 1.2, 'Đảo ngữ câu điều kiện loại 3: Were it not for + N → S + would have + V3.'),
('The professor insisted that the research ___ conducted ethically.', 'multiple_choice', '["be", "is", "was", "will be"]', 'be', 'C1', 'grammar', 1.1, 'Subjunctive với "insist that": S + insist that + S + V (bare infinitive).'),
('Seldom ___ such a remarkable performance.', 'multiple_choice', '["have I seen", "I have seen", "I saw", "I see"]', 'have I seen', 'C1', 'grammar', 1.0, '"Seldom" đứng đầu câu → đảo ngữ: Seldom + trợ động từ + S + V.'),
('The data ___ analyzed before the conclusion was drawn.', 'multiple_choice', '["had been", "has been", "is", "will be"]', 'had been', 'C1', 'grammar', 1.0, 'Quá khứ hoàn thành bị động: "had been analyzed" diễn tả hành động xảy ra trước "was drawn".'),
('It is high time the government ___ action on pollution.', 'multiple_choice', '["took", "takes", "has taken", "will take"]', 'took', 'C1', 'grammar', 1.2, 'Cấu trúc "It is high time + S + V-ed": đã đến lúc ai đó làm gì (dùng quá khứ đơn).');

-- C1 - Reading (5)
INSERT INTO `placement_questions` (`question_text`, `question_type`, `options_json`, `correct_answer`, `cefr_level`, `skill_type`, `difficulty_weight`, `passage`, `explanation`) VALUES
('What is the author''s tone toward globalization?', 'multiple_choice', '["Nuanced - acknowledging both benefits and drawbacks", "Entirely positive", "Completely negative", "Indifferent"]', 'Nuanced - acknowledging both benefits and drawbacks', 'C1', 'reading', 1.1, 'Globalization has undoubtedly created unprecedented economic opportunities, lifting millions out of poverty. However, its benefits have been distributed unevenly, exacerbating inequality within and between nations. Moreover, the homogenization of culture threatens linguistic diversity and indigenous traditions. A balanced approach that harnesses globalization''s benefits while mitigating its adverse effects is essential for sustainable development.', 'Tác giả thừa nhận cả lợi ích (cơ hội kinh tế) và mặt trái (bất bình đẳng, đồng nhất văn hóa) → sắc thái.'),
('What does "exacerbating" mean in this context?', 'multiple_choice', '["Making worse", "Improving", "Eliminating", "Ignoring"]', 'Making worse', 'C1', 'reading', 1.0, 'Globalization has undoubtedly created unprecedented economic opportunities, lifting millions out of poverty. However, its benefits have been distributed unevenly, exacerbating inequality within and between nations.', '"Exacerbating inequality" = làm trầm trọng thêm bất bình đẳng → making worse.'),
('What is the central paradox discussed in the passage?', 'multiple_choice', '["Technology connects us yet increases loneliness", "The internet is too slow", "Phones are too expensive", "Social media is always beneficial"]', 'Technology connects us yet increases loneliness', 'C1', 'reading', 1.2, 'The digital age has brought about a peculiar paradox: while technology has made communication easier and more accessible than ever before, rates of loneliness and social isolation have paradoxically increased. Researchers suggest that the quality, rather than quantity, of our digital interactions is key. Superficial online connections often fail to provide the emotional depth of face-to-face relationships.', 'Nghịch lý: công nghệ kết nối mọi người nhưng sự cô đơn lại tăng → Technology connects us yet increases loneliness.'),
('According to the passage, what matters more than quantity of interactions?', 'multiple_choice', '["Quality of interactions", "Speed of internet", "Number of followers", "Time spent online"]', 'Quality of interactions', 'C1', 'reading', 1.0, 'The digital age has brought about a peculiar paradox: while technology has made communication easier and more accessible than ever before, rates of loneliness and social isolation have paradoxically increased. Researchers suggest that the quality, rather than quantity, of our digital interactions is key.', 'Đoạn văn: "the quality, rather than quantity, of our digital interactions is key."'),
('What is the author''s view on renewable energy transition?', 'multiple_choice', '["It is necessary despite significant challenges", "It is impossible to achieve", "It will happen naturally", "It is too expensive to pursue"]', 'It is necessary despite significant challenges', 'C1', 'reading', 1.1, 'The transition to renewable energy sources, while imperative for mitigating climate change, presents formidable technical and economic challenges. Energy storage remains a critical bottleneck, and the intermittent nature of solar and wind power requires substantial grid modernization. Nevertheless, the rapidly declining costs of renewable technologies and growing political will suggest that a sustainable energy future is within reach.', 'Tác giả thừa nhận thách thức nhưng vẫn lạc quan về tương lai năng lượng tái tạo.');

-- C1 - Listening (3)
INSERT INTO `placement_questions` (`question_text`, `question_type`, `options_json`, `correct_answer`, `cefr_level`, `skill_type`, `difficulty_weight`, `passage`, `explanation`) VALUES
('(Nghe) What is the speaker''s main argument about education reform?', 'multiple_choice', '["Standardized testing should be replaced with holistic assessment", "More tests are needed", "Teachers should be paid less", "Schools should close earlier"]', 'Standardized testing should be replaced with holistic assessment', 'C1', 'listening', 1.2, 'The current education system relies far too heavily on standardized testing, which narrows the curriculum and fails to measure critical thinking, creativity, and collaboration. A holistic assessment approach that includes portfolios, project-based evaluations, and peer reviews would provide a much more accurate picture of student capabilities.', 'Diễn giả lập luận rằng kiểm tra chuẩn hóa nên được thay thế bằng đánh giá toàn diện.'),
('(Nghe) How does the speaker support their position?', 'multiple_choice', '["By citing research on student development", "With personal anecdotes only", "By attacking opponents", "Without any evidence"]', 'By citing research on student development', 'C1', 'listening', 1.1, 'Research conducted by the University of Cambridge over a ten-year period has demonstrated that students assessed through diverse methods show significantly higher levels of engagement and long-term retention compared to those evaluated solely through standardized tests.', 'Diễn giả trích dẫn nghiên cứu về phát triển học sinh để hỗ trợ lập luận.'),
('(Nghe) What does the expert predict about the economy?', 'multiple_choice', '["A moderate recession followed by recovery", "Continued rapid growth", "Immediate collapse", "No change at all"]', 'A moderate recession followed by recovery', 'C1', 'listening', 1.2, 'Based on current economic indicators, we are likely to experience a moderate recession over the next two quarters. However, given the strong fundamentals and the central bank''s proactive measures, recovery should begin by the end of the fiscal year.', 'Chuyên gia dự đoán suy thoái nhẹ sau đó phục hồi.');
