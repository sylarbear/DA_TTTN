<?php

/**
 * CourseController
 * Trang khóa học — tổ chức nội dung theo lộ trình Coursera-style
 */
class CourseController extends Controller
{
    /**
     * GET /course
     * Danh sách khóa học của user, chia 3 nhóm: mastered / đang học / locked
     */
    public function index()
    {
        Middleware::requireLogin();

        $userId = $_SESSION['user_id'];

        // Nếu chưa có placement → redirect làm placement trước
        if (!CourseProgress::hasProgress($userId)) {
            $this->setFlash('info', 'Vui lòng xác định trình độ của bạn trước khi học.');
            return $this->redirect('placement/intro');
        }

        $courseProgressModel = $this->model('CourseProgress');
        $userCourses = $courseProgressModel->getUserCourses($userId);

        // Phân nhóm
        $mastered = [];
        $active   = [];   // unlocked + in_progress
        $locked   = [];

        foreach ($userCourses as $c) {
            if ($c['status'] === 'mastered') {
                $mastered[] = $c;
            } elseif ($c['status'] === 'locked') {
                $locked[] = $c;
            } else {
                $active[] = $c;
            }
        }

        // Lấy % hoàn thành + last lesson cho khóa đang active
        $courseModel = $this->model('Course');
        foreach ($active as &$c) {
            $c['completion_percent'] = $courseModel->getCompletionPercent($userId, $c['id']);
            $c['last_lesson'] = $this->getLastViewedLesson($userId, $c['id']);
        }
        unset($c);

        $this->view('course/index', [
            'title'      => 'Khóa học - ' . APP_NAME,
            'mastered'   => $mastered,
            'active'     => $active,
            'locked'     => $locked,
            'user'       => Middleware::user(),
        ]);
    }

    /**
     * GET /course/show/{id}
     * Trang thông tin khóa học (landing page) — overview Coursera-style
     */
    public function show($id = null)
    {
        Middleware::requireLogin();

        if (!$id) return $this->redirect('course');

        $userId = $_SESSION['user_id'];

        $courseModel = $this->model('Course');
        $course = $courseModel->getWithChapters($id);
        if (!$course) return $this->redirect('course');

        // Kiểm tra quyền truy cập
        $progress = $courseModel->getUserProgress($userId, $id);
        if (!$progress || $progress['status'] === 'locked') {
            $this->setFlash('error', 'Khóa học này chưa được mở khóa. Hãy hoàn thành khóa trước.');
            return $this->redirect('course');
        }

        // Lấy bài học đang học dở cuối cùng (cho resume banner)
        $lastLesson = $this->getLastViewedLesson($userId, $id);

        // Lấy final exam info — luôn hiển thị section, trạng thái do canTakeFinal quyết định
        $canTakeFinal = $courseModel->canTakeFinalExam($userId, $id);
        $finalExam    = $courseModel->getFinalExam($id);

        // Course overview data
        $totalLessons = 0;
        foreach ($course['chapters'] as $ch) {
            $totalLessons += $ch['lesson_count'];
        }

        $courseOverview = [
            'total_hours'    => 0,
            'total_chapters' => count($course['chapters']),
            'total_lessons'  => $totalLessons,
            'total_vocab'    => array_sum(array_column($course['chapters'], 'vocab_count')),
            'total_quizzes'  => array_sum(array_column($course['chapters'], 'test_count')),
            'middle_tests'   => 0,
        ];

        $totalMinutes = $courseModel->getTotalMinutes((int)$id);
        $totalHours   = $totalMinutes > 0 ? round($totalMinutes / 60, 1) : 0;
        $middleTestCount = $courseModel->countMiddleTests((int)$id);

        $courseOverview['total_hours']  = $totalHours;
        $courseOverview['middle_tests'] = $middleTestCount;

        // Skills array
        $skills = [
            ['name' => 'Nghe (Listening)',  'icon' => 'fa-headphones',  'weight' => (int)($course['listening_weight'] ?? 25), 'color' => '#3b82f6'],
            ['name' => 'Nói (Speaking)',    'icon' => 'fa-microphone',  'weight' => (int)($course['speaking_weight'] ?? 25),  'color' => '#f59e0b'],
            ['name' => 'Đọc (Reading)',     'icon' => 'fa-book-open',   'weight' => (int)($course['reading_weight'] ?? 25),   'color' => '#10b981'],
            ['name' => 'Viết (Writing)',    'icon' => 'fa-pen',         'weight' => (int)($course['writing_weight'] ?? 25),   'color' => '#8b5cf6'],
        ];

        $objectives = array_values(array_filter(
            array_map('trim', explode("\n", $course['objectives'] ?? '')),
            fn($line) => $line !== ''
        ));

        $requirements = array_values(array_filter(
            array_map('trim', explode("\n", $course['requirements'] ?? '')),
            fn($line) => $line !== ''
        ));

        $skillLevelMap = ['A1' => 'Cơ bản', 'A2' => 'Cơ bản', 'B1' => 'Trung cấp', 'B2' => 'Trung cấp', 'C1' => 'Nâng cao'];
        $skillLevel = $skillLevelMap[$course['cefr_level']] ?? 'Cơ bản';

        $this->view('course/show', [
            'title'          => $course['title'] . ' - ' . APP_NAME,
            'course'         => $course,
            'progress'       => $progress,
            'lastLesson'     => $lastLesson,
            'courseOverview' => $courseOverview,
            'finalExam'      => $finalExam,
            'canTakeFinal'   => $canTakeFinal,
            'isReview'       => $progress['status'] === 'mastered',
            'user'           => Middleware::user(),
            'skills'         => $skills,
            'objectives'     => $objectives,
            'requirements'   => $requirements,
            'skillLevel'     => $skillLevel,
        ]);
    }

