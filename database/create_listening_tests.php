<?php
require __DIR__ . '/../app/config/database.php';
$db = getDB();

// 1. Fix remaining questions in test #10 that lack passages
$stmt = $db->query("SELECT id, question_text FROM questions WHERE test_id = 10 AND passage IS NULL");
foreach ($stmt->fetchAll() as $q) {
    $text = $q['question_text'];
    $passage = null;
    if (preg_match('/"([^"]+)"/', $text, $m)) {
        $passage = $m[1];
    }
    if ($passage) {
        $db->prepare('UPDATE questions SET passage = :p WHERE id = :qid')
           ->execute(['p' => $passage, 'qid' => $q['id']]);
        $newText = preg_replace('/^Listen:\s*"[^"]+"\.?\s*/', '', $text);
        if (empty(trim($newText))) $newText = 'What did you hear?';
        $db->prepare('UPDATE questions SET question_text = :t WHERE id = :qid')
           ->execute(['t' => trim($newText), 'qid' => $q['id']]);
        echo "  Fixed Q#{$q['id']}\n";
    }
}

// 2. Find courses without listening tests
$stmt = $db->query("SELECT c.id, c.title, c.cefr_level FROM courses c WHERE c.is_active = 1
    AND c.id NOT IN (SELECT DISTINCT tp.course_id FROM topics tp JOIN tests t ON t.topic_id = tp.id WHERE t.test_type = 'listening' AND t.is_active = 1 AND t.is_final = 0)
    ORDER BY FIELD(c.cefr_level,'A1','A2','B1','B2','C1'), c.sort_order");
$courses = $stmt->fetchAll();
echo "\nCourses without listening test: " . count($courses) . "\n";

$listeningPassages = [
    'A1' => ['I go to school every morning at 7 AM.','She has two brothers and one sister.','We eat dinner together as a family.','The weather is very nice today.','My favorite color is blue.','I like to play football with my friends.','The cat is sleeping on the sofa.','He drinks coffee every morning.'],
    'A2' => ['The train leaves at 3 PM from platform 5.','I would like to book a room for two nights.','You need to turn left at the traffic light.','She enjoys listening to music in her free time.','The museum is open from 9 AM to 6 PM.','I have been working here for three years.','Could you please tell me where the bank is?','They are planning to visit Paris next summer.'],
    'B1' => ['The company has decided to expand into international markets.','Climate change is one of the biggest challenges facing humanity.','She has been studying English for over five years now.','The new policy will affect all employees starting next month.','Social media has changed the way we communicate.','The report highlights several areas that need improvement.','Many people believe that education is the key to success.','The government announced plans to invest in renewable energy.'],
    'B2' => ['The economic implications of this policy are far-reaching and complex.','It is essential to consider multiple perspectives when analyzing history.','The study demonstrates a significant correlation between the two variables.','Negotiations between the two parties have reached a critical stage.','The author argues that technology fundamentally alters human interaction.','Despite considerable opposition, the proposal was ultimately accepted.','The phenomenon can be explained through several theoretical frameworks.','Effective leadership requires both emotional intelligence and strategic thinking.'],
    'C1' => ['The epistemological foundations of this argument warrant closer examination.','Subsequent research has corroborated the initial findings with remarkable precision.','The juxtaposition of contrasting ideologies reveals underlying societal tensions.','Paradoxically, the proliferation of information has led to greater polarization.','The nuanced interplay between socioeconomic factors defies simplistic analysis.','Her dissertation elucidates the intricate relationship between language and cognition.','The ramifications of this paradigm shift extend far beyond the immediate context.','A comprehensive meta-analysis of the extant literature yields compelling evidence.'],
];

function genQuestions($passage) {
    $words = explode(' ', $passage);
    $subject = $words[0];
    return [
        ['text' => 'What is the main subject of the sentence?', 'opts' => [$subject, 'They', 'Everyone', 'Nobody'], 'ans' => $subject],
        ['text' => 'What is the speaker talking about?', 'opts' => ['Daily life', 'An event or situation', 'Travel', 'A problem'], 'ans' => 'An event or situation'],
        ['text' => 'How many main ideas does the speaker express?', 'opts' => ['One', 'Two', 'Three', 'Several'], 'ans' => 'One'],
        ['text' => 'What can you infer from the statement?', 'opts' => ['The information is important', 'It is a joke', 'The speaker is angry', 'It is a question'], 'ans' => 'The information is important'],
        ['text' => 'What is the purpose of this statement?', 'opts' => ['To inform', 'To entertain', 'To persuade', 'To complain'], 'ans' => 'To inform'],
    ];
}

$created = 0;
foreach ($courses as $course) {
    $level = $course['cefr_level'];
    $stmt = $db->prepare('SELECT id, name FROM topics WHERE course_id = :cid AND is_active = 1 ORDER BY sort_order LIMIT 1');
    $stmt->execute(['cid' => $course['id']]);
    $topic = $stmt->fetch();
    if (!$topic) continue;

    $testTitle = $topic['name'] . ' - Listening Practice';
    $db->prepare("INSERT INTO tests (topic_id, title, test_type, duration_minutes, pass_score, is_final, is_active) VALUES (:tid, :t, 'listening', 15, 60, 0, 1)")
       ->execute(['tid' => $topic['id'], 't' => $testTitle]);
    $testId = $db->lastInsertId();

    $passages = $listeningPassages[$level] ?? $listeningPassages['B1'];
    shuffle($passages);
    $selected = array_slice($passages, 0, 5);
    $qBank = genQuestions($selected[0]);

    foreach ($selected as $i => $passage) {
        $q = $qBank[$i % count($qBank)];
        $db->prepare('INSERT INTO questions (test_id, question_text, question_type, options_json, correct_answer, points, sort_order, passage) VALUES (:tid, :qt, :type, :opts, :ans, 1, :so, :p)')
           ->execute([
               'tid' => $testId, 'qt' => $q['text'], 'type' => 'multiple_choice',
               'opts' => json_encode($q['opts']), 'ans' => $q['ans'], 'so' => $i + 1,
               'p' => $passage,
           ]);
    }
    $created++;
    echo "  [+] {$level} Course #{$course['id']}: $testTitle (Test #$testId, 5 questions)\n";
}

echo "\nCreated $created new listening tests.\n";
echo "Total listening tests: " . $db->query("SELECT COUNT(*) FROM tests WHERE test_type = 'listening' AND is_active = 1 AND is_final = 0")->fetchColumn() . "\n";
echo "Done.\n";
