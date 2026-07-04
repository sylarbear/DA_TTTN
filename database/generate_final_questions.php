<?php
/**
 * Script: Generate 300 Final Exam Questions (15 exams x 20 qs)
 * Chay 1 lan: php database/generate_final_questions.php
 */

require_once __DIR__ . '/../app/config/database.php';
$db = getDB();

// Lay tat ca final exams
$exams = $db->query("
    SELECT t.id, t.title, c.cefr_level
    FROM tests t
    JOIN courses c ON c.title = REPLACE(t.title, 'Final Exam: ', '')
    WHERE t.is_final = 1 AND t.is_active = 1
    ORDER BY c.cefr_level, c.sort_order
")->fetchAll();

if (empty($exams)) {
    echo "Khong tim thay final exam nao. Hay chay migration_v10 truoc.\n";
    exit(1);
}

// Update final exams with passages + sections flag
$updateStmt = $db->prepare(
    'UPDATE tests SET has_sections = 1, reading_passage = :rp, listening_transcript = :lt WHERE id = :id'
);

// Insert questions
$insertStmt = $db->prepare(
    'INSERT INTO questions (test_id, question_text, question_type, options_json, correct_answer, points, sort_order)
     VALUES (:tid, :qt, :qtype, :opt, :ca, :pts, :so)'
);

// ============================================
// READING PASSAGES by CEFR level
// ============================================
$readingPassages = [
    'A1' => "Anna is a student. She is 12 years old. She lives in a small town with her family. Every morning, she wakes up at 6:30 AM. She eats breakfast with her mother and father. Then she walks to school with her friend Lucy. School starts at 8:00 AM. Anna likes English and Math. She does not like History. After school, she goes home and does her homework. In the evening, she watches TV or reads books. She goes to bed at 9:30 PM.",
    'A2' => "Tom works in a big office in the city center. He takes the bus to work every day. The journey takes about 30 minutes. He starts work at 9 AM and finishes at 5:30 PM. For lunch, he usually goes to a small cafe near his office. He likes to eat sandwiches and drink coffee. In his free time, Tom enjoys playing tennis and going to the cinema with his friends. Last weekend, he went to a music concert. He said it was amazing. Next month, Tom plans to visit his parents who live in another city.",
    'B1' => "Climate change is one of the most serious challenges facing the world today. Scientists have shown that the Earth's temperature is rising at an alarming rate, largely due to human activities such as burning fossil fuels and deforestation. The consequences of this warming include rising sea levels, more frequent extreme weather events, and the loss of biodiversity. Many countries have agreed to reduce their carbon emissions through international agreements like the Paris Agreement. However, experts argue that current efforts are still not enough. They emphasize that both governments and individuals must take urgent action to protect the environment for future generations.",
    'B2' => "The Industrial Revolution, which began in Britain in the late 18th century, fundamentally transformed human society. Before this period, most people lived in rural areas and worked in agriculture. The invention of the steam engine and the mechanization of textile production led to the rapid growth of factories and cities. While this brought unprecedented economic growth and technological innovation, it also created serious social problems. Working conditions in early factories were often dangerous, and child labor was common. The gap between the wealthy factory owners and the working class widened considerably. These conditions eventually led to the rise of labor movements and significant social reforms throughout the 19th century.",
    'C1' => "The philosophical tension between determinism and free will has preoccupied thinkers for centuries. Determinism posits that every event, including human cognition and behavior, is causally determined by an unbroken chain of prior occurrences. If this thesis holds true, the notion of moral responsibility becomes profoundly problematic — we cannot justifiably hold individuals accountable for actions that were, in principle, inevitable. Compatibilists attempt to reconcile these seemingly incompatible positions by arguing that free will is compatible with determinism if we define freedom as the capacity to act according to one's own motivations without external coercion. However, incompatibilists maintain that this redefinition merely sidesteps the deeper metaphysical question of whether genuine choice exists in a deterministic universe.",
];

// ============================================
// READING QUESTIONS by CEFR level (10 per exam)
// ============================================
$readingQuestions = [
    'A1' => [
        ['How old is Anna?', '["10", "12", "14", "16"]', '12'],
        ['What time does Anna wake up?', '["6:00 AM", "6:30 AM", "7:00 AM", "7:30 AM"]', '6:30 AM'],
        ['How does Anna go to school?', '["By bus", "By car", "She walks", "By bike"]', 'She walks'],
        ['Which subject does Anna NOT like?', '["English", "Math", "History", "Science"]', 'History'],
        ['What time does Anna go to bed?', '["8:30 PM", "9:00 PM", "9:30 PM", "10:00 PM"]', '9:30 PM'],
        ['Who does Anna eat breakfast with?', '["Her friends", "Her teachers", "Her parents", "Her sister"]', 'Her parents'],
        ['What does Anna do after school?', '["Plays outside", "Does homework", "Goes shopping", "Visits friends"]', 'Does homework'],
        ['Who is Lucy?', '["Annas sister", "Annas teacher", "Annas friend", "Annas mother"]', 'Annas friend'],
        ['Where does Anna live?', '["In a big city", "In a small town", "On a farm", "Near the sea"]', 'In a small town'],
        ['What time does school start?', '["7:00 AM", "7:30 AM", "8:00 AM", "8:30 AM"]', '8:00 AM'],
    ],
    'A2' => [
        ['How does Tom get to work?', '["By car", "By bus", "By train", "By bike"]', 'By bus'],
        ['How long is Tom\'s journey to work?', '["15 minutes", "20 minutes", "30 minutes", "45 minutes"]', '30 minutes'],
        ['What time does Tom finish work?', '["4:30 PM", "5:00 PM", "5:30 PM", "6:00 PM"]', '5:30 PM'],
        ['What does Tom like to eat for lunch?', '["Pizza", "Salad", "Sandwiches", "Pasta"]', 'Sandwiches'],
        ['What sport does Tom enjoy playing?', '["Football", "Basketball", "Tennis", "Swimming"]', 'Tennis'],
        ['Where did Tom go last weekend?', '["A restaurant", "A music concert", "A museum", "A theme park"]', 'A music concert'],
        ['Where does Tom work?', '["In a school", "In a hospital", "In a big office", "In a restaurant"]', 'In a big office'],
        ['What does Tom plan to do next month?', '["Go on vacation", "Buy a new car", "Visit his parents", "Move to a new city"]', 'Visit his parents'],
        ['Where does Tom usually have lunch?', '["At his desk", "At a small cafe", "At home", "At a restaurant"]', 'At a small cafe'],
        ['How did Tom feel about the concert?', '["It was boring", "It was amazing", "It was too loud", "It was disappointing"]', 'It was amazing'],
    ],
    'B1' => [
        ['What is the main topic of this passage?', '["Space exploration", "Climate change", "Internet technology", "World population"]', 'Climate change'],
        ['What is causing the Earth\'s temperature to rise?', '["Natural cycles", "Human activities", "Solar flares", "Ocean currents"]', 'Human activities'],
        ['Which is mentioned as a consequence of global warming?', '["More rain", "Rising sea levels", "Better crops", "Longer days"]', 'Rising sea levels'],
        ['What international agreement is mentioned?', '["The Kyoto Protocol", "The Paris Agreement", "The Geneva Convention", "The Montreal Protocol"]', 'The Paris Agreement'],
        ['What do experts say about current efforts?', '["They are sufficient", "They are still not enough", "They are too strict", "They are unnecessary"]', 'They are still not enough'],
        ['According to the passage, who must take action?', '["Only governments", "Only individuals", "Both governments and individuals", "Only scientists"]', 'Both governments and individuals'],
        ['What is deforestation?', '["Planting more trees", "Cutting down forests", "Protecting animals", "Building cities"]', 'Cutting down forests'],
        ['What does "alarming rate" mean?', '["A slow speed", "A worrying speed", "A normal speed", "A decreasing speed"]', 'A worrying speed'],
        ['What is biodiversity?', '["Types of buildings", "Variety of life forms", "Different weather patterns", "Ocean currents"]', 'Variety of life forms'],
        ['What is the purpose of the Paris Agreement?', '["Increase trade", "Reduce carbon emissions", "Build more factories", "Create more jobs"]', 'Reduce carbon emissions'],
    ],
    'B2' => [
        ['Where did the Industrial Revolution begin?', '["France", "Germany", "Britain", "United States"]', 'Britain'],
        ['When did the Industrial Revolution begin?', '["Early 18th century", "Late 18th century", "Early 19th century", "Mid 17th century"]', 'Late 18th century'],
        ['What invention is mentioned as a key driver?', '["The telephone", "The steam engine", "The computer", "The automobile"]', 'The steam engine'],
        ['What happened to cities during this period?', '["They shrank", "They grew rapidly", "They stayed the same", "They disappeared"]', 'They grew rapidly'],
        ['What was a social problem caused by industrialization?', '["Too many holidays", "Dangerous working conditions", "Free education", "Universal healthcare"]', 'Dangerous working conditions'],
        ['What was common in early factories?', '["High wages", "Short hours", "Child labor", "Paid vacations"]', 'Child labor'],
        ['What gap widened during this period?', '["The education gap", "The wealth gap", "The technology gap", "The age gap"]', 'The wealth gap'],
        ['What resulted from the poor conditions?', '["More factories", "Labor movements and reforms", "Decreased production", "International trade"]', 'Labor movements and reforms'],
        ['Before industrialization, where did most people live?', '["In cities", "In rural areas", "In other countries", "Near factories"]', 'In rural areas'],
        ['What industry was mechanized first?', '["Steel production", "Textile production", "Food processing", "Automobile manufacturing"]', 'Textile production'],
    ],
    'C1' => [
        ['What is the main philosophical tension discussed?', '["Good vs Evil", "Determinism vs Free Will", "Reason vs Emotion", "Nature vs Nurture"]', 'Determinism vs Free Will'],
        ['What does determinism claim?', '["Humans have complete freedom", "Every event is causally determined", "Free will is an illusion", "Morality is absolute"]', 'Every event is causally determined'],
        ['What problem does determinism create for moral responsibility?', '["It makes punishment easier", "Actions become inevitable and not freely chosen", "It proves we are responsible", "It defines responsibility clearly"]', 'Actions become inevitable and not freely chosen'],
        ['What do compatibilists believe?', '["Free will and determinism are compatible", "Free will does not exist", "Determinism is false", "Morality is impossible"]', 'Free will and determinism are compatible'],
        ['How do compatibilists define freedom?', '["Acting randomly", "Acting without external coercion", "Acting against ones will", "Acting without thinking"]', 'Acting without external coercion'],
        ['What do incompatibilists argue?', '["Determinism is proven true", "The compatibilist redefinition avoids the real question", "Free will is an illusion", "Morality is simple"]', 'The compatibilist redefinition avoids the real question'],
        ['What does "causally determined" mean?', '["Caused by nothing", "Having a prior cause", "Random", "Unpredictable"]', 'Having a prior cause'],
        ['What does "metaphysical" refer to?', '["Physical science", "Beyond physical reality", "Medical science", "Computer science"]', 'Beyond physical reality'],
        ['What is "moral responsibility"?', '["Being accountable for ones actions", "Having no duties", "Ignoring ethics", "Following laws blindly"]', 'Being accountable for ones actions'],
        ['What does "coercion" mean?', '["Freedom", "Force or pressure", "Agreement", "Permission"]', 'Force or pressure'],
    ],
];

// ============================================
// LISTENING TRANSCRIPTS by CEFR level
// ============================================
$listeningTranscripts = [
    'A1' => "Hello. My name is Peter. I am ten years old. I have a dog. My dog is brown. I like to play with my dog in the park. My favorite food is pizza. I also like ice cream. I do not like vegetables very much. My best friend is Tom. We go to the same school. We are in the same class. After school, we often play football together.",
    'A2' => "Good morning everyone. Today I want to tell you about my holiday. Last summer, I went to the beach with my family. We stayed in a nice hotel near the sea. The weather was beautiful. We swam in the sea every day and built many sandcastles on the beach. My little brother found some interesting shells. In the evenings, we went to different restaurants and tried local seafood. It was the best holiday I have ever had. I hope we can go again next year.",
    'B1' => "Welcome to today's presentation about healthy eating habits. Research shows that eating a balanced diet can significantly improve your overall health and well-being. You should try to eat at least five portions of fruit and vegetables every day. It is also important to drink enough water — experts recommend about two liters per day. Try to reduce your intake of processed foods and sugary drinks. Instead, choose whole grains, lean proteins, and healthy fats. Remember, small changes can make a big difference. Start by adding one extra vegetable to your dinner tonight.",
    'B2' => "Today I want to discuss the impact of artificial intelligence on the modern workplace. Over the past decade, AI has transformed numerous industries, from healthcare to finance. While some fear that automation will eliminate jobs, the reality is more nuanced. AI tends to replace specific tasks rather than entire occupations. In many cases, it augments human capabilities, allowing workers to focus on more creative and strategic activities. However, this transition requires significant investment in retraining and education. Companies and governments must work together to ensure that the benefits of AI are shared broadly across society.",
    'C1' => "The phenomenon of linguistic relativity, often associated with the Sapir-Whorf hypothesis, proposes that the structure of a language influences its speakers' worldview and cognitive processes. While the strong version of this hypothesis — that language determines thought — has been largely discredited, a more moderate interpretation has gained empirical support. Research demonstrates that linguistic differences can affect color perception, spatial reasoning, and even memory encoding. For instance, speakers of languages that use absolute spatial references rather than relative ones show remarkable navigational abilities. This suggests that while language does not imprison thought, it does provide a framework that shapes habitual patterns of attention and categorization.",
];

// ============================================
// LISTENING QUESTIONS by CEFR level (10 per exam)
// ============================================
$listeningQuestions = [
    'A1' => [
        ['(Nghe) What is the boy\'s name?', '["Peter", "Tom", "John", "David"]', 'Peter'],
        ['(Nghe) How old is Peter?', '["8", "9", "10", "11"]', '10'],
        ['(Nghe) What pet does Peter have?', '["A cat", "A dog", "A bird", "A fish"]', 'A dog'],
        ['(Nghe) What color is Peter\'s dog?', '["Black", "White", "Brown", "Gray"]', 'Brown'],
        ['(Nghe) What is Peter\'s favorite food?', '["Hamburger", "Pasta", "Pizza", "Sandwich"]', 'Pizza'],
        ['(Nghe) What does Peter NOT like?', '["Pizza", "Ice cream", "Vegetables", "Fruit"]', 'Vegetables'],
        ['(Nghe) Who is Peter\'s best friend?', '["Tom", "John", "David", "Mike"]', 'Tom'],
        ['(Nghe) Where do they play football?', '["At school", "In the park", "At home", "At the stadium"]', 'At school'],
        ['(Nghe) What do Peter and Tom do after school?', '["Watch TV", "Play football", "Do homework", "Play video games"]', 'Play football'],
        ['(Nghe) What else does Peter like besides pizza?', '["Cake", "Ice cream", "Chocolate", "Candy"]', 'Ice cream'],
    ],
    'A2' => [
        ['(Nghe) Where did the speaker go on holiday?', '["The mountains", "The beach", "The city", "The countryside"]', 'The beach'],
        ['(Nghe) Who did the speaker go with?', '["Friends", "Colleagues", "Family", "Alone"]', 'Family'],
        ['(Nghe) Where did they stay?', '["In a tent", "In a hotel", "In an apartment", "With relatives"]', 'In a hotel'],
        ['(Nghe) How was the weather?', '["Rainy", "Cloudy", "Beautiful", "Cold"]', 'Beautiful'],
        ['(Nghe) What did they do every day?', '["Went shopping", "Swam in the sea", "Visited museums", "Read books"]', 'Swam in the sea'],
        ['(Nghe) What did the brother find?', '["Toys", "Shells", "Rocks", "Coins"]', 'Shells'],
        ['(Nghe) Where did they eat in the evenings?', '["At the hotel", "At different restaurants", "At home", "On the beach"]', 'At different restaurants'],
        ['(Nghe) What type of food did they try?', '["Chinese food", "Italian food", "Local seafood", "Fast food"]', 'Local seafood'],
        ['(Nghe) How does the speaker describe this holiday?', '["The worst ever", "The best ever", "Average", "Disappointing"]', 'The best ever'],
        ['(Nghe) What does the speaker hope?', '["To never return", "To go again next year", "To move there", "To forget about it"]', 'To go again next year'],
    ],
    'B1' => [
        ['(Nghe) What is the main topic of the presentation?', '["Exercise routines", "Healthy eating habits", "Sleep patterns", "Stress management"]', 'Healthy eating habits'],
        ['(Nghe) How many portions of fruit and vegetables are recommended per day?', '["Three", "Four", "Five", "Seven"]', 'Five'],
        ['(Nghe) How much water do experts recommend per day?', '["One liter", "Two liters", "Three liters", "Four liters"]', 'Two liters'],
        ['(Nghe) What should you reduce in your diet?', '["Fruits and vegetables", "Processed foods and sugary drinks", "Water intake", "Whole grains"]', 'Processed foods and sugary drinks'],
        ['(Nghe) What should you choose instead?', '["Processed foods", "Fast food", "Whole grains and lean proteins", "Sugary snacks"]', 'Whole grains and lean proteins'],
        ['(Nghe) What can a balanced diet significantly improve?', '["Only physical health", "Overall health and well-being", "Only mental health", "Only energy levels"]', 'Overall health and well-being'],
        ['(Nghe) What does the speaker suggest to start with?', '["Going to the gym", "Adding one extra vegetable to dinner", "Skipping breakfast", "Drinking coffee"]', 'Adding one extra vegetable to dinner'],
        ['(Nghe) What kind of fats does the speaker recommend?', '["Trans fats", "Saturated fats", "Healthy fats", "Any kind of fat"]', 'Healthy fats'],
        ['(Nghe) According to the speaker, do small changes matter?', '["No, only big changes matter", "Yes, they can make a big difference", "Only for young people", "Only for athletes"]', 'Yes, they can make a big difference'],
        ['(Nghe) What should you add to dinner tonight?', '["An extra vegetable", "More meat", "Dessert", "Soda"]', 'An extra vegetable'],
    ],
    'B2' => [
        ['(Nghe) What technology is being discussed?', '["Robotics", "Artificial intelligence", "Blockchain", "Quantum computing"]', 'Artificial intelligence'],
        ['(Nghe) Over what time period has AI transformed industries?', '["The past year", "The past five years", "The past decade", "The past century"]', 'The past decade'],
        ['(Nghe) What fear do some people have about AI?', '["It will become too expensive", "It will eliminate jobs", "It will stop working", "It will need too much electricity"]', 'It will eliminate jobs'],
        ['(Nghe) According to the speaker, what does AI tend to replace?', '["Entire occupations", "Specific tasks", "Nothing at all", "Only manual labor"]', 'Specific tasks'],
        ['(Nghe) What does AI do in many cases?', '["Replace all workers", "Augment human capabilities", "Decrease productivity", "Eliminate creativity"]', 'Augment human capabilities'],
        ['(Nghe) What does the transition require significant investment in?', '["New buildings", "Retraining and education", "Entertainment", "Marketing"]', 'Retraining and education'],
        ['(Nghe) Who must work together according to the speaker?', '["Only companies", "Only governments", "Companies and governments", "Only workers"]', 'Companies and governments'],
        ['(Nghe) What should the benefits of AI be?', '["Given only to the wealthy", "Shared broadly across society", "Kept secret", "Limited to tech companies"]', 'Shared broadly across society'],
        ['(Nghe) Which industries are mentioned as being transformed?', '["Only healthcare", "Healthcare and finance", "Only finance", "Only technology"]', 'Healthcare and finance'],
        ['(Nghe) Is the reality of AI\'s impact simple or nuanced?', '["Very simple", "More nuanced", "Completely clear", "Impossible to understand"]', 'More nuanced'],
    ],
    'C1' => [
        ['(Nghe) What hypothesis is discussed?', '["Natural selection", "Linguistic relativity", "Quantum mechanics", "General relativity"]', 'Linguistic relativity'],
        ['(Nghe) What does the strong version claim?', '["Language has no effect on thought", "Language determines thought", "Thought determines language", "Language and thought are unrelated"]', 'Language determines thought'],
        ['(Nghe) What is the status of the strong version?', '["Fully proven", "Largely discredited", "Still under debate", "Recently discovered"]', 'Largely discredited'],
        ['(Nghe) What has the moderate version gained?', '["Public popularity", "Empirical support", "Political backing", "Financial investment"]', 'Empirical support'],
        ['(Nghe) What can linguistic differences affect?', '["Only grammar", "Color perception and spatial reasoning", "Only vocabulary", "Only pronunciation"]', 'Color perception and spatial reasoning'],
        ['(Nghe) What remarkable ability do speakers of certain languages show?', '["Musical talent", "Navigational abilities", "Mathematical skills", "Athletic performance"]', 'Navigational abilities'],
        ['(Nghe) What does language provide according to the conclusion?', '["A prison for thought", "A framework for attention and categorization", "No effect whatsoever", "A complete worldview"]', 'A framework for attention and categorization'],
        ['(Nghe) What kind of spatial references are mentioned?', '["Relative", "Absolute", "Abstract", "Geographic"]', 'Absolute'],
        ['(Nghe) What cognitive process is mentioned besides perception?', '["Dreaming", "Memory encoding", "Reflexes", "Instinct"]', 'Memory encoding'],
        ['(Nghe) Does language imprison thought according to the passage?', '["Yes, completely", "No, it does not", "Sometimes", "Only in certain cultures"]', 'No, it does not'],
    ],
];

// ============================================
// GENERATE QUESTIONS
// ============================================

$count = 0;
foreach ($exams as $exam) {
    $level = $exam['cefr_level'];
    $examId = $exam['id'];

    // Update exam with passage, transcript, sections flag
    $updateStmt->execute([
        'rp' => $readingPassages[$level] ?? $readingPassages['B1'],
        'lt' => $listeningTranscripts[$level] ?? $listeningTranscripts['B1'],
        'id' => $examId,
    ]);

    // Insert 10 Reading questions
    $rq = $readingQuestions[$level] ?? $readingQuestions['B1'];
    foreach ($rq as $i => $q) {
        $insertStmt->execute([
            'tid' => $examId,
            'qt' => $q[0],
            'qtype' => 'multiple_choice',
            'opt' => $q[1],
            'ca' => $q[2],
            'pts' => 5,
            'so' => $i + 1,
        ]);
        $count++;
    }

    // Insert 10 Listening questions
    $lq = $listeningQuestions[$level] ?? $listeningQuestions['B1'];
    foreach ($lq as $i => $q) {
        $insertStmt->execute([
            'tid' => $examId,
            'qt' => $q[0],
            'qtype' => 'multiple_choice',
            'opt' => $q[1],
            'ca' => $q[2],
            'pts' => 5,
            'so' => $i + 11, // Sort order 11-20
        ]);
        $count++;
    }

    echo "OK: Exam {$examId} ({$exam['title']}) — Level {$level}\n";
}

echo "\nDone! Generated {$count} questions for " . count($exams) . " final exams.\n";
echo "Run: composer dump-autoload (if needed)\n";
