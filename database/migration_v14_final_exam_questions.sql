-- ============================================
-- Migration v14: Bổ sung câu hỏi true_false + fill_blank cho final exams
-- Mỗi exam: +5 true_false (sort 21-25) + 5 fill_blank (sort 26-30)
-- ============================================

-- A1: English Starter 1
INSERT INTO `questions` (`test_id`,`question_text`,`question_type`,`options_json`,`correct_answer`,`points`,`sort_order`) VALUES
((SELECT id FROM tests WHERE course_id=34 AND is_final=1 LIMIT 1),'Anna is 14 years old. (True/False)','true_false','["True","False"]','False',2,21),
((SELECT id FROM tests WHERE course_id=34 AND is_final=1 LIMIT 1),'"Hello" is a greeting word. (True/False)','true_false','["True","False"]','True',2,22),
((SELECT id FROM tests WHERE course_id=34 AND is_final=1 LIMIT 1),'The word "Goodbye" means "Xin chào". (True/False)','true_false','["True","False"]','False',2,23),
((SELECT id FROM tests WHERE course_id=34 AND is_final=1 LIMIT 1),'In English, we say "Good morning" before noon. (True/False)','true_false','["True","False"]','True',2,24),
((SELECT id FROM tests WHERE course_id=34 AND is_final=1 LIMIT 1),'"Thank you" means "Xin lỗi". (True/False)','true_false','["True","False"]','False',2,25),
((SELECT id FROM tests WHERE course_id=34 AND is_final=1 LIMIT 1),'Complete: "Hello, my name ___ John."','fill_blank',NULL,'is',2,26),
((SELECT id FROM tests WHERE course_id=34 AND is_final=1 LIMIT 1),'How do you say "Tạm biệt" in English?','fill_blank',NULL,'Goodbye',2,27),
((SELECT id FROM tests WHERE course_id=34 AND is_final=1 LIMIT 1),'Complete: "Nice to ___ you."','fill_blank',NULL,'meet',2,28),
((SELECT id FROM tests WHERE course_id=34 AND is_final=1 LIMIT 1),'How ___ you today? (am/is/are) Write the correct word.','fill_blank',NULL,'are',2,29),
((SELECT id FROM tests WHERE course_id=34 AND is_final=1 LIMIT 1),'Write the number: "seven" = ___.','fill_blank',NULL,'7',2,30);

-- A1: English Starter 2
INSERT INTO `questions` (`test_id`,`question_text`,`question_type`,`options_json`,`correct_answer`,`points`,`sort_order`) VALUES
((SELECT id FROM tests WHERE course_id=35 AND is_final=1 LIMIT 1),'The word "red" is a color. (True/False)','true_false','["True","False"]','True',2,21),
((SELECT id FROM tests WHERE course_id=35 AND is_final=1 LIMIT 1),'"Morning" comes after "Evening". (True/False)','true_false','["True","False"]','False',2,22),
((SELECT id FROM tests WHERE course_id=35 AND is_final=1 LIMIT 1),'We use "a" before words that start with a vowel sound. (True/False)','true_false','["True","False"]','False',2,23),
((SELECT id FROM tests WHERE course_id=35 AND is_final=1 LIMIT 1),'"Big" and "small" are opposites. (True/False)','true_false','["True","False"]','True',2,24),
((SELECT id FROM tests WHERE course_id=35 AND is_final=1 LIMIT 1),'Sunday is the first day of the week. (True/False)','true_false','["True","False"]','True',2,25),
((SELECT id FROM tests WHERE course_id=35 AND is_final=1 LIMIT 1),'What color is the sky on a sunny day?','fill_blank',NULL,'Blue',2,26),
((SELECT id FROM tests WHERE course_id=35 AND is_final=1 LIMIT 1),'Complete: "I ___ breakfast at 7 AM." (eat/eats)','fill_blank',NULL,'eat',2,27),
((SELECT id FROM tests WHERE course_id=35 AND is_final=1 LIMIT 1),'The opposite of "hot" is ___.','fill_blank',NULL,'cold',2,28),
((SELECT id FROM tests WHERE course_id=35 AND is_final=1 LIMIT 1),'What time is "a quarter past two"? Write as H:MM.','fill_blank',NULL,'2:15',2,29),
((SELECT id FROM tests WHERE course_id=35 AND is_final=1 LIMIT 1),'Complete: "She ___ a student." (is/are)','fill_blank',NULL,'is',2,30);

