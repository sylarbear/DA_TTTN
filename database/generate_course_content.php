<?php
/**
 * GENERATE COURSE CONTENT — Sinh toàn bộ nội dung cho 15 khóa A1→C1
 *
 * Chạy: php database/generate_course_content.php
 * An toàn: dùng INSERT IGNORE, có thể chạy lại nhiều lần
 */

require __DIR__ . '/../app/config/database.php';
$db = getDB();

echo "=== GENERATE COURSE CONTENT ===\n\n";

// ═══════════════════════════════════════════════════════════════
// DATA DEFINITION — 15 courses × 4-5 chapters each
// ═══════════════════════════════════════════════════════════════

$courses = [
    // ── A1 LEVEL ──────────────────────────────────────────────
    34 => [ // A1-S1: English Starter 1
        'chapters' => [
            [
                'name' => 'Greetings & Introductions',
                'slug' => 'greetings-introductions-a1s1',
                'level' => 'beginner',
                'lessons' => [
                    ['title' => 'Hello & Goodbye', 'mins' => 15, 'content' => [
                        '<h3>Chào hỏi cơ bản</h3><p>Trong tiếng Anh, có nhiều cách để chào hỏi tùy vào thời điểm trong ngày và mức độ trang trọng.</p><p><strong>Hello / Hi</strong> — Xin chào (dùng mọi lúc)</p><p><strong>Good morning</strong> — Chào buổi sáng (trước 12h)</p><p><strong>Good afternoon</strong> — Chào buổi chiều (12h-18h)</p><p><strong>Good evening</strong> — Chào buổi tối (sau 18h)</p>',
                        '<h3>Chào tạm biệt</h3><p><strong>Goodbye / Bye</strong> — Tạm biệt</p><p><strong>See you later</strong> — Hẹn gặp lại</p><p><strong>See you tomorrow</strong> — Hẹn gặp ngày mai</p><p><strong>Good night</strong> — Chúc ngủ ngon</p>',
                        '<h3>Bài tập thực hành</h3><p>1. What do you say at 8:00 AM? → <strong>Good morning!</strong></p><p>2. What do you say before going to bed? → <strong>Good night!</strong></p><p>3. What do you say when leaving a friend? → <strong>Bye! See you later!</strong></p>',
                    ]],
                    ['title' => 'Introducing Yourself', 'mins' => 12, 'content' => [
                        '<h3>Giới thiệu bản thân</h3><p>Khi gặp người mới, bạn có thể giới thiệu bản thân bằng các mẫu câu đơn giản:</p><p><strong>Hi! My name is ___.</strong> — Xin chào! Tên tôi là ___.</p><p><strong>I am from ___.</strong> — Tôi đến từ ___.</p><p><strong>Nice to meet you.</strong> — Rất vui được gặp bạn.</p><p><strong>I am ___ years old.</strong> — Tôi ___ tuổi.</p>',
                        '<h3>Hội thoại mẫu</h3><div class="dialogue"><p><strong>A:</strong> Hi! My name is Tom. What is your name?</p><p><strong>B:</strong> Hello! I am Anna. Nice to meet you!</p><p><strong>A:</strong> Nice to meet you too! Where are you from?</p><p><strong>B:</strong> I am from Vietnam. And you?</p><p><strong>A:</strong> I am from England.</p></div>',
                    ]],
                    ['title' => 'Greetings Practice', 'mins' => 15, 'content' => [
                        '<h3>Luyện tập chào hỏi</h3><p>Hoàn thành các câu sau:</p><p>1. ___ morning! (Good)</p><p>2. Nice to ___ you. (meet)</p><p>3. See you ___! (later)</p><p>4. My ___ is John. (name)</p><p>5. I am ___ Vietnam. (from)</p>',
                        '<h3>Tình huống thực tế</h3><p><strong>Bạn gặp đồng nghiệp lúc 9h sáng:</strong></p><p>You: Good morning! How are you?</p><p>Colleague: I\'m fine, thank you! And you?</p><p>You: I\'m great, thanks!</p>',
                    ]],
                ],
                'vocab' => [
                    ['word' => 'hello', 'pron' => '/həˈloʊ/', 'meaning' => 'xin chào', 'example' => 'Hello, how are you?'],
                    ['word' => 'goodbye', 'pron' => '/ɡʊdˈbaɪ/', 'meaning' => 'tạm biệt', 'example' => 'Goodbye, see you tomorrow.'],
                    ['word' => 'morning', 'pron' => '/ˈmɔːrnɪŋ/', 'meaning' => 'buổi sáng', 'example' => 'Good morning, teacher.'],
                    ['word' => 'name', 'pron' => '/neɪm/', 'meaning' => 'tên', 'example' => 'My name is Sarah.'],
                    ['word' => 'meet', 'pron' => '/miːt/', 'meaning' => 'gặp gỡ', 'example' => 'Nice to meet you.'],
                    ['word' => 'friend', 'pron' => '/frend/', 'meaning' => 'bạn bè', 'example' => 'She is my friend.'],
                    ['word' => 'how', 'pron' => '/haʊ/', 'meaning' => 'như thế nào', 'example' => 'How are you?'],
                    ['word' => 'fine', 'pron' => '/faɪn/', 'meaning' => 'khỏe, tốt', 'example' => 'I am fine, thank you.'],
                    ['word' => 'thank you', 'pron' => '/θæŋk juː/', 'meaning' => 'cảm ơn', 'example' => 'Thank you very much.'],
                    ['word' => 'welcome', 'pron' => '/ˈwelkəm/', 'meaning' => 'chào đón', 'example' => 'You are welcome.'],
                ],
                'quizzes' => [
                    ['title' => 'Greetings Quiz', 'questions' => [
                        ['q' => 'What do you say in the morning?', 'opts' => ['Good night', 'Good morning', 'Goodbye', 'Hello'], 'ans' => 'Good morning'],
                        ['q' => '"Nice to meet you" means:', 'opts' => ['Tạm biệt', 'Cảm ơn', 'Rất vui được gặp bạn', 'Xin lỗi'], 'ans' => 'Rất vui được gặp bạn'],
                        ['q' => 'Which is a greeting?', 'opts' => ['Bye', 'Hello', 'See you', 'Good night'], 'ans' => 'Hello'],
                        ['q' => 'How do you respond to "How are you?"', 'opts' => ['Goodbye', 'My name is Tom', 'I\'m fine, thanks', 'See you'], 'ans' => 'I\'m fine, thanks'],
                        ['q' => '"See you later" means:', 'opts' => ['Chào buổi sáng', 'Hẹn gặp lại', 'Cảm ơn', 'Xin chào'], 'ans' => 'Hẹn gặp lại'],
                    ]],
                    ['title' => 'Introductions Quiz', 'questions' => [
                        ['q' => 'How do you introduce your name?', 'opts' => ['I am from...', 'My name is...', 'See you...', 'Good...'], 'ans' => 'My name is...'],
                        ['q' => '"I am from Vietnam" means:', 'opts' => ['Tôi là Việt Nam', 'Tôi đến từ Việt Nam', 'Tôi thích Việt Nam', 'Việt Nam đẹp'], 'ans' => 'Tôi đến từ Việt Nam'],
                        ['q' => 'What question asks for someone\'s name?', 'opts' => ['How are you?', 'Where are you?', 'What is your name?', 'Who is he?'], 'ans' => 'What is your name?'],
                        ['q' => 'Choose the correct sentence:', 'opts' => ['I are Tom', 'I am Tom', 'I is Tom', 'I be Tom'], 'ans' => 'I am Tom'],
                        ['q' => '"And you?" is used to:', 'opts' => ['Say goodbye', 'Ask the same question back', 'Say thank you', 'Introduce yourself'], 'ans' => 'Ask the same question back'],
                    ]],
                ],
            ],
            [
                'name' => 'Family & People',
                'slug' => 'family-people-a1s1',
                'level' => 'beginner',
                'lessons' => [
                    ['title' => 'Family Members', 'mins' => 15, 'content' => [
                        '<h3>Thành viên gia đình</h3><p><strong>Mother / Mom</strong> — Mẹ</p><p><strong>Father / Dad</strong> — Bố</p><p><strong>Sister</strong> — Chị/em gái</p><p><strong>Brother</strong> — Anh/em trai</p><p><strong>Grandmother</strong> — Bà</p><p><strong>Grandfather</strong> — Ông</p>',
                        '<h3>Giới thiệu gia đình</h3><p><strong>This is my mother.</strong> — Đây là mẹ tôi.</p><p><strong>I have one brother.</strong> — Tôi có một anh/em trai.</p><p><strong>My sister is 20 years old.</strong> — Chị tôi 20 tuổi.</p>',
                    ]],
                    ['title' => 'Describing People', 'mins' => 15, 'content' => [
                        '<h3>Mô tả người</h3><p><strong>Tall / Short</strong> — Cao / Thấp</p><p><strong>Young / Old</strong> — Trẻ / Già</p><p><strong>Beautiful / Handsome</strong> — Xinh đẹp / Đẹp trai</p><p><strong>Friendly</strong> — Thân thiện</p><p><strong>Kind</strong> — Tốt bụng</p>',
                        '<h3>Câu mẫu</h3><p>My father is tall and kind.</p><p>She is very friendly.</p><p>He has short hair and brown eyes.</p>',
                    ]],
                    ['title' => 'Talking About Family', 'mins' => 10, 'content' => [
                        '<h3>Hỏi về gia đình</h3><p><strong>How many people are in your family?</strong> — Gia đình bạn có mấy người?</p><p><strong>Do you have any siblings?</strong> — Bạn có anh chị em không?</p><p><strong>What does your father do?</strong> — Bố bạn làm nghề gì?</p>',
                        '<h3>Đoạn hội thoại</h3><p>A: Tell me about your family.</p><p>B: There are four people in my family: my father, my mother, my sister, and me.</p><p>A: What does your sister do?</p><p>B: She is a student.</p>',
                    ]],
                ],
                'vocab' => [
                    ['word' => 'mother', 'pron' => '/ˈmʌðər/', 'meaning' => 'mẹ', 'example' => 'My mother is a teacher.'],
                    ['word' => 'father', 'pron' => '/ˈfɑːðər/', 'meaning' => 'bố', 'example' => 'My father works in an office.'],
                    ['word' => 'sister', 'pron' => '/ˈsɪstər/', 'meaning' => 'chị/em gái', 'example' => 'I have one sister.'],
                    ['word' => 'brother', 'pron' => '/ˈbrʌðər/', 'meaning' => 'anh/em trai', 'example' => 'My brother is 15 years old.'],
                    ['word' => 'family', 'pron' => '/ˈfæməli/', 'meaning' => 'gia đình', 'example' => 'I love my family.'],
                    ['word' => 'tall', 'pron' => '/tɔːl/', 'meaning' => 'cao', 'example' => 'He is very tall.'],
                    ['word' => 'short', 'pron' => '/ʃɔːrt/', 'meaning' => 'thấp, ngắn', 'example' => 'She has short hair.'],
                    ['word' => 'young', 'pron' => '/jʌŋ/', 'meaning' => 'trẻ', 'example' => 'My sister is very young.'],
                    ['word' => 'friendly', 'pron' => '/ˈfrendli/', 'meaning' => 'thân thiện', 'example' => 'Our neighbor is very friendly.'],
                    ['word' => 'kind', 'pron' => '/kaɪnd/', 'meaning' => 'tốt bụng', 'example' => 'She is a kind person.'],
                ],
                'quizzes' => [
                    ['title' => 'Family Members Quiz', 'questions' => [
                        ['q' => 'What is "father" in Vietnamese?', 'opts' => ['Mẹ', 'Bố', 'Ông', 'Anh'], 'ans' => 'Bố'],
                        ['q' => 'Your mother\'s mother is your:', 'opts' => ['Sister', 'Aunt', 'Grandmother', 'Cousin'], 'ans' => 'Grandmother'],
                        ['q' => '"I have one brother" means:', 'opts' => ['Tôi có một chị', 'Tôi có một em gái', 'Tôi có một anh/em trai', 'Tôi có một người bạn'], 'ans' => 'Tôi có một anh/em trai'],
                        ['q' => 'Choose the correct word: "My ___ is a doctor."', 'opts' => ['family', 'mother', 'tall', 'friendly'], 'ans' => 'mother'],
                        ['q' => '"Sister" means:', 'opts' => ['Anh trai', 'Chị/em gái', 'Bố', 'Bạn'], 'ans' => 'Chị/em gái'],
                    ]],
                    ['title' => 'Describing People Quiz', 'questions' => [
                        ['q' => 'The opposite of "tall" is:', 'opts' => ['Big', 'Short', 'Small', 'Old'], 'ans' => 'Short'],
                        ['q' => '"She is friendly" means:', 'opts' => ['Cô ấy cao', 'Cô ấy già', 'Cô ấy thân thiện', 'Cô ấy đẹp'], 'ans' => 'Cô ấy thân thiện'],
                        ['q' => 'Which describes someone nice to others?', 'opts' => ['Tall', 'Short', 'Young', 'Kind'], 'ans' => 'Kind'],
                        ['q' => '"He has brown eyes" — "brown" is a:', 'opts' => ['Noun', 'Verb', 'Color', 'Number'], 'ans' => 'Color'],
                        ['q' => 'Choose the correct sentence:', 'opts' => ['She are friendly', 'She is friendly', 'She am friendly', 'She be friendly'], 'ans' => 'She is friendly'],
                    ]],
                ],
            ],
            [
                'name' => 'Numbers, Dates & Time',
                'slug' => 'numbers-dates-time-a1s1',
                'level' => 'beginner',
                'lessons' => [
                    ['title' => 'Numbers 1-100', 'mins' => 15, 'content' => [
                        '<h3>Số đếm cơ bản</h3><p>1 = one, 2 = two, 3 = three, 4 = four, 5 = five</p><p>6 = six, 7 = seven, 8 = eight, 9 = nine, 10 = ten</p><p>11 = eleven, 12 = twelve, 13 = thirteen</p><p>20 = twenty, 30 = thirty, 40 = forty, 50 = fifty</p><p>100 = one hundred</p>',
                        '<h3>Cách đọc số</h3><p>25 = twenty-five</p><p>47 = forty-seven</p><p>63 = sixty-three</p><p>89 = eighty-nine</p>',
                    ]],
                    ['title' => 'Days, Months & Dates', 'mins' => 15, 'content' => [
                        '<h3>Ngày trong tuần</h3><p>Monday, Tuesday, Wednesday, Thursday, Friday, Saturday, Sunday</p>',
                        '<h3>Tháng trong năm</h3><p>January, February, March, April, May, June, July, August, September, October, November, December</p>',
                        '<h3>Nói ngày tháng</h3><p>Today is Monday, July 3rd.</p><p>My birthday is on March 15th.</p>',
                    ]],
                    ['title' => 'Telling Time', 'mins' => 10, 'content' => [
                        '<h3>Cách nói giờ</h3><p><strong>What time is it?</strong> — Mấy giờ rồi?</p><p>It\'s 7:00 — seven o\'clock</p><p>It\'s 7:15 — seven fifteen / quarter past seven</p><p>It\'s 7:30 — seven thirty / half past seven</p><p>It\'s 7:45 — seven forty-five / quarter to eight</p>',
                    ]],
                ],
                'vocab' => [
                    ['word' => 'one', 'pron' => '/wʌn/', 'meaning' => 'số một', 'example' => 'I have one apple.'],
                    ['word' => 'Monday', 'pron' => '/ˈmʌndeɪ/', 'meaning' => 'thứ Hai', 'example' => 'We have English on Monday.'],
                    ['word' => 'today', 'pron' => '/təˈdeɪ/', 'meaning' => 'hôm nay', 'example' => 'Today is a beautiful day.'],
                    ['word' => 'tomorrow', 'pron' => '/təˈmɔːroʊ/', 'meaning' => 'ngày mai', 'example' => 'See you tomorrow.'],
                    ['word' => 'birthday', 'pron' => '/ˈbɜːrθdeɪ/', 'meaning' => 'sinh nhật', 'example' => 'Happy birthday!'],
                    ['word' => 'time', 'pron' => '/taɪm/', 'meaning' => 'thời gian', 'example' => 'What time is it?'],
                    ['word' => 'clock', 'pron' => '/klɒk/', 'meaning' => 'đồng hồ', 'example' => 'Look at the clock.'],
                    ['word' => 'early', 'pron' => '/ˈɜːrli/', 'meaning' => 'sớm', 'example' => 'I wake up early.'],
                    ['word' => 'late', 'pron' => '/leɪt/', 'meaning' => 'muộn', 'example' => 'Don\'t be late!'],
                    ['word' => 'date', 'pron' => '/deɪt/', 'meaning' => 'ngày tháng', 'example' => 'What is the date today?'],
                ],
                'quizzes' => [
                    ['title' => 'Numbers Quiz', 'questions' => [
                        ['q' => 'How do you say "25" in English?', 'opts' => ['Twenty-five', 'Twenty', 'Fifty-two', 'Two-five'], 'ans' => 'Twenty-five'],
                        ['q' => 'What number is "eleven"?', 'opts' => ['10', '11', '12', '13'], 'ans' => '11'],
                        ['q' => '"Forty-seven" is:', 'opts' => ['47', '74', '40', '17'], 'ans' => '47'],
                        ['q' => '100 in English is:', 'opts' => ['One thousand', 'One hundred', 'Ten', 'One million'], 'ans' => 'One hundred'],
                        ['q' => 'What is 3 + 9?', 'opts' => ['Eleven', 'Twelve', 'Thirteen', 'Ten'], 'ans' => 'Twelve'],
                    ]],
                    ['title' => 'Time & Dates Quiz', 'questions' => [
                        ['q' => 'Which day comes after Monday?', 'opts' => ['Sunday', 'Tuesday', 'Thursday', 'Saturday'], 'ans' => 'Tuesday'],
                        ['q' => '"Half past seven" is:', 'opts' => ['7:00', '7:15', '7:30', '7:45'], 'ans' => '7:30'],
                        ['q' => 'Which month is the 5th month?', 'opts' => ['April', 'May', 'June', 'July'], 'ans' => 'May'],
                        ['q' => 'What is "hôm nay" in English?', 'opts' => ['Tomorrow', 'Yesterday', 'Today', 'Tonight'], 'ans' => 'Today'],
                        ['q' => '"Quarter to eight" is:', 'opts' => ['7:45', '8:00', '8:15', '7:15'], 'ans' => '7:45'],
                    ]],
                ],
            ],
            [
                'name' => 'Colors & Descriptions',
                'slug' => 'colors-descriptions-a1s1',
                'level' => 'beginner',
                'lessons' => [
                    ['title' => 'Basic Colors', 'mins' => 10, 'content' => [
                        '<h3>Màu sắc cơ bản</h3><p>Red, Blue, Green, Yellow, Black, White, Orange, Purple, Pink, Brown, Gray</p>',
                        '<h3>Câu ví dụ</h3><p>The sky is blue.</p><p>I like red roses.</p><p>My car is white.</p>',
                    ]],
                    ['title' => 'Describing Things', 'mins' => 15, 'content' => [
                        '<h3>Tính từ mô tả</h3><p><strong>Big / Small</strong> — To / Nhỏ</p><p><strong>New / Old</strong> — Mới / Cũ</p><p><strong>Good / Bad</strong> — Tốt / Xấu</p><p><strong>Hot / Cold</strong> — Nóng / Lạnh</p>',
                        '<h3>Câu mẫu</h3><p>This is a big house.</p><p>I have a new phone.</p><p>The coffee is hot.</p>',
                    ]],
                    ['title' => 'Making Sentences', 'mins' => 15, 'content' => [
                        '<h3>Câu với tính từ</h3><p>Trong tiếng Anh, tính từ đứng trước danh từ hoặc sau "to be":</p><p><strong>It is a red car.</strong> — Đó là chiếc xe màu đỏ.</p><p><strong>The car is red.</strong> — Chiếc xe màu đỏ.</p><p><strong>I have a small dog.</strong> — Tôi có một con chó nhỏ.</p>',
                    ]],
                ],
                'vocab' => [
                    ['word' => 'red', 'pron' => '/red/', 'meaning' => 'màu đỏ', 'example' => 'The apple is red.'],
                    ['word' => 'blue', 'pron' => '/bluː/', 'meaning' => 'màu xanh dương', 'example' => 'The ocean is blue.'],
                    ['word' => 'green', 'pron' => '/ɡriːn/', 'meaning' => 'màu xanh lá', 'example' => 'Green trees are beautiful.'],
                    ['word' => 'big', 'pron' => '/bɪɡ/', 'meaning' => 'to, lớn', 'example' => 'Elephants are big.'],
                    ['word' => 'small', 'pron' => '/smɔːl/', 'meaning' => 'nhỏ', 'example' => 'Ants are small.'],
                    ['word' => 'new', 'pron' => '/njuː/', 'meaning' => 'mới', 'example' => 'I bought a new book.'],
                    ['word' => 'good', 'pron' => '/ɡʊd/', 'meaning' => 'tốt', 'example' => 'This is a good movie.'],
                    ['word' => 'hot', 'pron' => '/hɒt/', 'meaning' => 'nóng', 'example' => 'The soup is hot.'],
                    ['word' => 'cold', 'pron' => '/koʊld/', 'meaning' => 'lạnh', 'example' => 'Ice cream is cold.'],
                    ['word' => 'beautiful', 'pron' => '/ˈbjuːtɪfəl/', 'meaning' => 'đẹp', 'example' => 'What a beautiful day!'],
                ],
                'quizzes' => [
                    ['title' => 'Colors Quiz', 'questions' => [
                        ['q' => 'What color is the sky usually?', 'opts' => ['Red', 'Blue', 'Green', 'Yellow'], 'ans' => 'Blue'],
                        ['q' => 'The opposite of "white" is:', 'opts' => ['Red', 'Blue', 'Green', 'Black'], 'ans' => 'Black'],
                        ['q' => '"Green" in Vietnamese is:', 'opts' => ['Xanh dương', 'Xanh lá', 'Đỏ', 'Vàng'], 'ans' => 'Xanh lá'],
                        ['q' => 'Bananas are usually:', 'opts' => ['Red', 'Blue', 'Yellow', 'Purple'], 'ans' => 'Yellow'],
                        ['q' => 'Which is a color?', 'opts' => ['Happy', 'Purple', 'Fast', 'Small'], 'ans' => 'Purple'],
                    ]],
                    ['title' => 'Descriptions Quiz', 'questions' => [
                        ['q' => 'The opposite of "big" is:', 'opts' => ['Tall', 'Small', 'Old', 'New'], 'ans' => 'Small'],
                        ['q' => '"The soup is hot" means:', 'opts' => ['Súp lạnh', 'Súp nóng', 'Súp mới', 'Súp ngon'], 'ans' => 'Súp nóng'],
                        ['q' => 'Choose the correct sentence:', 'opts' => ['It is a car red', 'It is a red car', 'It a red car is', 'It car is red'], 'ans' => 'It is a red car'],
                        ['q' => '"Beautiful" means:', 'opts' => ['Cao', 'Nhanh', 'Đẹp', 'Nhỏ'], 'ans' => 'Đẹp'],
                        ['q' => 'The opposite of "new" is:', 'opts' => ['Big', 'Small', 'Good', 'Old'], 'ans' => 'Old'],
                    ]],
                ],
            ],
            [
                'name' => 'Review & Practice: A1 Starter 1',
                'slug' => 'review-practice-a1s1',
                'level' => 'beginner',
                'lessons' => [
                    ['title' => 'Review: Greetings & Family', 'mins' => 15, 'content' => [
                        '<h3>Ôn tập Chương 1-2</h3><p>Hãy cùng ôn lại những gì đã học về chào hỏi và gia đình.</p>',
                        '<h3>Bài tập tổng hợp</h3><p>Hoàn thành các câu: 1. Good ___, how are you? 2. My ___ is John. 3. This is my ___. She is a teacher. 4. Nice to ___ you!</p>',
                    ]],
                    ['title' => 'Review: Numbers & Descriptions', 'mins' => 15, 'content' => [
                        '<h3>Ôn tập Chương 3-4</h3><p>Ôn lại số đếm, ngày tháng, màu sắc và tính từ mô tả.</p>',
                    ]],
                    ['title' => 'Final Practice: All Topics', 'mins' => 15, 'content' => [
                        '<h3>Ôn tập tổng hợp A1 Starter 1</h3><p>Chúc mừng bạn đã hoàn thành English Starter 1! Hãy làm bài Final Exam để kiểm tra kiến thức.</p>',
                    ]],
                ],
                'vocab' => [
                    ['word' => 'review', 'pron' => '/rɪˈvjuː/', 'meaning' => 'ôn tập', 'example' => 'Let\'s review the lessons.'],
                    ['word' => 'practice', 'pron' => '/ˈpræktɪs/', 'meaning' => 'luyện tập', 'example' => 'Practice makes perfect.'],
                    ['word' => 'learn', 'pron' => '/lɜːrn/', 'meaning' => 'học', 'example' => 'I learn English every day.'],
                    ['word' => 'speak', 'pron' => '/spiːk/', 'meaning' => 'nói', 'example' => 'Do you speak English?'],
                    ['word' => 'listen', 'pron' => '/ˈlɪsən/', 'meaning' => 'lắng nghe', 'example' => 'Listen to the teacher.'],
                    ['word' => 'write', 'pron' => '/raɪt/', 'meaning' => 'viết', 'example' => 'Please write your name.'],
                    ['word' => 'read', 'pron' => '/riːd/', 'meaning' => 'đọc', 'example' => 'I like to read books.'],
                    ['word' => 'question', 'pron' => '/ˈkwestʃən/', 'meaning' => 'câu hỏi', 'example' => 'Do you have a question?'],
                    ['word' => 'answer', 'pron' => '/ˈænsər/', 'meaning' => 'câu trả lời', 'example' => 'Please answer the question.'],
                    ['word' => 'test', 'pron' => '/test/', 'meaning' => 'bài kiểm tra', 'example' => 'We have a test tomorrow.'],
                ],
                'quizzes' => [
                    ['title' => 'Review Quiz 1: Greetings & Family', 'questions' => [
                        ['q' => '"Good morning" is used:', 'opts' => ['At night', 'In the morning', 'At lunch', 'Any time'], 'ans' => 'In the morning'],
                        ['q' => 'Your father\'s wife is your:', 'opts' => ['Sister', 'Aunt', 'Mother', 'Cousin'], 'ans' => 'Mother'],
                        ['q' => '"How are you?" — Best response:', 'opts' => ['Goodbye', 'My name is Tom', 'I\'m fine, thanks', 'See you'], 'ans' => 'I\'m fine, thanks'],
                        ['q' => '"This is my brother" means:', 'opts' => ['Đây là bố tôi', 'Đây là anh/em trai tôi', 'Đây là bạn tôi', 'Đây là mẹ tôi'], 'ans' => 'Đây là anh/em trai tôi'],
                        ['q' => 'Which is NOT a greeting?', 'opts' => ['Hello', 'Hi', 'Goodbye', 'Good morning'], 'ans' => 'Goodbye'],
                    ]],
                    ['title' => 'Review Quiz 2: Numbers & Colors', 'questions' => [
                        ['q' => 'What number is "thirty-three"?', 'opts' => ['13', '30', '33', '43'], 'ans' => '33'],
                        ['q' => 'What day comes before Sunday?', 'opts' => ['Monday', 'Friday', 'Saturday', 'Wednesday'], 'ans' => 'Saturday'],
                        ['q' => 'The color of grass is:', 'opts' => ['Blue', 'Green', 'Red', 'Yellow'], 'ans' => 'Green'],
                        ['q' => '"It is a small house" — Which word is the adjective?', 'opts' => ['It', 'is', 'small', 'house'], 'ans' => 'small'],
                        ['q' => '7:45 is:', 'opts' => ['Quarter past seven', 'Quarter to eight', 'Half past seven', 'Seven o\'clock'], 'ans' => 'Quarter to eight'],
                    ]],
                ],
            ],
        ],
    ],
];

