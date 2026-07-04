-- ============================================
-- Seed: Cau hoi cho Final Exams (15 bai thi cuoi khoa)
-- Dung fill_blank de tranh JSON quotes issues
-- ============================================

-- Final 1: Starter 1 (course 34)
INSERT INTO `questions` (`test_id`, `question_text`, `question_type`, `options_json`, `correct_answer`, `points`, `sort_order`) VALUES
((SELECT id FROM tests WHERE title LIKE 'Final Exam: English Starter 1' AND is_final=1 LIMIT 1), 'How do you say "Xin chao" in English?', 'fill_blank', NULL, 'Hello', 2, 1),
((SELECT id FROM tests WHERE title LIKE 'Final Exam: English Starter 1' AND is_final=1 LIMIT 1), 'Complete: "My ___ is John."', 'fill_blank', NULL, 'name', 2, 2),
((SELECT id FROM tests WHERE title LIKE 'Final Exam: English Starter 1' AND is_final=1 LIMIT 1), 'What number is this word: "five"?', 'fill_blank', NULL, '5', 2, 3),
((SELECT id FROM tests WHERE title LIKE 'Final Exam: English Starter 1' AND is_final=1 LIMIT 1), 'Complete: "How ___ you?"', 'fill_blank', NULL, 'are', 2, 4),
((SELECT id FROM tests WHERE title LIKE 'Final Exam: English Starter 1' AND is_final=1 LIMIT 1), '"Mother" means ___ in Vietnamese.', 'fill_blank', NULL, 'Me', 2, 5);

-- Final 2: Starter 2 (course 35)
INSERT INTO `questions` (`test_id`, `question_text`, `question_type`, `options_json`, `correct_answer`, `points`, `sort_order`) VALUES
((SELECT id FROM tests WHERE title LIKE 'Final Exam: English Starter 2' AND is_final=1 LIMIT 1), 'What time is "half past three"? Write the number.', 'fill_blank', NULL, '3:30', 2, 1),
((SELECT id FROM tests WHERE title LIKE 'Final Exam: English Starter 2' AND is_final=1 LIMIT 1), 'Which color is the sky on a clear day?', 'fill_blank', NULL, 'Blue', 2, 2),
((SELECT id FROM tests WHERE title LIKE 'Final Exam: English Starter 2' AND is_final=1 LIMIT 1), 'I ___ breakfast at 7 AM every day. (eat/eats)', 'fill_blank', NULL, 'eat', 2, 3),
((SELECT id FROM tests WHERE title LIKE 'Final Exam: English Starter 2' AND is_final=1 LIMIT 1), 'The opposite of "big" is ___.', 'fill_blank', NULL, 'small', 2, 4),
((SELECT id FROM tests WHERE title LIKE 'Final Exam: English Starter 2' AND is_final=1 LIMIT 1), '"It is a red car" — Is this sentence correct? (yes/no)', 'fill_blank', NULL, 'yes', 2, 5);

-- Final 3: Starter 3 (course 36)
INSERT INTO `questions` (`test_id`, `question_text`, `question_type`, `options_json`, `correct_answer`, `points`, `sort_order`) VALUES
((SELECT id FROM tests WHERE title LIKE 'Final Exam: English Starter 3' AND is_final=1 LIMIT 1), 'When it is raining, the weather is ___.', 'fill_blank', NULL, 'wet', 2, 1),
((SELECT id FROM tests WHERE title LIKE 'Final Exam: English Starter 3' AND is_final=1 LIMIT 1), 'I would like to ___ a coffee, please. (order/drink)', 'fill_blank', NULL, 'order', 2, 2),
((SELECT id FROM tests WHERE title LIKE 'Final Exam: English Starter 3' AND is_final=1 LIMIT 1), 'An apple is a type of ___.', 'fill_blank', NULL, 'fruit', 2, 3),
((SELECT id FROM tests WHERE title LIKE 'Final Exam: English Starter 3' AND is_final=1 LIMIT 1), 'How much ___ this shirt? (is/are)', 'fill_blank', NULL, 'is', 2, 4),
((SELECT id FROM tests WHERE title LIKE 'Final Exam: English Starter 3' AND is_final=1 LIMIT 1), 'I go shopping ___ the weekend. (at/on/in)', 'fill_blank', NULL, 'at', 2, 5);