-- A1: English Starter 3
INSERT INTO `questions` (`test_id`,`question_text`,`question_type`,`options_json`,`correct_answer`,`points`,`sort_order`) VALUES
((SELECT id FROM tests WHERE course_id=36 AND is_final=1 LIMIT 1),'An apple is a vegetable. (True/False)','true_false','["True","False"]','False',2,21),
((SELECT id FROM tests WHERE course_id=36 AND is_final=1 LIMIT 1),'We use an umbrella when it rains. (True/False)','true_false','["True","False"]','True',2,22),
((SELECT id FROM tests WHERE course_id=36 AND is_final=1 LIMIT 1),'"How much" is used for countable nouns. (True/False)','true_false','["True","False"]','False',2,23),
((SELECT id FROM tests WHERE course_id=36 AND is_final=1 LIMIT 1),'Winter is the hottest season. (True/False)','true_false','["True","False"]','False',2,24),
((SELECT id FROM tests WHERE course_id=36 AND is_final=1 LIMIT 1),'The word "delicious" describes food that tastes good. (True/False)','true_false','["True","False"]','True',2,25),
((SELECT id FROM tests WHERE course_id=36 AND is_final=1 LIMIT 1),'Complete: "I would like ___ buy a ticket." (to/for)','fill_blank',NULL,'to',2,26),
((SELECT id FROM tests WHERE course_id=36 AND is_final=1 LIMIT 1),'How much ___ this T-shirt cost? (do/does)','fill_blank',NULL,'does',2,27),
((SELECT id FROM tests WHERE course_id=36 AND is_final=1 LIMIT 1),'Complete: "Can I ___ you?" (help/helps)','fill_blank',NULL,'help',2,28),
((SELECT id FROM tests WHERE course_id=36 AND is_final=1 LIMIT 1),'"It is raining outside" — write the past tense of "is".','fill_blank',NULL,'was',2,29),
((SELECT id FROM tests WHERE course_id=36 AND is_final=1 LIMIT 1),'Complete: "I go to the market ___ Sundays." (in/on/at)','fill_blank',NULL,'on',2,30);

-- A2: English Basic 1
INSERT INTO `questions` (`test_id`,`question_text`,`question_type`,`options_json`,`correct_answer`,`points`,`sort_order`) VALUES
((SELECT id FROM tests WHERE course_id=37 AND is_final=1 LIMIT 1),'A passport is needed for international travel. (True/False)','true_false','["True","False"]','True',2,21),
((SELECT id FROM tests WHERE course_id=37 AND is_final=1 LIMIT 1),'You can buy train tickets at the airport. (True/False)','true_false','["True","False"]','False',2,22),
((SELECT id FROM tests WHERE course_id=37 AND is_final=1 LIMIT 1),'"Depart" means to leave a place. (True/False)','true_false','["True","False"]','True',2,23),
((SELECT id FROM tests WHERE course_id=37 AND is_final=1 LIMIT 1),'"Luggage" and "baggage" mean the same thing. (True/False)','true_false','["True","False"]','True',2,24),
((SELECT id FROM tests WHERE course_id=37 AND is_final=1 LIMIT 1),'A single ticket is for a round trip. (True/False)','true_false','["True","False"]','False',2,25),
((SELECT id FROM tests WHERE course_id=37 AND is_final=1 LIMIT 1),'Complete: "Excuse me, where ___ the bus station?" (is/are)','fill_blank',NULL,'is',2,26),
((SELECT id FROM tests WHERE course_id=37 AND is_final=1 LIMIT 1),'I want to ___ a room for two nights. (book/buy)','fill_blank',NULL,'book',2,27),
((SELECT id FROM tests WHERE course_id=37 AND is_final=1 LIMIT 1),'Complete: "What time does the train ___?" (depart/arrive)','fill_blank',NULL,'depart',2,28),
((SELECT id FROM tests WHERE course_id=37 AND is_final=1 LIMIT 1),'"Luggage" means ___ in Vietnamese.','fill_blank',NULL,'Hành lý',2,29),
((SELECT id FROM tests WHERE course_id=37 AND is_final=1 LIMIT 1),'Complete: "I need ___ taxi to the hotel." (a/an)','fill_blank',NULL,'a',2,30);