// Helper function to generate content for courses 35-48
function generateLessons($topic) {
    $name = $topic['name'];
    return [
        ['title' => "$name — Part 1: Vocabulary", 'mins' => 15, 'content' => [
            "<h3>Giới thiệu: $name</h3><p>Chào mừng đến với bài học về <strong>$name</strong>. Trong bài này, chúng ta sẽ học các từ vựng và mẫu câu quan trọng liên quan đến chủ đề này.</p>",
            "<h3>Từ vựng chính</h3><p>Hãy cùng khám phá những từ vựng cần thiết cho chủ đề <strong>$name</strong>. Mỗi từ đều có ví dụ cụ thể để bạn dễ dàng áp dụng trong giao tiếp hàng ngày.</p>",
        ]],
        ['title' => "$name — Part 2: Grammar & Usage", 'mins' => 15, 'content' => [
            "<h3>Ngữ pháp trong ngữ cảnh</h3><p>Bài này sẽ hướng dẫn bạn cách sử dụng đúng ngữ pháp khi nói về <strong>$name</strong>. Chúng ta sẽ thực hành với các cấu trúc câu thông dụng.</p>",
            "<h3>Ví dụ thực tế</h3><p>Dưới đây là một số tình huống thực tế sử dụng từ vựng và ngữ pháp đã học. Hãy đọc kỹ và thực hành theo.</p>",
        ]],
        ['title' => "$name — Part 3: Practice & Speaking", 'mins' => 15, 'content' => [
            "<h3>Bài tập thực hành</h3><p>Hoàn thành các bài tập sau để củng cố kiến thức về <strong>$name</strong>.</p>",
            "<h3>Luyện nói</h3><p>Thực hành nói theo các mẫu câu. Hãy cố gắng tạo câu của riêng bạn dựa trên chủ đề <strong>$name</strong>.</p>",
        ]],
    ];
}