    /**
     * GET /course/learn/{id}
     * Trang học tập — giao diện 2-panel (sidebar + content)
     */
    public function learn($id = null)
    {
        Middleware::requireLogin();

        if (!$id) return $this->redirect('course');

        $userId = $_SESSION['user_id'];

        $courseModel = $this->model('Course');
        $course = $courseModel->getWithChapters($id);
        if (!$course) return $this->redirect('course');

        // Kiểm tra quyền truy cập
        $progress = $courseModel->getUserProgress($userId, $id);
        if (!$progress || $progress['status'] === 'locked') {
            $this->setFlash('error', 'Khóa học này chưa được mở khóa. Hãy hoàn thành khóa trước.');
            return $this->redirect('course');
        }

        // Nếu đang unlocked → tự động chuyển sang in_progress
        if ($progress['status'] === 'unlocked') {
            $courseProgressModel = $this->model('CourseProgress');
            $courseProgressModel->startCourse($userId, $id);
            $progress['status'] = 'in_progress';
        }

        // Lấy completed lesson IDs từ DB
        $completedLessonIds = $this->getCompletedLessonIds($userId, $id);

        // Đánh dấu trạng thái từng chương
        foreach ($course['chapters'] as &$chapter) {
            $chapter['is_completed'] = $courseModel->isChapterCompleted($userId, $chapter['id']);
        }
        unset($chapter);

        // Build flat lesson list cho navigation (Next/Prev)
        $lessonList = $this->buildLessonList($course['chapters']);
        $lessonMap  = [];
        foreach ($lessonList as $idx => $li) {
            $lessonMap[$li['id']] = $idx;
        }

        // Lấy bài học đang học dở cuối cùng
        $lastLesson = $this->getLastViewedLesson($userId, $id);

        // Kiểm tra điều kiện mở final exam
        $canTakeFinal = $courseModel->canTakeFinalExam($userId, $id);
        $finalExam    = $canTakeFinal ? $courseModel->getFinalExam($id) : null;
        $completionPercent = $courseModel->getCompletionPercent($userId, $id);

        // Lấy kết quả final exam gần nhất (nếu có)
        $finalResult = null;
        if ($finalExam) {
            $stmt = getDB()->prepare(
                'SELECT tr.* FROM test_results tr
                 WHERE tr.user_id = :uid AND tr.test_id = :tid
                 ORDER BY tr.completed_at DESC LIMIT 1'
            );
            $stmt->execute(['uid' => $userId, 'tid' => $finalExam['id']]);
            $finalResult = $stmt->fetch();
        }

        $totalLessons = count($lessonList);

        $this->view('course/learn', [
            'title'              => $course['title'] . ' - ' . APP_NAME,
            'course'             => $course,
            'progress'           => $progress,
            'completionPercent'  => $completionPercent,
            'completedLessonIds' => $completedLessonIds,
            'lessonList'         => $lessonList,
            'lessonMap'          => $lessonMap,
            'lastLesson'         => $lastLesson,
            'courseOverview'     => [],
            'canTakeFinal'       => $canTakeFinal,
            'finalExam'          => $finalExam,
            'finalResult'        => $finalResult,
            'isReview'           => $progress['status'] === 'mastered',
            'user'               => Middleware::user(),
        ]);
    }