-- A2: English Basic 2
INSERT INTO `questions` (`test_id`,`question_text`,`question_type`,`options_json`,`correct_answer`,`points`,`sort_order`) VALUES
((SELECT id FROM tests WHERE course_id=38 AND is_final=1 LIMIT 1),'Sports and exercise are good for your health. (True/False)','true_false','["True","False"]','True',2,21),
((SELECT id FROM tests WHERE course_id=38 AND is_final=1 LIMIT 1),'The word "hobby" means a job. (True/False)','true_false','["True","False"]','False',2,22),
((SELECT id FROM tests WHERE course_id=38 AND is_final=1 LIMIT 1),'You should drink at least 2 liters of water per day. (True/False)','true_false','["True","False"]','True',2,23),
((SELECT id FROM tests WHERE course_id=38 AND is_final=1 LIMIT 1),'The phrase "once a week" means 7 times a week. (True/False)','true_false','["True","False"]','False',2,24),
((SELECT id FROM tests WHERE course_id=38 AND is_final=1 LIMIT 1),'"Enjoy" and "like" have similar meanings. (True/False)','true_false','["True","False"]','True',2,25),
((SELECT id FROM tests WHERE course_id=38 AND is_final=1 LIMIT 1),'Complete: "I enjoy ___ to music." (listen/listening)','fill_blank',NULL,'listening',2,26),
((SELECT id FROM tests WHERE course_id=38 AND is_final=1 LIMIT 1),'She ___ to the gym every morning. (go/goes)','fill_blank',NULL,'goes',2,27),
((SELECT id FROM tests WHERE course_id=38 AND is_final=1 LIMIT 1),'Complete: "My favorite sport ___ football." (is/are)','fill_blank',NULL,'is',2,28),
((SELECT id FROM tests WHERE course_id=38 AND is_final=1 LIMIT 1),'You should ___ plenty of water. (drink/drinking)','fill_blank',NULL,'drink',2,29),
((SELECT id FROM tests WHERE course_id=38 AND is_final=1 LIMIT 1),'Complete: "He likes ___ video games." (play/playing)','fill_blank',NULL,'playing',2,30);

-- A2: English Basic 3
INSERT INTO `questions` (`test_id`,`question_text`,`question_type`,`options_json`,`correct_answer`,`points`,`sort_order`) VALUES
((SELECT id FROM tests WHERE course_id=39 AND is_final=1 LIMIT 1),'A teacher works at a hospital. (True/False)','true_false','["True","False"]','False',2,21),
((SELECT id FROM tests WHERE course_id=39 AND is_final=1 LIMIT 1),'You can use email to communicate with people. (True/False)','true_false','["True","False"]','True',2,22),
((SELECT id FROM tests WHERE course_id=39 AND is_final=1 LIMIT 1),'"Graduate" means to start school. (True/False)','true_false','["True","False"]','False',2,23),
((SELECT id FROM tests WHERE course_id=39 AND is_final=1 LIMIT 1),'"Homework" is work done at home after school. (True/False)','true_false','["True","False"]','True',2,24),
((SELECT id FROM tests WHERE course_id=39 AND is_final=1 LIMIT 1),'A doctor works at a school. (True/False)','true_false','["True","False"]','False',2,25),
((SELECT id FROM tests WHERE course_id=39 AND is_final=1 LIMIT 1),'Complete: "She is a ___. She teaches students." (teacher/doctor)','fill_blank',NULL,'teacher',2,26),
((SELECT id FROM tests WHERE course_id=39 AND is_final=1 LIMIT 1),'I ___ my homework every evening. (do/make)','fill_blank',NULL,'do',2,27),
((SELECT id FROM tests WHERE course_id=39 AND is_final=1 LIMIT 1),'Complete: "He wants to ___ English." (learn/teach)','fill_blank',NULL,'learn',2,28),
((SELECT id FROM tests WHERE course_id=39 AND is_final=1 LIMIT 1),'A ___ is used to write emails. (computer/telephone)','fill_blank',NULL,'computer',2,29),
((SELECT id FROM tests WHERE course_id=39 AND is_final=1 LIMIT 1),'Complete: "I go to ___ at 7 AM." (school/work)','fill_blank',NULL,'school',2,30);