function generateVocab($topic) {
    $name = strtolower($topic['name']);
    $words = [
        ['word' => "$name basics", 'pron' => '/.../', 'meaning' => "kiến thức cơ bản về $name", 'example' => "Learning $name basics is important."],
        ['word' => "understand", 'pron' => '/ˌʌndərˈstænd/', 'meaning' => 'hiểu', 'example' => "I understand the topic well."],
        ['word' => "example", 'pron' => '/ɪɡˈzæmpəl/', 'meaning' => 'ví dụ', 'example' => "Can you give me an example?"],
        ['word' => "important", 'pron' => '/ɪmˈpɔːrtənt/', 'meaning' => 'quan trọng', 'example' => "This is an important topic."],
        ['word' => "different", 'pron' => '/ˈdɪfərənt/', 'meaning' => 'khác nhau', 'example' => "There are different ways to learn."],
        ['word' => "remember", 'pron' => '/rɪˈmembər/', 'meaning' => 'nhớ', 'example' => "Try to remember these words."],
        ['word' => "express", 'pron' => '/ɪkˈspres/', 'meaning' => 'diễn đạt', 'example' => "Express your ideas clearly."],
        ['word' => "common", 'pron' => '/ˈkɒmən/', 'meaning' => 'phổ biến', 'example' => "This is a common phrase."],
        ['word' => "situation", 'pron' => '/ˌsɪtʃuˈeɪʃən/', 'meaning' => 'tình huống', 'example' => "Use it in this situation."],
        ['word' => "improve", 'pron' => '/ɪmˈpruːv/', 'meaning' => 'cải thiện', 'example' => "Practice to improve your skills."],
    ];
    return $words;
}