    /**
     * GET /course/loadLesson/{id} (AJAX)
     * Trả về JSON với HTML nội dung bài học + metadata navigation
     */
    public function loadLesson($id = null)
    {
        Middleware::requireLogin();
        if (!$id) return $this->json(['error' => 'Invalid lesson ID'], 400);

        $userId     = $_SESSION['user_id'];
        $lessonModel = $this->model('Lesson');
        $lesson      = $lessonModel->getWithContents($id);
        if (!$lesson) return $this->json(['error' => 'Không tìm thấy bài học.'], 404);

        // Lấy course_id từ topic
        $stmt = getDB()->prepare(
            'SELECT t.course_id, t.id as topic_id FROM lessons l
             JOIN topics t ON t.id = l.topic_id
             WHERE l.id = :lid'
        );
        $stmt->execute(['lid' => $id]);
        $topicInfo = $stmt->fetch();
        $courseId  = $topicInfo ? $topicInfo['course_id'] : null;
        $topicId   = $topicInfo ? $topicInfo['topic_id'] : null;

        // Lưu completion vào DB (thay vì session)
        $this->saveLessonProgress($userId, $id, $courseId);

        // Increment user_progress
        if ($topicId) {
            $progressModel = $this->model('UserProgress');
            $progressModel->increment($userId, $topicId, 'lessons_completed');
        }

        // Build lesson list để tính prev/next
        $courseModel = $this->model('Course');
        $course      = $courseModel->getWithChapters($courseId);
        $lessonList  = $this->buildLessonList($course['chapters'] ?? []);
        $currentIdx  = null;
        foreach ($lessonList as $idx => $li) {
            if ($li['id'] == $id) { $currentIdx = $idx; break; }
        }

        $prevId = ($currentIdx !== null && $currentIdx > 0) ? $lessonList[$currentIdx - 1]['id'] : null;
        $nextId = ($currentIdx !== null && $currentIdx < count($lessonList) - 1) ? $lessonList[$currentIdx + 1]['id'] : null;

        // Render HTML bài học
        ob_start();
        ?>
        <div class="lesson-inline" data-lesson-id="<?= $id ?>">
            <div class="lesson-header">
                <span class="lesson-type-badge"><i class="fas fa-book-open"></i> Lý thuyết</span>
                <?php if (!empty($lesson['estimated_minutes'])): ?>
                <span class="lesson-duration"><i class="fas fa-clock"></i> <?= $lesson['estimated_minutes'] ?> phút</span>
                <?php endif; ?>
            </div>
            <h2><?= htmlspecialchars($lesson['title']) ?></h2>
            <?php foreach ($lesson['contents'] as $block): ?>
                <?php if ($block['content_type'] === 'text'): ?>
                    <div class="lesson-text"><?= $block['content'] ?></div>
                <?php elseif ($block['content_type'] === 'image'): ?>
                    <figure class="lesson-figure">
                        <img src="<?= htmlspecialchars($block['content']) ?>" alt="" class="lesson-image" loading="lazy">
                    </figure>
                <?php elseif ($block['content_type'] === 'audio'): ?>
                    <div class="lesson-media-wrapper">
                        <div class="lesson-media-label"><i class="fas fa-headphones"></i> Audio</div>
                        <audio controls src="<?= htmlspecialchars($block['content']) ?>" class="lesson-audio" data-enhanced="true"></audio>
                    </div>
                <?php elseif ($block['content_type'] === 'video'): ?>
                    <div class="lesson-media-wrapper">
                        <div class="lesson-media-label"><i class="fas fa-play-circle"></i> Video</div>
                        <video controls src="<?= htmlspecialchars($block['content']) ?>" class="lesson-video" data-enhanced="true" playsinline></video>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>

            <div class="lesson-complete-section" id="lessonCompleteSection">
                <button class="lesson-complete-btn is-completed" onclick="CoursePlayer.toggleComplete(<?= $id ?>, <?= $courseId ?>)">
                    <span class="complete-icon"><i class="fas fa-check-circle"></i></span>
                    <span class="complete-text">Đã hoàn thành</span>
                    <span class="incomplete-text">Đánh dấu hoàn thành</span>
                </button>
            </div>

            <!-- Notes Section -->
            <div class="lesson-notes-section" id="lessonNotesSection">
                <div class="notes-header" onclick="CoursePlayer.toggleNotes(<?= $id ?>)">
                    <h3><i class="fas fa-pencil-alt"></i> Ghi chú của tôi</h3>
                    <button class="notes-toggle-btn"><i class="fas fa-chevron-down"></i></button>
                </div>
                <div class="notes-body" id="notesBody-<?= $id ?>" style="display:none">
                    <textarea class="notes-textarea" id="notesText-<?= $id ?>"
                        placeholder="Viết ghi chú của bạn tại đây..."
                        oninput="CoursePlayer.saveNotes(<?= $id ?>, this.value)"></textarea>
                    <span class="notes-saved" id="notesSaved-<?= $id ?>">Đã lưu</span>
                </div>
            </div>

            <!-- Discussion Section -->
            <div class="lesson-discussion-wrapper" id="discussionWrapper-<?= $id ?>">
                <div class="discussion-loader" id="discussionLoader-<?= $id ?>">
                    <button class="btn btn-outline btn-sm" onclick="CoursePlayer.loadDiscussion(<?= $id ?>)">
                        <i class="fas fa-comments"></i> Xem thảo luận
                    </button>
                </div>
                <div class="discussion-container" id="discussionContainer-<?= $id ?>" style="display:none"></div>
            </div>
        </div>
        <?php
        $html = ob_get_clean();

        return $this->json([
            'success'       => true,
            'html'          => $html,
            'lesson_id'     => (int)$id,
            'title'         => $lesson['title'],
            'prev_lesson_id' => $prevId,
            'next_lesson_id' => $nextId,
            'topic_id'      => $topicId,
            'course_id'     => $courseId,
            'is_completed'  => true,
        ]);
    }

