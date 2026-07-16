<!-- Course Detail — Coursera-style 2-panel layout with enhanced UX -->
<?php
$courseTitle = htmlspecialchars($course['title']);
$courseDesc  = htmlspecialchars($course['description'] ?? '');
$cefr        = $course['cefr_level'];
$hasLastLesson = !empty($lastLesson);
$completedMap  = array_fill_keys($completedLessonIds, true);
?>
<section class="course-learn-header">
    <div class="container">
        <a href="<?= BASE_URL ?>/course" class="back-link"><i class="fas fa-arrow-left"></i> Quay lại khóa học</a>
        <div class="course-learn-title-row">
            <div>
                <span class="course-level-badge cefr-<?= strtolower($cefr) ?>"><?= $cefr ?></span>
                <h1><?= $courseTitle ?></h1>
            </div>
            <div class="course-learn-progress">
                <div class="mini-ring" id="progressRing">
                    <svg viewBox="0 0 36 36" width="56" height="56">
                        <circle cx="18" cy="18" r="15" fill="none" stroke="#e2e8f0" stroke-width="3"/>
                        <circle cx="18" cy="18" r="15" fill="none" stroke="#4f46e5" stroke-width="3"
                            stroke-dasharray="0, 100" stroke-linecap="round"
                            transform="rotate(-90 18 18)" id="progressCircle"/>
                    </svg>
                    <span id="progressPercent">0%</span>
                </div>
            </div>
        </div>
    </div>
</section>

<?php if (!empty($isReview)): ?>
<section class="course-review-banner">
    <div class="container">
        <div class="review-banner-content">
            <i class="fas fa-info-circle"></i>
            <span>Trình độ của bạn đã vượt qua cấp độ này. Bạn có thể ôn tập lại các bài học bên dưới.</span>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="course-learn-body">
    <div class="container course-learn-container">
        <!-- SIDEBAR — Curriculum -->
        <aside class="course-sidebar" id="courseSidebar">
            <div class="sidebar-search">
                <i class="fas fa-search" aria-hidden="true"></i>
                <label for="courseSearch" class="sr-only">Tìm trong khóa học</label>
                <input type="text" id="courseSearch" placeholder="Tìm trong khóa học..." autocomplete="off">
            </div>

            <h2>Nội dung khóa học</h2>

            <?php foreach ($course['chapters'] as $wi => $chapter): ?>
            <?php
                $weekNum       = $wi + 1;
                $weekCompleted = $chapter['is_completed'];
                $isFirstIncomplete = !$weekCompleted && ($wi == 0 || $course['chapters'][$wi - 1]['is_completed']);

                // Get lessons for this chapter
                $stmt = getDB()->prepare('SELECT id, title, estimated_minutes FROM lessons WHERE topic_id = :tid AND is_active = 1 ORDER BY sort_order');
                $stmt->execute(['tid' => $chapter['id']]);
                $chapterLessons = $stmt->fetchAll();

                // Get quizzes for this chapter
                $stmt = getDB()->prepare('SELECT id, title FROM tests WHERE topic_id = :tid AND is_active = 1 AND is_final = 0 ORDER BY id');
                $stmt->execute(['tid' => $chapter['id']]);
                $chapterQuizzes = $stmt->fetchAll();

                // Count completed lessons in this chapter
                $chapCompletedCount = 0;
                foreach ($chapterLessons as $cl) {
                    if (isset($completedMap[$cl['id']])) $chapCompletedCount++;
                }
                $chapTotalItems = count($chapterLessons) + count($chapterQuizzes);
                $chapProgressPct = $chapTotalItems > 0 ? round($chapCompletedCount / max(count($chapterLessons), 1) * 100) : 0;
            ?>
            <div class="sidebar-week <?= $weekCompleted ? 'done' : '' ?> <?= $isFirstIncomplete ? 'current' : '' ?>" data-week="<?= $weekNum ?>">
                <div class="sidebar-week-header" onclick="CoursePlayer.toggleWeek(this)">
                    <div class="week-indicator">
                        <?php if ($weekCompleted): ?><i class="fas fa-check-circle"></i>
                        <?php elseif ($isFirstIncomplete): ?><span><?= $weekNum ?></span>
                        <?php else: ?><i class="fas fa-lock"></i><?php endif; ?>
                    </div>
                    <div class="week-info">
                        <strong>Chương <?= $weekNum ?>: <?= htmlspecialchars($chapter['name']) ?></strong>
                        <small><?= $chapter['lesson_count'] ?> bài học · <?= $chapter['test_count'] ?> quiz</small>
                        <div class="week-mini-progress">
                            <div class="week-mini-bar"><div class="week-mini-fill" style="width: <?= $chapProgressPct ?>%"></div></div>
                        </div>
                    </div>
                </div>
                <div class="sidebar-week-items" style="<?= $isFirstIncomplete || $weekCompleted ? '' : 'display:none' ?>">
                    <?php foreach ($chapterLessons as $li => $lesson):
                        $isDone = isset($completedMap[$lesson['id']]);
                        $duration = !empty($lesson['estimated_minutes']) ? $lesson['estimated_minutes'] . ' phút' : '';
                    ?>
                    <div class="sidebar-item <?= $isDone ? 'done' : '' ?>" data-type="lesson" data-id="<?= $lesson['id'] ?>" data-topic="<?= $chapter['id'] ?>" data-title="<?= htmlspecialchars($lesson['title']) ?>">
                        <div class="sidebar-item-icon"><?= $isDone ? '<i class="fas fa-check-circle"></i>' : '<i class="fas fa-play-circle"></i>' ?></div>
                        <div class="sidebar-item-text">
                            <span><?= htmlspecialchars($lesson['title']) ?></span>
                            <small><?= $duration ?: 'Lý thuyết' ?></small>
                        </div>
                    </div>
                    <?php endforeach; ?>

                    <?php foreach ($chapterQuizzes as $quiz):
                        $quizDone = false;
                        $stmt2 = getDB()->prepare('SELECT tests_passed FROM user_progress WHERE user_id = :uid AND topic_id = :tid');
                        $stmt2->execute(['uid' => $_SESSION['user_id'], 'tid' => $chapter['id']]);
                        $tp = $stmt2->fetch();
                        $quizDone = ($tp['tests_passed'] ?? 0) > 0;
                    ?>
                    <div class="sidebar-item quiz-item <?= $quizDone ? 'done' : '' ?>" data-type="quiz" data-id="<?= $quiz['id'] ?>" data-topic="<?= $chapter['id'] ?>" data-title="<?= htmlspecialchars($quiz['title']) ?>">
                        <div class="sidebar-item-icon"><?= $quizDone ? '<i class="fas fa-check-circle"></i>' : '<i class="fas fa-question-circle"></i>' ?></div>
                        <div class="sidebar-item-text">
                            <span><?= htmlspecialchars($quiz['title']) ?></span>
                            <small>Quiz</small>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>

            <!-- Final Exam Item -->
            <div class="sidebar-week sidebar-final <?= $canTakeFinal ? 'current' : '' ?>">
                <div class="sidebar-week-header final-exam-header" onclick="<?= $canTakeFinal ? "location.href='" . BASE_URL . "/test/finalTake/" . ($finalExam['id'] ?? 0) . "'" : '' ?>">
                    <div class="week-indicator final-indicator">
                        <?php if ($finalResult && ($finalResult['score']/$finalResult['total_points']*100) >= 70): ?>
                            <i class="fas fa-trophy"></i>
                        <?php elseif ($canTakeFinal): ?>
                            <i class="fas fa-star"></i>
                        <?php else: ?>
                            <i class="fas fa-lock"></i>
                        <?php endif; ?>
                    </div>
                    <div class="week-info">
                        <strong>Bài thi cuối khóa</strong>
                        <small><?= $finalExam['question_count'] ?? 0 ?> câu · <?= $finalExam['duration_minutes'] ?? 0 ?> phút</small>
                    </div>
                </div>
            </div>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="course-content" id="courseContent">
            <?php if ($hasLastLesson): ?>
            <!-- Resume Banner -->
            <div class="resume-banner">
                <div class="resume-banner-content">
                    <div class="resume-banner-info">
                        <i class="fas fa-play-circle"></i>
                        <span>Bạn đang học dở: <strong><?= htmlspecialchars($lastLesson['title']) ?></strong></span>
                    </div>
                    <div class="resume-banner-actions">
                        <a href="#" class="btn btn-primary btn-sm" onclick="CoursePlayer.loadLesson(<?= $lastLesson['id'] ?>); return false;">
                            <i class="fas fa-play"></i> Tiếp tục
                        </a>
                        <a href="<?= BASE_URL ?>/course/show/<?= $course['id'] ?>" class="btn btn-outline btn-sm">
                            <i class="fas fa-info-circle"></i> Xem thông tin khóa học
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Dynamic Content Display (lesson/quiz loaded here) -->
            <div id="contentDisplay"></div>

            <!-- Loading State -->
            <div class="content-loading" id="contentLoading" style="display:none">
                <div class="loading-spinner"></div>
                <p>Đang tải bài học...</p>
            </div>
        </main>
    </div>