-- B1: English Intermediate 1
INSERT INTO `questions` (`test_id`,`question_text`,`question_type`,`options_json`,`correct_answer`,`points`,`sort_order`) VALUES
((SELECT id FROM tests WHERE course_id=40 AND is_final=1 LIMIT 1),'Climate change is caused only by natural factors. (True/False)','true_false','["True","False"]','False',2,21),
((SELECT id FROM tests WHERE course_id=40 AND is_final=1 LIMIT 1),'Recycling helps protect the environment. (True/False)','true_false','["True","False"]','True',2,22),
((SELECT id FROM tests WHERE course_id=40 AND is_final=1 LIMIT 1),'"Relationship" only refers to romantic connections. (True/False)','true_false','["True","False"]','False',2,23),
((SELECT id FROM tests WHERE course_id=40 AND is_final=1 LIMIT 1),'Eating vegetables is beneficial for health. (True/False)','true_false','["True","False"]','True',2,24),
((SELECT id FROM tests WHERE course_id=40 AND is_final=1 LIMIT 1),'Pollution has no effect on human health. (True/False)','true_false','["True","False"]','False',2,25),
((SELECT id FROM tests WHERE course_id=40 AND is_final=1 LIMIT 1),'Complete: "We must ___ the environment." (protect/destroy)','fill_blank',NULL,'protect',2,26),
((SELECT id FROM tests WHERE course_id=40 AND is_final=1 LIMIT 1),'They have been friends ___ 2010. (since/for)','fill_blank',NULL,'since',2,27),
((SELECT id FROM tests WHERE course_id=40 AND is_final=1 LIMIT 1),'Complete: "Regular exercise is ___ for your health." (good/bad)','fill_blank',NULL,'good',2,28),
((SELECT id FROM tests WHERE course_id=40 AND is_final=1 LIMIT 1),'If we ___ now, the problem will get worse. (act/ignore)','fill_blank',NULL,'ignore',2,29),
((SELECT id FROM tests WHERE course_id=40 AND is_final=1 LIMIT 1),'Complete: "She ___ yoga three times a week." (does/do)','fill_blank',NULL,'does',2,30);

-- B1: English Intermediate 2
INSERT INTO `questions` (`test_id`,`question_text`,`question_type`,`options_json`,`correct_answer`,`points`,`sort_order`) VALUES
((SELECT id FROM tests WHERE course_id=41 AND is_final=1 LIMIT 1),'Social media has no impact on communication. (True/False)','true_false','["True","False"]','False',2,21),
((SELECT id FROM tests WHERE course_id=41 AND is_final=1 LIMIT 1),'Every country has unique traditions and customs. (True/False)','true_false','["True","False"]','True',2,22),
((SELECT id FROM tests WHERE course_id=41 AND is_final=1 LIMIT 1),'The word "culture" only refers to art and music. (True/False)','true_false','["True","False"]','False',2,23),
((SELECT id FROM tests WHERE course_id=41 AND is_final=1 LIMIT 1),'"Broadcast" means to send out a program on TV or radio. (True/False)','true_false','["True","False"]','True',2,24),
((SELECT id FROM tests WHERE course_id=41 AND is_final=1 LIMIT 1),'Education is not important for career development. (True/False)','true_false','["True","False"]','False',2,25),
((SELECT id FROM tests WHERE course_id=41 AND is_final=1 LIMIT 1),'Complete: "Social media has changed how people ___." (communicate/eat)','fill_blank',NULL,'communicate',2,26),
((SELECT id FROM tests WHERE course_id=41 AND is_final=1 LIMIT 1),'Many students ___ to study abroad every year. (choose/refuse)','fill_blank',NULL,'choose',2,27),
((SELECT id FROM tests WHERE course_id=41 AND is_final=1 LIMIT 1),'Complete: "The news ___ at 7 PM every evening." (airs/starts)','fill_blank',NULL,'airs',2,28),
((SELECT id FROM tests WHERE course_id=41 AND is_final=1 LIMIT 1),'Each country has its own ___ and traditions. (culture/food)','fill_blank',NULL,'culture',2,29),
((SELECT id FROM tests WHERE course_id=41 AND is_final=1 LIMIT 1),'Complete: "The ___ of this article is very interesting." (content/title)','fill_blank',NULL,'content',2,30);