function generateQuizQuestions($topicName) {
    return [
        ['title' => "$topicName — Quiz 1", 'questions' => [
            ['q' => "Which topic does this chapter cover?", 'opts' => ["Food", "$topicName", "Travel", "Family"], 'ans' => "$topicName"],
            ['q' => 'Learning new vocabulary helps you:', 'opts' => ['Forget words', 'Improve communication', 'Speak less', 'Write slower'], 'ans' => 'Improve communication'],
            ['q' => 'What is the best way to remember new words?', 'opts' => ['Never use them', 'Practice regularly', 'Ignore them', 'Only read'], 'ans' => 'Practice regularly'],
            ['q' => 'How many parts are in this chapter?', 'opts' => ['1', '2', '3', '4'], 'ans' => '3'],
            ['q' => 'After this chapter, you should be able to:', 'opts' => ['Forget everything', 'Discuss this topic', 'Only listen', 'Stop learning'], 'ans' => 'Discuss this topic'],
        ]],
        ['title' => "$topicName — Quiz 2", 'questions' => [
            ['q' => 'Which skill is most practiced in this chapter?', 'opts' => ['Math', 'Cooking', 'English communication', 'Driving'], 'ans' => 'English communication'],
            ['q' => 'To "express yourself" means:', 'opts' => ['Stay silent', 'Share your thoughts', 'Run fast', 'Eat food'], 'ans' => 'Share your thoughts'],
            ['q' => 'What helps you improve the most?', 'opts' => ['Watching without thinking', 'Active practice', 'Sleeping', 'Ignoring mistakes'], 'ans' => 'Active practice'],
            ['q' => 'The best approach to learning is:', 'opts' => ['Give up easily', 'Practice every day', 'Never review', 'Skip lessons'], 'ans' => 'Practice every day'],
            ['q' => 'After completing this chapter, you should feel:', 'opts' => ['Confused', 'More confident', 'The same', 'Worse'], 'ans' => 'More confident'],
        ]],
    ];
}