    /**
     * POST /course/toggleLessonComplete (AJAX)
     * Toggle trạng thái hoàn thành bài học
     */
    public function toggleLessonComplete()
    {
        Middleware::requireLogin();
        if (!$this->isMethod('POST')) {
            return $this->json(['error' => 'Method not allowed'], 405);
        }

        $input    = json_decode(file_get_contents('php://input'), true);
        if (!is_array($input)) $input = [];
        $lessonId = intval($input['lesson_id'] ?? 0);
        $courseId = intval($input['course_id'] ?? 0);
        $userId   = $_SESSION['user_id'];

        if (!$lessonId || !$courseId) {
            return $this->json(['error' => 'Dữ liệu không hợp lệ'], 400);
        }

        $db   = getDB();
        $stmt = $db->prepare('SELECT id FROM lesson_progress WHERE user_id = :uid AND lesson_id = :lid');
        $stmt->execute(['uid' => $userId, 'lid' => $lessonId]);
        $existing = $stmt->fetch();

        if ($existing) {
            // Un-mark
            $db->prepare('DELETE FROM lesson_progress WHERE id = :id')
               ->execute(['id' => $existing['id']]);
            $completed = false;
        } else {
            // Mark complete
            $db->prepare('INSERT INTO lesson_progress (user_id, lesson_id, course_id) VALUES (:uid, :lid, :cid)')
               ->execute(['uid' => $userId, 'lid' => $lessonId, 'cid' => $courseId]);
            $completed = true;

            // Cũng increment user_progress
            $stmt = $db->prepare('SELECT t.id as topic_id FROM lessons l JOIN topics t ON t.id = l.topic_id WHERE l.id = :lid');
            $stmt->execute(['lid' => $lessonId]);
            $topic = $stmt->fetch();
            if ($topic) {
                $progressModel = $this->model('UserProgress');
                if ($completed) {
                    $progressModel->increment($userId, $topic['topic_id'], 'lessons_completed');
                }
            }
        }

        // Tính lại completion %
        $courseModel = $this->model('Course');
        $percent = $courseModel->getCompletionPercent($userId, $courseId);

        return $this->json([
            'success'        => true,
            'completed'      => $completed,
            'completion_percent' => $percent,
            'can_take_final' => $courseModel->canTakeFinalExam($userId, $courseId),
        ]);
    }

    /**
     * GET /course/lessonStatuses/{courseId} (AJAX)
     * Trả về map lesson_id => completed cho tất cả lessons trong khóa
     */
    public function lessonStatuses($courseId = null)
    {
        Middleware::requireLogin();
        if (!$courseId) return $this->json(['error' => 'Invalid course ID'], 400);

        $userId   = $_SESSION['user_id'];
        $statuses = $this->getCompletedLessonIds($userId, (int)$courseId);

        return $this->json([
            'success'  => true,
            'statuses' => $statuses,
        ]);
    }

    /**
     * GET /course/loadQuiz/{id} (AJAX)
     * Trả về HTML quiz inline trong content area
     */
    public function loadQuiz($id = null)
    {
        Middleware::requireLogin();
        if (!$id) return $this->json(['error' => 'Invalid quiz ID'], 400);

        $testModel = $this->model('Test');
        $test = $testModel->getWithQuestions($id);
        if (!$test) return $this->json(['error' => 'Không tìm thấy quiz.'], 404);

        // Quiz + Listening load inline; Reading redirect ra ngoài
        $isInline = in_array($test['test_type'], ['quiz', 'multiple_choice', 'true_false', 'fill_blank', 'listening']);

        if (!$isInline) {
            return $this->json([
                'success'  => false,
                'redirect' => BASE_URL . '/test/take/' . $id,
            ]);
        }

        // Listening test yêu cầu Pro membership
        if ($test['test_type'] === 'listening' && !Middleware::isPro()) {
            return $this->json([
                'success'  => false,
                'redirect' => BASE_URL . '/membership',
            ]);
        }

        $questions = $test['questions'] ?? [];
        $totalQuestions = count($questions);
        $hasTimer = ($test['duration_minutes'] ?? 0) > 0;

        ob_start();
        $isListening = ($test['test_type'] === 'listening');
        $headerIcon = $isListening ? 'fa-headphones' : 'fa-question-circle';
        ?>
        <div class="quiz-inline" data-test-id="<?= $id ?>" data-timer="<?= $test['duration_minutes'] ?? 0 ?>">
            <div class="quiz-header">
                <h2><i class="fas <?= $headerIcon ?>"></i> <?= htmlspecialchars($test['title']) ?></h2>
                <div class="quiz-meta">
                    <span><i class="fas fa-list"></i> <?= $totalQuestions ?> câu</span>
                    <?php if ($hasTimer): ?>
                    <span class="quiz-timer" id="quizTimer">
                        <i class="fas fa-clock"></i> <span id="timerDisplay"><?= $test['duration_minutes'] ?>:00</span>
                    </span>
                    <?php endif; ?>
                    <span>Pass: <?= $test['pass_score'] ?>%</span>
                </div>
            </div>

            <form id="quizForm" class="quiz-form">
                <input type="hidden" name="test_id" value="<?= $id ?>">
                <?php foreach ($questions as $i => $q): ?>
                <div class="quiz-question-card" id="qq-<?= $i ?>" data-question="<?= $q['id'] ?>">
                    <div class="quiz-question-header">
                        <span class="quiz-question-num">Câu <?= $i + 1 ?></span>
                        <span class="quiz-question-pts"><?= $q['points'] ?> điểm</span>
                    </div>

                    <?php // Audio / TTS cho listening test ?>
                    <?php if ($isListening && !empty($q['audio_url'])): ?>
                    <div class="lesson-media-wrapper" style="margin-bottom:12px">
                        <div class="lesson-media-label"><i class="fas fa-headphones"></i> Audio</div>
                        <audio controls src="<?= htmlspecialchars($q['audio_url']) ?>" class="lesson-audio" data-enhanced="true"></audio>
                    </div>
                    <?php elseif ($isListening && !empty($q['passage'])): ?>
                    <div class="quiz-passage-box">
                        <button type="button" class="btn btn-outline btn-sm quiz-tts-btn" onclick="CoursePlayer.speakPassage(this)" data-passage="<?= htmlspecialchars($q['passage']) ?>">
                            <i class="fas fa-volume-up"></i> Nghe
                        </button>
                        <span class="listen-hint">(Bấm nút để nghe. Có thể nghe lại nhiều lần.)</span>
                    </div>
                    <?php endif; ?>

                    <div class="quiz-question-text"><?= htmlspecialchars($q['question_text']) ?></div>

                    <?php
                    $options = $q['options'] ?? [];
                    $qType   = $q['question_type'] ?? 'multiple_choice';
                    ?>

                    <?php if ($qType === 'multiple_choice' && !empty($options)): ?>
                    <div class="quiz-options">
                        <?php foreach ($options as $j => $opt): ?>
                        <label class="quiz-option">
                            <input type="radio" name="q[<?= $q['id'] ?>]" value="<?= htmlspecialchars($opt) ?>">
                            <span class="quiz-option-letter"><?= chr(65 + $j) ?></span>
                            <span class="quiz-option-text"><?= htmlspecialchars($opt) ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                    <?php elseif ($qType === 'true_false'): ?>
                    <div class="quiz-options quiz-tf">
                        <label class="quiz-option"><input type="radio" name="q[<?= $q['id'] ?>]" value="True"> <span class="quiz-option-text">True</span></label>
                        <label class="quiz-option"><input type="radio" name="q[<?= $q['id'] ?>]" value="False"> <span class="quiz-option-text">False</span></label>
                    </div>
                    <?php elseif ($qType === 'fill_blank'): ?>
                    <div class="quiz-fill">
                        <input type="text" name="q[<?= $q['id'] ?>]" class="quiz-input" placeholder="Nhập câu trả lời...">
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>

                <div class="quiz-actions">
                    <button type="button" class="btn btn-primary btn-lg" onclick="CoursePlayer.submitQuiz()">
                        <i class="fas fa-paper-plane"></i> Nộp bài
                    </button>
                </div>
            </form>

            <div id="quizResult" class="quiz-result" style="display:none"></div>
        </div>
        <?php
        $html = ob_get_clean();

        return $this->json([
            'success' => true,
            'html'    => $html,
        ]);
    }