-- B1: English Intermediate 3
INSERT INTO `questions` (`test_id`,`question_text`,`question_type`,`options_json`,`correct_answer`,`points`,`sort_order`) VALUES
((SELECT id FROM tests WHERE course_id=42 AND is_final=1 LIMIT 1),'A presentation should be prepared carefully. (True/False)','true_false','["True","False"]','True',2,21),
((SELECT id FROM tests WHERE course_id=42 AND is_final=1 LIMIT 1),'"Postpone" means to move something earlier. (True/False)','true_false','["True","False"]','False',2,22),
((SELECT id FROM tests WHERE course_id=42 AND is_final=1 LIMIT 1),'A good report should be clear and concise. (True/False)','true_false','["True","False"]','True',2,23),
((SELECT id FROM tests WHERE course_id=42 AND is_final=1 LIMIT 1),'The word "budget" refers to a plan for spending money. (True/False)','true_false','["True","False"]','True',2,24),
((SELECT id FROM tests WHERE course_id=42 AND is_final=1 LIMIT 1),'"Deadline" means the starting date of a project. (True/False)','true_false','["True","False"]','False',2,25),
((SELECT id FROM tests WHERE course_id=42 AND is_final=1 LIMIT 1),'Complete: "Before giving a ___, prepare carefully." (presentation/party)','fill_blank',NULL,'presentation',2,26),
((SELECT id FROM tests WHERE course_id=42 AND is_final=1 LIMIT 1),'Our company plans to ___ new markets. (enter/leave)','fill_blank',NULL,'enter',2,27),
((SELECT id FROM tests WHERE course_id=42 AND is_final=1 LIMIT 1),'Complete: "We need to ___ the budget before Friday." (approve/reject)','fill_blank',NULL,'approve',2,28),
((SELECT id FROM tests WHERE course_id=42 AND is_final=1 LIMIT 1),'The meeting was ___ to next Monday. (postponed/started)','fill_blank',NULL,'postponed',2,29),
((SELECT id FROM tests WHERE course_id=42 AND is_final=1 LIMIT 1),'Complete: "A good ___ should be clear and concise." (report/email)','fill_blank',NULL,'report',2,30);

-- B2: English Upper 1
INSERT INTO `questions` (`test_id`,`question_text`,`question_type`,`options_json`,`correct_answer`,`points`,`sort_order`) VALUES
((SELECT id FROM tests WHERE course_id=43 AND is_final=1 LIMIT 1),'Passive voice uses "to be + past participle". (True/False)','true_false','["True","False"]','True',2,21),
((SELECT id FROM tests WHERE course_id=43 AND is_final=1 LIMIT 1),'"Despite" is followed by a full clause with subject and verb. (True/False)','true_false','["True","False"]','False',2,22),
((SELECT id FROM tests WHERE course_id=43 AND is_final=1 LIMIT 1),'Third conditional describes unreal past situations. (True/False)','true_false','["True","False"]','True',2,23),
((SELECT id FROM tests WHERE course_id=43 AND is_final=1 LIMIT 1),'"Not only" at the beginning requires subject-verb inversion. (True/False)','true_false','["True","False"]','True',2,24),
((SELECT id FROM tests WHERE course_id=43 AND is_final=1 LIMIT 1),'Subjunctive mood uses "were" for all subjects. (True/False)','true_false','["True","False"]','True',2,25),
((SELECT id FROM tests WHERE course_id=43 AND is_final=1 LIMIT 1),'The report ___ by the manager yesterday. (write → passive)','fill_blank',NULL,'was written',2,26),
((SELECT id FROM tests WHERE course_id=43 AND is_final=1 LIMIT 1),'Complete: "If I ___ richer, I would travel more." (was/were)','fill_blank',NULL,'were',2,27),
((SELECT id FROM tests WHERE course_id=43 AND is_final=1 LIMIT 1),'Not only ___ late, he also forgot the documents. (did he arrive/he arrived)','fill_blank',NULL,'did he arrive',2,28),
((SELECT id FROM tests WHERE course_id=43 AND is_final=1 LIMIT 1),'Complete: "Despite ___ hard, she failed." (study/studying)','fill_blank',NULL,'studying',2,29),
((SELECT id FROM tests WHERE course_id=43 AND is_final=1 LIMIT 1),'It is essential that everyone ___ on time. (be/is)','fill_blank',NULL,'be',2,30);