// ═══════════════════════════════════════════════════════════════
// DEFINE REMAINING COURSES
// ═══════════════════════════════════════════════════════════════

// A1-S2: English Starter 2
$courses[35] = ['chapters' => []];
foreach ([
    ['name' => 'My Daily Routine', 'slug' => 'daily-routine-a1s2'],
    ['name' => 'Clothes & Shopping', 'slug' => 'clothes-shopping-a1s2'],
    ['name' => 'Weather & Seasons', 'slug' => 'weather-seasons-a1s2'],
    ['name' => 'Hobbies & Activities', 'slug' => 'hobbies-activities-a1s2'],
    ['name' => 'Review & Conversation', 'slug' => 'review-conversation-a1s2'],
] as $t) {
    $courses[35]['chapters'][] = [
        'name' => $t['name'], 'slug' => $t['slug'], 'level' => 'beginner',
        'lessons' => generateLessons($t),
        'vocab' => generateVocab($t),
        'quizzes' => generateQuizQuestions($t['name']),
    ];
}

// A1-S3: English Starter 3 (giữ topic #3 "Food & Cooking", bổ sung 4 chương)
$courses[36] = ['chapters' => []];
foreach ([
    ['name' => 'At the Restaurant', 'slug' => 'restaurant-a1s3'],
    ['name' => 'Shopping & Money', 'slug' => 'shopping-money-a1s3'],
    ['name' => 'Transportation', 'slug' => 'transportation-a1s3'],
    ['name' => 'Everyday Conversations', 'slug' => 'everyday-conversations-a1s3'],
] as $t) {
    $courses[36]['chapters'][] = [
        'name' => $t['name'], 'slug' => $t['slug'], 'level' => 'beginner',
        'lessons' => generateLessons($t),
        'vocab' => generateVocab($t),
        'quizzes' => generateQuizQuestions($t['name']),
    ];
}