    /**
     * POST /course/submitQuiz (AJAX)
     * Nộp bài quiz inline
     */
    public function submitQuiz()
    {
        Middleware::requireLogin();
        if (!$this->isMethod('POST')) {
            return $this->json(['error' => 'Method not allowed'], 405);
        }

        $input  = json_decode(file_get_contents('php://input'), true);
        if (!is_array($input)) $input = [];
        $testId = intval($input['test_id'] ?? 0);
        $answers = $input['answers'] ?? [];

        if (!$testId || empty($answers)) {
            return $this->json(['error' => 'Dữ liệu không hợp lệ'], 400);
        }

        $userId = $_SESSION['user_id'];
        $userAnswerModel = $this->model('UserAnswer');
        $result = $userAnswerModel->submitTest($userId, $testId, $answers, 0);

        $passed      = $result['percentage'] >= ($test['pass_score'] ?? 70);
        $testModel   = $this->model('Test');
        $test        = $testModel->find($testId);
        $topicId     = $test['topic_id'] ?? null;

        // Cập nhật user_progress nếu pass quiz
        if ($passed && $topicId) {
            $progressModel = $this->model('UserProgress');
            $progressModel->increment($userId, $topicId, 'tests_passed');

            // XP thưởng
            if (class_exists('StreakService')) {
                StreakService::addXp($userId, 20, 'quiz_passed');
            }
        }

        // Nếu là final exam → complete course
        $courseCompleted = false;
        $certificate     = null;
        $unlockedCourse  = null;

        if ($passed && ($test['is_final'] ?? 0)) {
            $courseId = $test['course_id'] ?? null;
            $course = null;
            if ($courseId) {
                $courseStmt = getDB()->prepare("SELECT id FROM courses WHERE id = ? AND is_active = 1 LIMIT 1");
                $courseStmt->execute([$courseId]);
                $course = $courseStmt->fetch();
            }

            if ($course) {
                require_once APP_PATH . '/models/CourseProgress.php';
                $cpModel         = new CourseProgress();
                $completeResult  = $cpModel->completeCourse($userId, $course['id']);
                $courseCompleted = true;

                if ($completeResult['unlocked_next']) {
                    $unlockedCourse = $completeResult['unlocked_next']['title'];
                }
                if ($completeResult['certificate']) {
                    $certificate = $completeResult['certificate'];
                    $_SESSION['new_certificate_course_id'] = $course['id'];
                }
            }
        }

        // Tính correct_count và total_questions từ details
        $correctCount   = 0;
        $totalQuestions = 0;
        if (!empty($result['details'])) {
            $totalQuestions = count($result['details']);
            foreach ($result['details'] as $d) {
                if (!empty($d['is_correct'])) $correctCount++;
            }
        }

        return $this->json([
            'success'          => true,
            'score'            => $result['score'],
            'total'            => $result['total_points'],
            'percentage'       => $result['percentage'],
            'passed'           => $passed,
            'correct_count'    => $correctCount,
            'total_questions'  => $totalQuestions,
            'course_completed' => $courseCompleted,
            'certificate'      => $certificate,
            'unlocked_course'  => $unlockedCourse,
        ]);
    }