-- B2: English Upper 2 + 3 + C1: English Advanced 1-3 (similar pattern)
-- B2: English Upper 2
INSERT INTO `questions` (`test_id`,`question_text`,`question_type`,`options_json`,`correct_answer`,`points`,`sort_order`) VALUES
((SELECT id FROM tests WHERE course_id=44 AND is_final=1 LIMIT 1),'The Industrial Revolution started in the 18th century. (True/False)','true_false','["True","False"]','True',2,21),
((SELECT id FROM tests WHERE course_id=44 AND is_final=1 LIMIT 1),'Renewable energy includes coal and oil. (True/False)','true_false','["True","False"]','False',2,22),
((SELECT id FROM tests WHERE course_id=44 AND is_final=1 LIMIT 1),'Inversion is used after negative adverbs like "Seldom". (True/False)','true_false','["True","False"]','True',2,23),
((SELECT id FROM tests WHERE course_id=44 AND is_final=1 LIMIT 1),'"Although" and "despite" are used in the same grammatical structure. (True/False)','true_false','["True","False"]','False',2,24),
((SELECT id FROM tests WHERE course_id=44 AND is_final=1 LIMIT 1),'The economy and education are often topics in debate essays. (True/False)','true_false','["True","False"]','True',2,25),
((SELECT id FROM tests WHERE course_id=44 AND is_final=1 LIMIT 1),'Complete: "The government should ___ in education." (invest/investment)','fill_blank',NULL,'invest',2,26),
((SELECT id FROM tests WHERE course_id=44 AND is_final=1 LIMIT 1),'Seldom ___ such a beautiful sunset. (have I seen / I have seen)','fill_blank',NULL,'have I seen',2,27),
((SELECT id FROM tests WHERE course_id=44 AND is_final=1 LIMIT 1),'Complete: "Many people ___ that education should be free." (argue/argument)','fill_blank',NULL,'argue',2,28),
((SELECT id FROM tests WHERE course_id=44 AND is_final=1 LIMIT 1),'The Industrial Revolution ___ in the 18th century. (began/ended)','fill_blank',NULL,'began',2,29),
((SELECT id FROM tests WHERE course_id=44 AND is_final=1 LIMIT 1),'Complete: "Although it was raining, they ___ to go out." (decided/refused)','fill_blank',NULL,'decided',2,30);