// A2 courses (37-39) — already have 1 topic each, add 4 more
$extraA2chapters = [
    37 => [['name' => 'Asking for Directions', 'slug' => 'directions-a2s1'], ['name' => 'At the Hotel', 'slug' => 'hotel-a2s1'], ['name' => 'Sightseeing & Culture', 'slug' => 'sightseeing-a2s1'], ['name' => 'Travel Stories', 'slug' => 'travel-stories-a2s1']],
    38 => [['name' => 'Music & Entertainment', 'slug' => 'music-entertainment-a2s2'], ['name' => 'Sports & Games', 'slug' => 'sports-games-a2s2'], ['name' => 'Weekend Activities', 'slug' => 'weekend-a2s2'], ['name' => 'Making Plans', 'slug' => 'making-plans-a2s2']],
    39 => [['name' => 'Jobs & Careers', 'slug' => 'jobs-careers-a2s3'], ['name' => 'Office English', 'slug' => 'office-english-a2s3'], ['name' => 'Emails & Messages', 'slug' => 'emails-messages-a2s3'], ['name' => 'Phone Conversations', 'slug' => 'phone-conversations-a2s3']],
];
foreach ($extraA2chapters as $cid => $chapters) {
    if (!isset($courses[$cid])) $courses[$cid] = ['chapters' => []];
    foreach ($chapters as $t) {
        $courses[$cid]['chapters'][] = [
            'name' => $t['name'], 'slug' => $t['slug'], 'level' => 'intermediate',
            'lessons' => generateLessons($t),
            'vocab' => generateVocab($t),
            'quizzes' => generateQuizQuestions($t['name']),
        ];
    }
}