    /**
     * GET /course/loadDiscussion/{lessonId} (AJAX)
     * Trả về HTML thảo luận cho 1 bài học
     */
    public function loadDiscussion($lessonId = null)
    {
        Middleware::requireLogin();
        if (!$lessonId) return $this->json(['error' => 'Invalid lesson ID'], 400);

        $userId  = $_SESSION['user_id'];
        $db      = getDB();

        // Lấy tất cả comments (flat, will build tree in PHP)
        $stmt = $db->prepare(
            'SELECT d.*, u.username, u.full_name, u.avatar
             FROM lesson_discussions d
             JOIN users u ON u.id = d.user_id
             WHERE d.lesson_id = :lid
             ORDER BY d.created_at ASC'
        );
        $stmt->execute(['lid' => $lessonId]);
        $comments = $stmt->fetchAll();

        // Build tree: top-level + replies
        $tree = [];
        $replies = [];
        foreach ($comments as $c) {
            if ($c['parent_id']) {
                $replies[$c['parent_id']][] = $c;
            } else {
                $tree[] = $c;
            }
        }

        ob_start();
        ?>
        <div class="discussion-section" id="discussion-<?= $lessonId ?>">
            <h3><i class="fas fa-comments"></i> Thảo luận (<?= count($comments) ?>)</h3>

            <!-- Comment form -->
            <div class="discussion-form">
                <textarea id="discussionInput-<?= $lessonId ?>" class="discussion-input"
                    placeholder="Đặt câu hỏi hoặc chia sẻ ý kiến của bạn..."
                    rows="2"></textarea>
                <button class="btn btn-primary btn-sm" onclick="CoursePlayer.postComment(<?= $lessonId ?>)">
                    <i class="fas fa-paper-plane"></i> Gửi
                </button>
            </div>

            <!-- Comments list -->
            <div class="discussion-list" id="discussionList-<?= $lessonId ?>">
                <?php if (empty($tree)): ?>
                <p class="discussion-empty">Chưa có bình luận nào. Hãy là người đầu tiên thảo luận!</p>
                <?php else: ?>
                <?php foreach ($tree as $comment): ?>
                <?= $this->renderCommentHTML($comment, $replies, $userId) ?>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        <?php
        $html = ob_get_clean();

        return $this->json([
            'success' => true,
            'html'    => $html,
            'count'   => count($comments),
        ]);
    }