</section>

<!-- Lesson Navigation Bar (fixed bottom) -->
<div class="lesson-nav-bar" id="lessonNavBar" style="display:none">
    <div class="lesson-nav-container">
        <button class="nav-btn nav-prev" id="prevLessonBtn" onclick="CoursePlayer.navigateLesson('prev')" disabled>
            <i class="fas fa-arrow-left"></i> Bài trước
        </button>
        <div class="nav-center">
            <button class="nav-btn nav-complete" id="navCompleteBtn" onclick="CoursePlayer.toggleCurrentComplete()">
                <span class="nav-complete-icon"><i class="fas fa-check-circle"></i></span>
                <span>Đánh dấu hoàn thành</span>
            </button>
        </div>
        <button class="nav-btn nav-next" id="nextLessonBtn" onclick="CoursePlayer.navigateLesson('next')" disabled>
            Bài tiếp theo <i class="fas fa-arrow-right"></i>
        </button>
    </div>
</div>

<!-- Mobile sidebar toggle -->
<button class="sidebar-toggle-mobile" id="sidebarToggle" onclick="CoursePlayer.toggleMobileSidebar()">
    <i class="fas fa-list"></i> Nội dung
</button>

<!-- Toast container -->
<div class="toast-container" id="toastContainer"></div>

<script>
// Pass server data to JS
window.COURSE_DATA = {
    courseId: <?= $course['id'] ?>,
    completedLessonIds: <?= json_encode(array_values($completedLessonIds)) ?>,
    completionPercent: <?= $completionPercent ?>,
    baseUrl: '<?= BASE_URL ?>',
    lessonList: <?= json_encode($lessonList) ?>,
    hasLastLesson: <?= $hasLastLesson ? 'true' : 'false' ?>,
    lastLessonId: <?= $lastLesson ? $lastLesson['id'] : 'null' ?>,
};
</script>
<script src="<?= BASE_URL ?>/js/course.js?v=<?= APP_VERSION ?>"></script>
<link rel="stylesheet" href="<?= BASE_URL ?>/css/course.css?v=<?= APP_VERSION ?>">