-- B2: English Upper 3
INSERT INTO `questions` (`test_id`,`question_text`,`question_type`,`options_json`,`correct_answer`,`points`,`sort_order`) VALUES
((SELECT id FROM tests WHERE course_id=45 AND is_final=1 LIMIT 1),'"Look forward to" is followed by a gerund. (True/False)','true_false','["True","False"]','True',2,21),
((SELECT id FROM tests WHERE course_id=45 AND is_final=1 LIMIT 1),'"Due to" and "because of" have different meanings. (True/False)','true_false','["True","False"]','False',2,22),
((SELECT id FROM tests WHERE course_id=45 AND is_final=1 LIMIT 1),'In formal emails, you should use contractions like "don\'t". (True/False)','true_false','["True","False"]','False',2,23),
((SELECT id FROM tests WHERE course_id=45 AND is_final=1 LIMIT 1),'"Review" means to examine something carefully. (True/False)','true_false','["True","False"]','True',2,24),
((SELECT id FROM tests WHERE course_id=45 AND is_final=1 LIMIT 1),'The word "whose" is a possessive relative pronoun. (True/False)','true_false','["True","False"]','True',2,25),
((SELECT id FROM tests WHERE course_id=45 AND is_final=1 LIMIT 1),'Complete: "I look forward to ___ from you." (hear/hearing)','fill_blank',NULL,'hearing',2,26),
((SELECT id FROM tests WHERE course_id=45 AND is_final=1 LIMIT 1),'Could you please ___ me the report by Friday? (send/sending)','fill_blank',NULL,'send',2,27),
((SELECT id FROM tests WHERE course_id=45 AND is_final=1 LIMIT 1),'Complete: "We need to ___ the contract before signing." (review/ignore)','fill_blank',NULL,'review',2,28),
((SELECT id FROM tests WHERE course_id=45 AND is_final=1 LIMIT 1),'She was promoted ___ her excellent performance. (due to/despite)','fill_blank',NULL,'due to',2,29),
((SELECT id FROM tests WHERE course_id=45 AND is_final=1 LIMIT 1),'Complete: "The candidate ___ answers impressed." (whose/who)','fill_blank',NULL,'whose',2,30);

-- C1: English Advanced 1
INSERT INTO `questions` (`test_id`,`question_text`,`question_type`,`options_json`,`correct_answer`,`points`,`sort_order`) VALUES
((SELECT id FROM tests WHERE course_id=46 AND is_final=1 LIMIT 1),'Academic writing should avoid subjective language. (True/False)','true_false','["True","False"]','True',2,21),
((SELECT id FROM tests WHERE course_id=46 AND is_final=1 LIMIT 1),'A hypothesis is a proven fact. (True/False)','true_false','["True","False"]','False',2,22),
((SELECT id FROM tests WHERE course_id=46 AND is_final=1 LIMIT 1),'The subjunctive mood is used after "It is imperative that...". (True/False)','true_false','["True","False"]','True',2,23),
((SELECT id FROM tests WHERE course_id=46 AND is_final=1 LIMIT 1),'"Exacerbate" means to make something better. (True/False)','true_false','["True","False"]','False',2,24),
((SELECT id FROM tests WHERE course_id=46 AND is_final=1 LIMIT 1),'"Were it not for" is an inverted conditional. (True/False)','true_false','["True","False"]','True',2,25),
((SELECT id FROM tests WHERE course_id=46 AND is_final=1 LIMIT 1),'Complete: "The research ___ that the hypothesis was correct." (demonstrated/refused)','fill_blank',NULL,'demonstrated',2,26),
((SELECT id FROM tests WHERE course_id=46 AND is_final=1 LIMIT 1),'It is imperative that the data ___ verified before publication. (be/is)','fill_blank',NULL,'be',2,27),
((SELECT id FROM tests WHERE course_id=46 AND is_final=1 LIMIT 1),'Complete: "The author argues that globalization has ___ inequality." (exacerbated/reduced)','fill_blank',NULL,'exacerbated',2,28),
((SELECT id FROM tests WHERE course_id=46 AND is_final=1 LIMIT 1),'Were it not for the grant, the research ___. (would have failed / will fail)','fill_blank',NULL,'would have failed',2,29),
((SELECT id FROM tests WHERE course_id=46 AND is_final=1 LIMIT 1),'Complete: "A ___ analysis of the data revealed patterns." (thorough/quick)','fill_blank',NULL,'thorough',2,30);