// B1 courses (40-42)
$b1Chapters = [
    40 => [['name' => 'Relationships & Family', 'slug' => 'relationships-b1s1'], ['name' => 'Health & Wellbeing', 'slug' => 'health-wellbeing-b1s1'], ['name' => 'Environment & Nature', 'slug' => 'environment-b1s1'], ['name' => 'Giving Opinions', 'slug' => 'opinions-b1s1'], ['name' => 'Discussions & Debates', 'slug' => 'discussions-b1s1']],
    41 => [['name' => 'Social Media & Internet', 'slug' => 'social-media-b1s2'], ['name' => 'News & Journalism', 'slug' => 'news-b1s2'], ['name' => 'Cultural Differences', 'slug' => 'cultural-diff-b1s2'], ['name' => 'Education Systems', 'slug' => 'education-systems-b1s2'], ['name' => 'Critical Thinking', 'slug' => 'critical-thinking-b1s2']],
    42 => [['name' => 'Business Basics', 'slug' => 'business-basics-b1s3'], ['name' => 'Presentations', 'slug' => 'presentations-b1s3'], ['name' => 'Writing Reports', 'slug' => 'writing-reports-b1s3'], ['name' => 'Meetings & Negotiations', 'slug' => 'meetings-b1s3']],
];
foreach ($b1Chapters as $cid => $chapters) {
    if (!isset($courses[$cid])) $courses[$cid] = ['chapters' => []];
    foreach ($chapters as $t) {
        $courses[$cid]['chapters'][] = [
            'name' => $t['name'], 'slug' => $t['slug'], 'level' => 'intermediate',
            'lessons' => generateLessons($t),
            'vocab' => generateVocab($t),
            'quizzes' => generateQuizQuestions($t['name']),
        ];
    }
}

// B2 courses (43-45)
$b2Chapters = [
    43 => [['name' => 'Complex Sentences', 'slug' => 'complex-sentences-b2s1'], ['name' => 'Passive & Conditionals', 'slug' => 'passive-conditionals-b2s1'], ['name' => 'Essay Writing', 'slug' => 'essay-writing-b2s1'], ['name' => 'Formal vs Informal', 'slug' => 'formal-informal-b2s1'], ['name' => 'Advanced Reading', 'slug' => 'advanced-reading-b2s1']],
    44 => [['name' => 'Current Affairs', 'slug' => 'current-affairs-b2s2'], ['name' => 'History & Politics', 'slug' => 'history-politics-b2s2'], ['name' => 'Economics Basics', 'slug' => 'economics-b2s2'], ['name' => 'Debate Skills', 'slug' => 'debate-skills-b2s2'], ['name' => 'Argumentative Writing', 'slug' => 'argumentative-writing-b2s2']],
    45 => [['name' => 'Job Interviews', 'slug' => 'job-interviews-b2s3'], ['name' => 'Professional Emails', 'slug' => 'professional-emails-b2s3'], ['name' => 'Negotiation Skills', 'slug' => 'negotiation-b2s3'], ['name' => 'Advanced Presentations', 'slug' => 'advanced-presentations-b2s3'], ['name' => 'Leadership Communication', 'slug' => 'leadership-b2s3']],
];
foreach ($b2Chapters as $cid => $chapters) {
    if (!isset($courses[$cid])) $courses[$cid] = ['chapters' => []];
    foreach ($chapters as $t) {
        $courses[$cid]['chapters'][] = [
            'name' => $t['name'], 'slug' => $t['slug'], 'level' => 'advanced',
            'lessons' => generateLessons($t),
            'vocab' => generateVocab($t),
            'quizzes' => generateQuizQuestions($t['name']),
        ];
    }
}

// C1 courses (46-48)
$c1Chapters = [
    46 => [['name' => 'Research Methods', 'slug' => 'research-methods-c1s1'], ['name' => 'Academic Vocabulary', 'slug' => 'academic-vocab-c1s1'], ['name' => 'Citation & Referencing', 'slug' => 'citation-c1s1'], ['name' => 'Literature Review', 'slug' => 'literature-review-c1s1'], ['name' => 'Academic Paper Writing', 'slug' => 'academic-paper-c1s1']],
    47 => [['name' => 'Literary Analysis', 'slug' => 'literary-analysis-c1s2'], ['name' => 'Art & Aesthetics', 'slug' => 'art-aesthetics-c1s2'], ['name' => 'Philosophy Basics', 'slug' => 'philosophy-c1s2'], ['name' => 'Creative Writing', 'slug' => 'creative-writing-c1s2'], ['name' => 'Critical Reviews', 'slug' => 'critical-reviews-c1s2']],
    48 => [['name' => 'Idioms & Expressions', 'slug' => 'idioms-c1s3'], ['name' => 'Nuance & Subtlety', 'slug' => 'nuance-c1s3'], ['name' => 'Academic Debates', 'slug' => 'academic-debates-c1s3'], ['name' => 'Professional Publishing', 'slug' => 'publishing-c1s3'], ['name' => 'Full Mastery Project', 'slug' => 'mastery-project-c1s3']],
];
foreach ($c1Chapters as $cid => $chapters) {
    if (!isset($courses[$cid])) $courses[$cid] = ['chapters' => []];
    foreach ($chapters as $t) {
        $courses[$cid]['chapters'][] = [
            'name' => $t['name'], 'slug' => $t['slug'], 'level' => 'advanced',
            'lessons' => generateLessons($t),
            'vocab' => generateVocab($t),
            'quizzes' => generateQuizQuestions($t['name']),
        ];
    }
}

// ═══════════════════════════════════════════════════════════════
// EXECUTION: Insert into database
// ═══════════════════════════════════════════════════════════════

$totalTopics = $totalLessons = $totalContents = $totalVocab = $totalTests = $totalQuestions = 0;