-- Final 4: Basic 1 (course 37)
INSERT INTO `questions` (`test_id`, `question_text`, `question_type`, `options_json`, `correct_answer`, `points`, `sort_order`) VALUES
((SELECT id FROM tests WHERE title LIKE 'Final Exam: English Basic 1' AND is_final=1 LIMIT 1), 'Where can you buy a train ticket?', 'fill_blank', NULL, 'At the station', 2, 1),
((SELECT id FROM tests WHERE title LIKE 'Final Exam: English Basic 1' AND is_final=1 LIMIT 1), 'Excuse me, how do I ___ to the museum? (get/go)', 'fill_blank', NULL, 'get', 2, 2),
((SELECT id FROM tests WHERE title LIKE 'Final Exam: English Basic 1' AND is_final=1 LIMIT 1), 'I need to ___ a hotel room. (book/buy)', 'fill_blank', NULL, 'book', 2, 3),
((SELECT id FROM tests WHERE title LIKE 'Final Exam: English Basic 1' AND is_final=1 LIMIT 1), 'The flight ___ at 6 PM. (departs/arrives)', 'fill_blank', NULL, 'departs', 2, 4),
((SELECT id FROM tests WHERE title LIKE 'Final Exam: English Basic 1' AND is_final=1 LIMIT 1), '"Luggage" means ___ in Vietnamese.', 'fill_blank', NULL, 'Hanh ly', 2, 5);

-- Final 5: Basic 2 (course 38)
INSERT INTO `questions` (`test_id`, `question_text`, `question_type`, `options_json`, `correct_answer`, `points`, `sort_order`) VALUES
((SELECT id FROM tests WHERE title LIKE 'Final Exam: English Basic 2' AND is_final=1 LIMIT 1), 'I enjoy ___ football on weekends. (playing/play)', 'fill_blank', NULL, 'playing', 2, 1),
((SELECT id FROM tests WHERE title LIKE 'Final Exam: English Basic 2' AND is_final=1 LIMIT 1), 'You should ___ more water every day. (drink/drinks)', 'fill_blank', NULL, 'drink', 2, 2),
((SELECT id FROM tests WHERE title LIKE 'Final Exam: English Basic 2' AND is_final=1 LIMIT 1), '"Exercise" is good for your ___.', 'fill_blank', NULL, 'health', 2, 3),
((SELECT id FROM tests WHERE title LIKE 'Final Exam: English Basic 2' AND is_final=1 LIMIT 1), 'My favorite ___ is swimming. (sport/food)', 'fill_blank', NULL, 'sport', 2, 4),
((SELECT id FROM tests WHERE title LIKE 'Final Exam: English Basic 2' AND is_final=1 LIMIT 1), 'She ___ to the gym three times a week. (goes/go)', 'fill_blank', NULL, 'goes', 2, 5);

-- Final 6: Basic 3 (course 39)
INSERT INTO `questions` (`test_id`, `question_text`, `question_type`, `options_json`, `correct_answer`, `points`, `sort_order`) VALUES
((SELECT id FROM tests WHERE title LIKE 'Final Exam: English Basic 3' AND is_final=1 LIMIT 1), 'She is a ___. She works at a school. (teacher/doctor)', 'fill_blank', NULL, 'teacher', 2, 1),
((SELECT id FROM tests WHERE title LIKE 'Final Exam: English Basic 3' AND is_final=1 LIMIT 1), 'I ___ my homework every evening. (do/make)', 'fill_blank', NULL, 'do', 2, 2),
((SELECT id FROM tests WHERE title LIKE 'Final Exam: English Basic 3' AND is_final=1 LIMIT 1), 'A ___ is used to send emails. (computer/phone)', 'fill_blank', NULL, 'computer', 2, 3),
((SELECT id FROM tests WHERE title LIKE 'Final Exam: English Basic 3' AND is_final=1 LIMIT 1), 'He wants to ___ a new language. (learn/teach)', 'fill_blank', NULL, 'learn', 2, 4),
((SELECT id FROM tests WHERE title LIKE 'Final Exam: English Basic 3' AND is_final=1 LIMIT 1), '"Graduate" means to ___ from a school. (complete/start)', 'fill_blank', NULL, 'complete', 2, 5);