-- C1: English Advanced 2
INSERT INTO `questions` (`test_id`,`question_text`,`question_type`,`options_json`,`correct_answer`,`points`,`sort_order`) VALUES
((SELECT id FROM tests WHERE course_id=47 AND is_final=1 LIMIT 1),'Shakespeare wrote only comedies. (True/False)','true_false','["True","False"]','False',2,21),
((SELECT id FROM tests WHERE course_id=47 AND is_final=1 LIMIT 1),'Literature can explore the complexity of human existence. (True/False)','true_false','["True","False"]','True',2,22),
((SELECT id FROM tests WHERE course_id=47 AND is_final=1 LIMIT 1),'"Incisive" means dull and uninteresting. (True/False)','true_false','["True","False"]','False',2,23),
((SELECT id FROM tests WHERE course_id=47 AND is_final=1 LIMIT 1),'Philosophy and art often examine questions of morality. (True/False)','true_false','["True","False"]','True',2,24),
((SELECT id FROM tests WHERE course_id=47 AND is_final=1 LIMIT 1),'The word "relevant" means no longer important. (True/False)','true_false','["True","False"]','False',2,25),
((SELECT id FROM tests WHERE course_id=47 AND is_final=1 LIMIT 1),'Complete: "The novel explores the ___ of human existence." (complexity/simplicity)','fill_blank',NULL,'complexity',2,26),
((SELECT id FROM tests WHERE course_id=47 AND is_final=1 LIMIT 1),'Shakespeare\'s works remain ___ centuries later. (relevant/irrelevant)','fill_blank',NULL,'relevant',2,27),
((SELECT id FROM tests WHERE course_id=47 AND is_final=1 LIMIT 1),'Complete: "The artist\'s style is highly ___." (distinctive/ordinary)','fill_blank',NULL,'distinctive',2,28),
((SELECT id FROM tests WHERE course_id=47 AND is_final=1 LIMIT 1),'The philosopher argued that morality is not ___. (absolute/relative)','fill_blank',NULL,'absolute',2,29),
((SELECT id FROM tests WHERE course_id=47 AND is_final=1 LIMIT 1),'Complete: "His ___ critique sparked widespread debate." (incisive/dull)','fill_blank',NULL,'incisive',2,30);

-- C1: English Advanced 3
INSERT INTO `questions` (`test_id`,`question_text`,`question_type`,`options_json`,`correct_answer`,`points`,`sort_order`) VALUES
((SELECT id FROM tests WHERE course_id=48 AND is_final=1 LIMIT 1),'Mastery of a language includes understanding nuance. (True/False)','true_false','["True","False"]','True',2,21),
((SELECT id FROM tests WHERE course_id=48 AND is_final=1 LIMIT 1),'"Eloquent" means speaking unclearly. (True/False)','true_false','["True","False"]','False',2,22),
((SELECT id FROM tests WHERE course_id=48 AND is_final=1 LIMIT 1),'The word "differentiate" means to confuse two things. (True/False)','true_false','["True","False"]','False',2,23),
((SELECT id FROM tests WHERE course_id=48 AND is_final=1 LIMIT 1),'"Judicious" means showing good judgment. (True/False)','true_false','["True","False"]','True',2,24),
((SELECT id FROM tests WHERE course_id=48 AND is_final=1 LIMIT 1),'Idioms are literal expressions. (True/False)','true_false','["True","False"]','False',2,25),
((SELECT id FROM tests WHERE course_id=48 AND is_final=1 LIMIT 1),'Complete: "Her ___ of English is truly remarkable." (mastery/ignorance)','fill_blank',NULL,'mastery',2,26),
((SELECT id FROM tests WHERE course_id=48 AND is_final=1 LIMIT 1),'The subtle ___ in his speech conveyed more than words. (nuance/error)','fill_blank',NULL,'nuance',2,27),
((SELECT id FROM tests WHERE course_id=48 AND is_final=1 LIMIT 1),'Complete: "She spoke with such ___ that everyone was convinced." (eloquence/confusion)','fill_blank',NULL,'eloquence',2,28),
((SELECT id FROM tests WHERE course_id=48 AND is_final=1 LIMIT 1),'He could ___ between seemingly identical concepts. (differentiate/confuse)','fill_blank',NULL,'differentiate',2,29),
((SELECT id FROM tests WHERE course_id=48 AND is_final=1 LIMIT 1),'Complete: "The writer\'s ___ use of metaphors enriched the narrative." (judicious/careless)','fill_blank',NULL,'judicious',2,30);