foreach ($courses as $courseId => $courseData) {
    $courseTitle = $db->query("SELECT title FROM courses WHERE id = $courseId")->fetch()['title'] ?? "Course #$courseId";
    echo "Course #$courseId: $courseTitle\n";

    foreach ($courseData['chapters'] as $chIdx => $chapter) {
        $chapterName = $chapter['name'];
        $chapterSlug = $chapter['slug'];
        $chapterLevel = $chapter['level'] ?? 'beginner';
        $sortOrder = $chIdx + 1;

        // Check if topic already exists (by slug or name + course_id)
        $existing = $db->prepare('SELECT id FROM topics WHERE (slug = :slug OR (name = :name AND course_id = :cid)) LIMIT 1');
        $existing->execute(['slug' => $chapterSlug, 'name' => $chapterName, 'cid' => $courseId]);
        $topicRow = $existing->fetch();

        if ($topicRow) {
            $topicId = $topicRow['id'];
            echo "  [SKIP] Topic #$topicId: $chapterName (exists)\n";
        } else {
            $db->prepare('INSERT INTO topics (name, slug, level, course_id, sort_order, is_active) VALUES (:n, :s, :l, :cid, :so, 1)')
               ->execute(['n' => $chapterName, 's' => $chapterSlug, 'l' => $chapterLevel, 'cid' => $courseId, 'so' => $sortOrder]);
            $topicId = $db->lastInsertId();
            $totalTopics++;
            echo "  [+] Topic #$topicId: $chapterName\n";
        }

        // ── Lessons ──
        $lSort = 1;
        foreach ($chapter['lessons'] as $lesson) {
            $lTitle = $lesson['title'];
            $existing = $db->prepare('SELECT id FROM lessons WHERE title = :t AND topic_id = :tid LIMIT 1');
            $existing->execute(['t' => $lTitle, 'tid' => $topicId]);
            $lessonRow = $existing->fetch();

            if ($lessonRow) {
                $lessonId = $lessonRow['id'];
            } else {
                $db->prepare('INSERT INTO lessons (topic_id, title, sort_order, estimated_minutes, is_active) VALUES (:tid, :t, :so, :mins, 1)')
                   ->execute(['tid' => $topicId, 't' => $lTitle, 'so' => $lSort, 'mins' => $lesson['mins'] ?? 15]);
                $lessonId = $db->lastInsertId();
                $totalLessons++;
                echo "    [+] Lesson #$lessonId: $lTitle\n";

                // ── Lesson Contents ──
                $cSort = 1;
                foreach (($lesson['content'] ?? []) as $contentHTML) {
                    $db->prepare('INSERT INTO lesson_contents (lesson_id, content_type, content, sort_order) VALUES (:lid, :ct, :c, :so)')
                       ->execute(['lid' => $lessonId, 'ct' => 'text', 'c' => $contentHTML, 'so' => $cSort]);
                    $totalContents++;
                    $cSort++;
                }
            }
            $lSort++;
        }

        // ── Vocabulary ──
        foreach ($chapter['vocab'] as $vocab) {
            $existing = $db->prepare('SELECT id FROM vocabularies WHERE word = :w AND topic_id = :tid LIMIT 1');
            $existing->execute(['w' => $vocab['word'], 'tid' => $topicId]);
            if (!$existing->fetch()) {
                $db->prepare('INSERT INTO vocabularies (topic_id, word, pronunciation, meaning_vi, example_sentence) VALUES (:tid, :w, :p, :m, :e)')
                   ->execute(['tid' => $topicId, 'w' => $vocab['word'], 'p' => $vocab['pron'], 'm' => $vocab['meaning'], 'e' => $vocab['example']]);
                $totalVocab++;
            }
        }

        // ── Quizzes ──
        foreach ($chapter['quizzes'] as $quiz) {
            $qTitle = $quiz['title'];
            $existing = $db->prepare('SELECT id FROM tests WHERE title = :t AND topic_id = :tid AND is_final = 0 LIMIT 1');
            $existing->execute(['t' => $qTitle, 'tid' => $topicId]);
            $testRow = $existing->fetch();

            if ($testRow) {
                $testId = $testRow['id'];
            } else {
                $db->prepare('INSERT INTO tests (topic_id, title, test_type, duration_minutes, pass_score, is_final, is_active) VALUES (:tid, :t, :tt, :d, :ps, 0, 1)')
                   ->execute(['tid' => $topicId, 't' => $qTitle, 'tt' => 'quiz', 'd' => 10, 'ps' => 60]);
                $testId = $db->lastInsertId();
                $totalTests++;
                echo "    [+] Test #$testId: $qTitle\n";

                // ── Questions ──
                $qSort = 1;
                foreach ($quiz['questions'] as $question) {
                    $opts = $question['opts'];
                    $db->prepare('INSERT INTO questions (test_id, question_text, question_type, options_json, correct_answer, points, sort_order) VALUES (:tid, :qt, :type, :opts, :ans, :pts, :so)')
                       ->execute([
                           'tid' => $testId, 'qt' => $question['q'], 'type' => 'multiple_choice',
                           'opts' => json_encode($opts), 'ans' => $question['ans'], 'pts' => 1, 'so' => $qSort,
                       ]);
                    $totalQuestions++;
                    $qSort++;
                }
            }
        }
    }
    echo "\n";
}

// ═══════════════════════════════════════════════════════════════
// FIX: Final exam placement — detach from topic #5, assign correctly
// ═══════════════════════════════════════════════════════════════
echo "=== Fixing Final Exams ===\n";
$finalExams = [
    49 => 34, 50 => 35, 51 => 36, // A1
    52 => 37, 53 => 38, 54 => 39, // A2
    55 => 40, 56 => 41, 57 => 42, // B1
    58 => 43, 59 => 44, 60 => 45, // B2
    61 => 46, 62 => 47, 63 => 48, // C1
];
foreach ($finalExams as $finalTestId => $courseId) {
    // Find a valid topic for this course
    $stmt = $db->prepare('SELECT id FROM topics WHERE course_id = :cid AND is_active = 1 ORDER BY sort_order LIMIT 1');
    $stmt->execute(['cid' => $courseId]);
    $topic = $stmt->fetch();

    if ($topic) {
        $db->prepare('UPDATE tests SET topic_id = :tid WHERE id = :fid AND is_final = 1')
           ->execute(['tid' => $topic['id'], 'fid' => $finalTestId]);
        echo "  Fixed: Final Exam #$finalTestId → topic #{$topic['id']} (course #$courseId)\n";
    } else {
        echo "  WARNING: Final Exam #$finalTestId — course #$courseId has no topics yet!\n";
    }
}

// Also clean up: any final exam still on topic #5 that doesn't belong
$db->exec("UPDATE tests SET topic_id = NULL WHERE topic_id = 5 AND is_final = 1 AND id NOT IN (57)");

echo "\n=== DONE ===\n";
echo "Topics created: $totalTopics\n";
echo "Lessons created: $totalLessons\n";
echo "Lesson contents created: $totalContents\n";
echo "Vocabulary entries created: $totalVocab\n";
echo "Tests created: $totalTests\n";
echo "Questions created: $totalQuestions\n";