-- Final 7: Intermediate 1 (course 40)
INSERT INTO `questions` (`test_id`, `question_text`, `question_type`, `options_json`, `correct_answer`, `points`, `sort_order`) VALUES
((SELECT id FROM tests WHERE title LIKE 'Final Exam: English Intermediate 1' AND is_final=1 LIMIT 1), 'We should ___ the environment by recycling. (protect/damage)', 'fill_blank', NULL, 'protect', 2, 1),
((SELECT id FROM tests WHERE title LIKE 'Final Exam: English Intermediate 1' AND is_final=1 LIMIT 1), 'She has a close ___ with her sister. (relationship/friendship)', 'fill_blank', NULL, 'relationship', 2, 2),
((SELECT id FROM tests WHERE title LIKE 'Final Exam: English Intermediate 1' AND is_final=1 LIMIT 1), 'Eating vegetables is ___ for your health. (good/bad)', 'fill_blank', NULL, 'good', 2, 3),
((SELECT id FROM tests WHERE title LIKE 'Final Exam: English Intermediate 1' AND is_final=1 LIMIT 1), 'If we do not act now, climate change will ___ worse. (get/become)', 'fill_blank', NULL, 'get', 2, 4),
((SELECT id FROM tests WHERE title LIKE 'Final Exam: English Intermediate 1' AND is_final=1 LIMIT 1), 'They have been friends ___ childhood. (since/for)', 'fill_blank', NULL, 'since', 2, 5);

-- Final 8: Intermediate 2 (course 41)
INSERT INTO `questions` (`test_id`, `question_text`, `question_type`, `options_json`, `correct_answer`, `points`, `sort_order`) VALUES
((SELECT id FROM tests WHERE title LIKE 'Final Exam: English Intermediate 2' AND is_final=1 LIMIT 1), 'Social media has changed how people ___. (communicate/eat)', 'fill_blank', NULL, 'communicate', 2, 1),
((SELECT id FROM tests WHERE title LIKE 'Final Exam: English Intermediate 2' AND is_final=1 LIMIT 1), 'Every country has its own ___ and traditions. (culture/food)', 'fill_blank', NULL, 'culture', 2, 2),
((SELECT id FROM tests WHERE title LIKE 'Final Exam: English Intermediate 2' AND is_final=1 LIMIT 1), 'Many students ___ to study abroad every year. (choose/refuse)', 'fill_blank', NULL, 'choose', 2, 3),
((SELECT id FROM tests WHERE title LIKE 'Final Exam: English Intermediate 2' AND is_final=1 LIMIT 1), 'The news ___ at 7 PM every evening. (airs/starts)', 'fill_blank', NULL, 'airs', 2, 4),
((SELECT id FROM tests WHERE title LIKE 'Final Exam: English Intermediate 2' AND is_final=1 LIMIT 1), 'The ___ of this article is very interesting. (content/title)', 'fill_blank', NULL, 'content', 2, 5);

-- Final 9: Intermediate 3 (course 42)
INSERT INTO `questions` (`test_id`, `question_text`, `question_type`, `options_json`, `correct_answer`, `points`, `sort_order`) VALUES
((SELECT id FROM tests WHERE title LIKE 'Final Exam: English Intermediate 3' AND is_final=1 LIMIT 1), 'Before giving a ___, you should prepare carefully. (presentation/party)', 'fill_blank', NULL, 'presentation', 2, 1),
((SELECT id FROM tests WHERE title LIKE 'Final Exam: English Intermediate 3' AND is_final=1 LIMIT 1), 'Our company plans to ___ new markets next year. (enter/leave)', 'fill_blank', NULL, 'enter', 2, 2),
((SELECT id FROM tests WHERE title LIKE 'Final Exam: English Intermediate 3' AND is_final=1 LIMIT 1), 'A good ___ should be clear and concise. (report/email)', 'fill_blank', NULL, 'report', 2, 3),
((SELECT id FROM tests WHERE title LIKE 'Final Exam: English Intermediate 3' AND is_final=1 LIMIT 1), 'We need to ___ the budget before the deadline. (approve/reject)', 'fill_blank', NULL, 'approve', 2, 4),
((SELECT id FROM tests WHERE title LIKE 'Final Exam: English Intermediate 3' AND is_final=1 LIMIT 1), 'The meeting has been ___ to next Monday. (postponed/started)', 'fill_blank', NULL, 'postponed', 2, 5);