    /**
     * POST /course/postComment (AJAX)
     * Gửi bình luận mới
     */
    public function postComment()
    {
        Middleware::requireLogin();
        if (!$this->isMethod('POST')) {
            return $this->json(['error' => 'Method not allowed'], 405);
        }

        $input    = json_decode(file_get_contents('php://input'), true);
        if (!is_array($input)) $input = [];
        if (!$input) $input = [];
        $lessonId = intval($input['lesson_id'] ?? 0);
        $content  = trim($input['content'] ?? '');
        $parentId = !empty($input['parent_id']) ? intval($input['parent_id']) : null;
        $userId   = $_SESSION['user_id'];

        if (!$lessonId || empty($content)) {
            return $this->json(['error' => 'Dữ liệu không hợp lệ'], 400);
        }

        if (strlen($content) > 2000) {
            return $this->json(['error' => 'Bình luận quá dài (tối đa 2000 ký tự)'], 400);
        }

        $db = getDB();
        $stmt = $db->prepare(
            'INSERT INTO lesson_discussions (lesson_id, user_id, parent_id, content) VALUES (:lid, :uid, :pid, :content)'
        );
        $stmt->execute([
            'lid'     => $lessonId,
            'uid'     => $userId,
            'pid'     => $parentId,
            'content' => $content,
        ]);

        $commentId = $db->lastInsertId();

        // Lấy thông tin user
        $stmt = $db->prepare('SELECT username, full_name, avatar FROM users WHERE id = :uid');
        $stmt->execute(['uid' => $userId]);
        $user = $stmt->fetch();

        return $this->json([
            'success'    => true,
            'comment_id' => $commentId,
            'username'   => $user['username'],
            'full_name'  => $user['full_name'],
            'content'    => $content,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Render 1 comment HTML (helper, gọi từ loadDiscussion)
     */
    private function renderCommentHTML(array $comment, array $replies, int $currentUserId): string
    {
        $name    = htmlspecialchars($comment['full_name'] ?? $comment['username']);
        $content = htmlspecialchars($comment['content']);
        $time    = date('d/m/Y H:i', strtotime($comment['created_at']));
        $cid     = $comment['id'];
        $isOwner = ($comment['user_id'] == $currentUserId);

        $html = <<<HTML
        <div class="discussion-comment" id="comment-{$cid}">
            <div class="discussion-avatar"><i class="fas fa-user-circle"></i></div>
            <div class="discussion-body">
                <div class="discussion-meta">
                    <strong>{$name}</strong>
                    <span>{$time}</span>
                </div>
                <p>{$content}</p>
                <button class="discussion-reply-btn" onclick="CoursePlayer.showReplyForm({$cid})">
                    <i class="fas fa-reply"></i> Trả lời
                </button>
                <div class="discussion-reply-form" id="replyForm-{$cid}" style="display:none">
                    <textarea class="discussion-input" placeholder="Viết trả lời..." rows="2"></textarea>
                    <button class="btn btn-primary btn-sm" onclick="CoursePlayer.postReply({$cid}, this)">Gửi</button>
                </div>
        HTML;

        // Replies
        if (isset($replies[$cid])) {
            $html .= '<div class="discussion-replies">';
            foreach ($replies[$cid] as $reply) {
                $html .= $this->renderCommentHTML($reply, $replies, $currentUserId);
            }
            $html .= '</div>';
        }

        $html .= '</div></div>';
        return $html;
    }

    /**
     * GET /course/certificate/{courseId}
     * Trang chứng chỉ sau khi hoàn thành khóa học
     */
    public function certificate($courseId = null)
    {
        Middleware::requireLogin();
        if (!$courseId) return $this->redirect('course');

        $userId      = $_SESSION['user_id'];
        $courseModel = $this->model('Course');
        $course      = $courseModel->find((int)$courseId);
        if (!$course) return $this->redirect('course');

        $progress = $courseModel->getUserProgress($userId, (int)$courseId);
        if (!$progress || !in_array($progress['status'], ['completed', 'mastered'])) {
            $this->setFlash('error', 'Bạn chưa hoàn thành khóa học này.');
            return $this->redirect('course');
        }

        $user = Middleware::user();

        $this->view('course/certificate', [
            'title'      => 'Chứng chỉ - ' . $course['title'],
            'course'     => $course,
            'progress'   => $progress,
            'user'       => $user,
        ]);
    }

    /**
     * GET /course/reviews/{courseId} (AJAX)
     * Lấy đánh giá của khóa học
     */
    public function reviews($courseId = null)
    {
        Middleware::requireLogin();
        if (!$courseId) return $this->json(['error' => 'Invalid course ID'], 400);

        $db  = getDB();
        $userId = $_SESSION['user_id'];

        // Tổng quan
        $stmt = $db->prepare(
            'SELECT COUNT(*) as total, ROUND(AVG(rating), 1) as avg_rating
             FROM course_reviews WHERE course_id = :cid'
        );
        $stmt->execute(['cid' => $courseId]);
        $stats = $stmt->fetch();

        // Review của user hiện tại
        $stmt = $db->prepare(
            'SELECT * FROM course_reviews WHERE user_id = :uid AND course_id = :cid'
        );
        $stmt->execute(['uid' => $userId, 'cid' => $courseId]);
        $myReview = $stmt->fetch();

        // Danh sách review gần đây
        $stmt = $db->prepare(
            'SELECT r.*, u.username, u.full_name, u.avatar
             FROM course_reviews r
             JOIN users u ON u.id = r.user_id
             WHERE r.course_id = :cid
             ORDER BY r.created_at DESC LIMIT 20'
        );
        $stmt->execute(['cid' => $courseId]);
        $reviews = $stmt->fetchAll();

        ob_start();
        ?>
        <div class="reviews-section">
            <div class="reviews-summary">
                <div class="reviews-score">
                    <span class="reviews-avg"><?= $stats['avg_rating'] ?: '0' ?></span>
                    <div class="reviews-stars"><?= $this->renderStars(round($stats['avg_rating'] ?? 0)) ?></div>
                    <span class="reviews-count"><?= $stats['total'] ?> đánh giá</span>
                </div>
            </div>

            <!-- My Review -->
            <div class="review-my" id="reviewMy">
                <h4>Đánh giá của tôi</h4>
                <div class="review-stars-input" id="reviewStarsInput">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                    <span class="star-input <?= $myReview && $myReview['rating'] >= $i ? 'active' : '' ?>"
                          data-star="<?= $i ?>" onclick="CoursePlayer.setRating(<?= $i ?>, <?= $courseId ?>)">
                        <i class="fas fa-star"></i>
                    </span>
                    <?php endfor; ?>
                </div>
                <textarea class="review-textarea" id="reviewText" placeholder="Chia sẻ trải nghiệm của bạn về khóa học này..."
                    oninput="CoursePlayer.autoSaveReview(<?= $courseId ?>)"><?= htmlspecialchars($myReview['review_text'] ?? '') ?></textarea>
                <button class="btn btn-primary btn-sm" onclick="CoursePlayer.submitReview(<?= $courseId ?>)">
                    <i class="fas fa-paper-plane"></i> Gửi đánh giá
                </button>
                <span class="review-saved" id="reviewSaved" style="display:none">Đã lưu!</span>
            </div>

            <!-- Reviews List -->
            <div class="reviews-list">
                <h4>Đánh giá từ học viên</h4>
                <?php if (empty($reviews)): ?>
                <p class="reviews-empty">Chưa có đánh giá nào.</p>
                <?php else: ?>
                <?php foreach ($reviews as $r): ?>
                <div class="review-card">
                    <div class="review-card-header">
                        <span class="review-user"><i class="fas fa-user-circle"></i> <?= htmlspecialchars($r['full_name'] ?? $r['username']) ?></span>
                        <span class="review-card-stars"><?= $this->renderStars($r['rating']) ?></span>
                        <span class="review-date"><?= date('d/m/Y', strtotime($r['created_at'])) ?></span>
                    </div>
                    <?php if (!empty($r['review_text'])): ?>
                    <p class="review-text"><?= htmlspecialchars($r['review_text']) ?></p>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        <?php
        $html = ob_get_clean();

        return $this->json([
            'success'    => true,
            'html'       => $html,
            'avg_rating' => $stats['avg_rating'] ?: 0,
            'total'      => (int)$stats['total'],
            'my_review'  => $myReview ?: null,
        ]);
    }

    /**
     * POST /course/submitReview (AJAX)
     */
    public function submitReview()
    {
        Middleware::requireLogin();
        if (!$this->isMethod('POST')) {
            return $this->json(['error' => 'Method not allowed'], 405);
        }

        $input    = json_decode(file_get_contents('php://input'), true);
        if (!is_array($input)) $input = [];
        $courseId = intval($input['course_id'] ?? 0);
        $rating   = intval($input['rating'] ?? 0);
        $text     = trim($input['review_text'] ?? '');
        $userId   = $_SESSION['user_id'];

        if (!$courseId || $rating < 1 || $rating > 5) {
            return $this->json(['error' => 'Dữ liệu không hợp lệ'], 400);
        }

        $db = getDB();
        $stmt = $db->prepare('SELECT id FROM course_reviews WHERE user_id = :uid AND course_id = :cid');
        $stmt->execute(['uid' => $userId, 'cid' => $courseId]);
        $existing = $stmt->fetch();

        if ($existing) {
            $db->prepare('UPDATE course_reviews SET rating = :r, review_text = :t WHERE id = :id')
               ->execute(['r' => $rating, 't' => $text, 'id' => $existing['id']]);
        } else {
            $db->prepare('INSERT INTO course_reviews (user_id, course_id, rating, review_text) VALUES (:uid, :cid, :r, :t)')
               ->execute(['uid' => $userId, 'cid' => $courseId, 'r' => $rating, 't' => $text]);
        }

        // Tính lại avg
        $stmt = $db->prepare('SELECT ROUND(AVG(rating), 1) as avg_rating, COUNT(*) as total FROM course_reviews WHERE course_id = :cid');
        $stmt->execute(['cid' => $courseId]);
        $stats = $stmt->fetch();

        return $this->json([
            'success'    => true,
            'avg_rating' => $stats['avg_rating'],
            'total'      => (int)$stats['total'],
        ]);
    }

    /**
     * Helper: render star HTML
     */
    protected function renderStars(int $rating): string
    {
        $html = '';
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $rating) {
                $html .= '<i class="fas fa-star"></i>';
            } else {
                $html .= '<i class="far fa-star"></i>';
            }
        }
        return $html;
    }

    // ─── Private helpers ────────────────────────────────────────────

    /**
     * Lấy danh sách lesson IDs đã hoàn thành của user trong 1 khóa
     */
    private function getCompletedLessonIds(int $userId, int $courseId): array
    {
        $stmt = getDB()->prepare(
            'SELECT lesson_id FROM lesson_progress WHERE user_id = :uid AND course_id = :cid'
        );
        $stmt->execute(['uid' => $userId, 'cid' => $courseId]);
        return array_column($stmt->fetchAll(), 'lesson_id');
    }

    /**
     * Lấy bài học xem gần đây nhất của user trong 1 khóa
     */
    private function getLastViewedLesson(int $userId, int $courseId): ?array
    {
        $stmt = getDB()->prepare(
            'SELECT l.id, l.title, lp.completed_at
             FROM lesson_progress lp
             JOIN lessons l ON l.id = lp.lesson_id
             WHERE lp.user_id = :uid AND lp.course_id = :cid
             ORDER BY lp.completed_at DESC LIMIT 1'
        );
        $stmt->execute(['uid' => $userId, 'cid' => $courseId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Lưu lesson progress vào DB
     */
    private function saveLessonProgress(int $userId, int $lessonId, ?int $courseId): void
    {
        if (!$courseId) return;

        $db = getDB();
        try {
            $stmt = $db->prepare(
                'INSERT IGNORE INTO lesson_progress (user_id, lesson_id, course_id) VALUES (:uid, :lid, :cid)'
            );
            $stmt->execute(['uid' => $userId, 'lid' => $lessonId, 'cid' => $courseId]);
        } catch (PDOException $e) {
            // Bỏ qua nếu trùng
        }
    }

    /**
     * Build danh sách phẳng tất cả lessons trong các chương (cho navigation)
     */
    private function buildLessonList(array $chapters): array
    {
        $list = [];
        foreach ($chapters as $chapter) {
            $stmt = getDB()->prepare(
                'SELECT id, title, estimated_minutes FROM lessons WHERE topic_id = :tid AND is_active = 1 ORDER BY sort_order'
            );
            $stmt->execute(['tid' => $chapter['id']]);
            $lessons = $stmt->fetchAll();
            foreach ($lessons as $l) {
                $l['chapter_name'] = $chapter['name'];
                $list[] = $l;
            }
        }
        return $list;
    }
}