-- Final 10: Upper 1 (course 43)
INSERT INTO `questions` (`test_id`, `question_text`, `question_type`, `options_json`, `correct_answer`, `points`, `sort_order`) VALUES
((SELECT id FROM tests WHERE title LIKE 'Final Exam: English Upper 1' AND is_final=1 LIMIT 1), 'The report ___ by the manager yesterday. (was written / wrote)', 'fill_blank', NULL, 'was written', 2, 1),
((SELECT id FROM tests WHERE title LIKE 'Final Exam: English Upper 1' AND is_final=1 LIMIT 1), 'If I had studied harder, I ___ the exam. (would have passed / will pass)', 'fill_blank', NULL, 'would have passed', 2, 2),
((SELECT id FROM tests WHERE title LIKE 'Final Exam: English Upper 1' AND is_final=1 LIMIT 1), 'Not only ___ late, but he also forgot the documents. (did he arrive / he arrived)', 'fill_blank', NULL, 'did he arrive', 2, 3),
((SELECT id FROM tests WHERE title LIKE 'Final Exam: English Upper 1' AND is_final=1 LIMIT 1), 'Despite ___ hard, she did not pass the test. (studying/study)', 'fill_blank', NULL, 'studying', 2, 4),
((SELECT id FROM tests WHERE title LIKE 'Final Exam: English Upper 1' AND is_final=1 LIMIT 1), 'It is essential that everyone ___ on time. (be/is)', 'fill_blank', NULL, 'be', 2, 5);

-- Final 11: Upper 2 (course 44)
INSERT INTO `questions` (`test_id`, `question_text`, `question_type`, `options_json`, `correct_answer`, `points`, `sort_order`) VALUES
((SELECT id FROM tests WHERE title LIKE 'Final Exam: English Upper 2' AND is_final=1 LIMIT 1), 'The government should ___ more in renewable energy. (invest/investment)', 'fill_blank', NULL, 'invest', 2, 1),
((SELECT id FROM tests WHERE title LIKE 'Final Exam: English Upper 2' AND is_final=1 LIMIT 1), 'Many people ___ that education should be free. (argue/argument)', 'fill_blank', NULL, 'argue', 2, 2),
((SELECT id FROM tests WHERE title LIKE 'Final Exam: English Upper 2' AND is_final=1 LIMIT 1), 'The Industrial Revolution ___ in the 18th century. (began/ended)', 'fill_blank', NULL, 'began', 2, 3),
((SELECT id FROM tests WHERE title LIKE 'Final Exam: English Upper 2' AND is_final=1 LIMIT 1), 'Although it was raining, they ___ to go out. (decided/refused)', 'fill_blank', NULL, 'decided', 2, 4),
((SELECT id FROM tests WHERE title LIKE 'Final Exam: English Upper 2' AND is_final=1 LIMIT 1), 'Seldom ___ such a beautiful sunset. (have I seen / I have seen)', 'fill_blank', NULL, 'have I seen', 2, 5);

-- Final 12: Upper 3 (course 45)
INSERT INTO `questions` (`test_id`, `question_text`, `question_type`, `options_json`, `correct_answer`, `points`, `sort_order`) VALUES
((SELECT id FROM tests WHERE title LIKE 'Final Exam: English Upper 3' AND is_final=1 LIMIT 1), 'I look forward to ___ from you soon. (hearing/hear)', 'fill_blank', NULL, 'hearing', 2, 1),
((SELECT id FROM tests WHERE title LIKE 'Final Exam: English Upper 3' AND is_final=1 LIMIT 1), 'Could you please ___ me the report by Friday? (send/sending)', 'fill_blank', NULL, 'send', 2, 2),
((SELECT id FROM tests WHERE title LIKE 'Final Exam: English Upper 3' AND is_final=1 LIMIT 1), 'We need to ___ the contract before signing. (review/ignore)', 'fill_blank', NULL, 'review', 2, 3),
((SELECT id FROM tests WHERE title LIKE 'Final Exam: English Upper 3' AND is_final=1 LIMIT 1), 'She was promoted ___ her excellent performance. (due to / despite)', 'fill_blank', NULL, 'due to', 2, 4),
((SELECT id FROM tests WHERE title LIKE 'Final Exam: English Upper 3' AND is_final=1 LIMIT 1), 'The candidate ___ answers impressed the panel. (whose/who)', 'fill_blank', NULL, 'whose', 2, 5);

-- Final 13: Advanced 1 (course 46)
INSERT INTO `questions` (`test_id`, `question_text`, `question_type`, `options_json`, `correct_answer`, `points`, `sort_order`) VALUES
((SELECT id FROM tests WHERE title LIKE 'Final Exam: English Advanced 1' AND is_final=1 LIMIT 1), 'The research ___ that the hypothesis was correct. (demonstrated/refused)', 'fill_blank', NULL, 'demonstrated', 2, 1),
((SELECT id FROM tests WHERE title LIKE 'Final Exam: English Advanced 1' AND is_final=1 LIMIT 1), 'It is imperative that the data ___ verified before publication. (be/is)', 'fill_blank', NULL, 'be', 2, 2),
((SELECT id FROM tests WHERE title LIKE 'Final Exam: English Advanced 1' AND is_final=1 LIMIT 1), 'The author argues that globalization has ___ inequality. (exacerbated/reduced)', 'fill_blank', NULL, 'exacerbated', 2, 3),
((SELECT id FROM tests WHERE title LIKE 'Final Exam: English Advanced 1' AND is_final=1 LIMIT 1), 'Were it not for the grant, the research ___. (would have failed / will fail)', 'fill_blank', NULL, 'would have failed', 2, 4),
((SELECT id FROM tests WHERE title LIKE 'Final Exam: English Advanced 1' AND is_final=1 LIMIT 1), 'A ___ analysis of the data revealed unexpected patterns. (thorough/quick)', 'fill_blank', NULL, 'thorough', 2, 5);

-- Final 14: Advanced 2 (course 47)
INSERT INTO `questions` (`test_id`, `question_text`, `question_type`, `options_json`, `correct_answer`, `points`, `sort_order`) VALUES
((SELECT id FROM tests WHERE title LIKE 'Final Exam: English Advanced 2' AND is_final=1 LIMIT 1), 'The novel explores the ___ of human existence. (complexity/simplicity)', 'fill_blank', NULL, 'complexity', 2, 1),
((SELECT id FROM tests WHERE title LIKE 'Final Exam: English Advanced 2' AND is_final=1 LIMIT 1), 'Shakespeare''s works remain ___ centuries after his death. (relevant/irrelevant)', 'fill_blank', NULL, 'relevant', 2, 2),
((SELECT id FROM tests WHERE title LIKE 'Final Exam: English Advanced 2' AND is_final=1 LIMIT 1), 'The artist''s style is highly ___ and recognizable. (distinctive/ordinary)', 'fill_blank', NULL, 'distinctive', 2, 3),
((SELECT id FROM tests WHERE title LIKE 'Final Exam: English Advanced 2' AND is_final=1 LIMIT 1), 'The philosopher argued that morality is not ___. (absolute/relative)', 'fill_blank', NULL, 'absolute', 2, 4),
((SELECT id FROM tests WHERE title LIKE 'Final Exam: English Advanced 2' AND is_final=1 LIMIT 1), 'His ___ critique of society sparked widespread debate. (incisive/dull)', 'fill_blank', NULL, 'incisive', 2, 5);

-- Final 15: Advanced 3 (course 48)
INSERT INTO `questions` (`test_id`, `question_text`, `question_type`, `options_json`, `correct_answer`, `points`, `sort_order`) VALUES
((SELECT id FROM tests WHERE title LIKE 'Final Exam: English Advanced 3' AND is_final=1 LIMIT 1), 'Her ___ of the English language is truly remarkable. (mastery/ignorance)', 'fill_blank', NULL, 'mastery', 2, 1),
((SELECT id FROM tests WHERE title LIKE 'Final Exam: English Advanced 3' AND is_final=1 LIMIT 1), 'The subtle ___ in his speech conveyed more than words. (nuance/error)', 'fill_blank', NULL, 'nuance', 2, 2),
((SELECT id FROM tests WHERE title LIKE 'Final Exam: English Advanced 3' AND is_final=1 LIMIT 1), 'She spoke with such ___ that everyone was convinced. (eloquence/confusion)', 'fill_blank', NULL, 'eloquence', 2, 3),
((SELECT id FROM tests WHERE title LIKE 'Final Exam: English Advanced 3' AND is_final=1 LIMIT 1), 'The writer''s ___ use of metaphors enriched the narrative. (judicious/careless)', 'fill_blank', NULL, 'judicious', 2, 4),
((SELECT id FROM tests WHERE title LIKE 'Final Exam: English Advanced 3' AND is_final=1 LIMIT 1), 'He could ___ between seemingly identical concepts. (differentiate/confuse)', 'fill_blank', NULL, 'differentiate', 2, 5);
