<!-- Course Info Landing Page — Udemy/Coursera-style -->
<?php
$courseTitle = htmlspecialchars($course['title']);
$courseDesc  = htmlspecialchars($course['description'] ?? '');
$cefr        = $course['cefr_level'];
$hasLastLesson = !empty($lastLesson);
$learnUrl = BASE_URL . '/course/learn/' . $course['id'];
?>
<!-- Hero Banner -->
<section class="course-hero-banner">
    <div class="container">
        <a href="<?= BASE_URL ?>/course" class="back-link"><i class="fas fa-arrow-left"></i> Khóa học</a>
        <div style="display:flex; gap:2rem; align-items:flex-start; flex-wrap:wrap;">
            <div style="flex:1; min-width:280px;">
                <span class="course-level-badge cefr-<?= strtolower($cefr) ?>"><?= $cefr ?></span>
                <h1 style="color:#fff; font-size:2rem; font-weight:800; margin-top:0.5rem;"><?= $courseTitle ?></h1>
                <?php if (!empty($course['subtitle'])): ?>
                <p style="color:rgba(255,255,255,0.8); font-size:1rem;"><?= htmlspecialchars($course['subtitle']) ?></p>
                <?php endif; ?>
            </div>
            <div class="course-hero-sidebar" style="background:rgba(255,255,255,0.12); border-radius:8px; padding:1.5rem; min-width:220px; text-align:center;">
                <div style="color:#fff; font-size:0.85rem; margin-bottom:0.5rem;">
                    <i class="fas fa-clock"></i> <?= $courseOverview['total_hours'] ?> giờ · <?= $courseOverview['total_lessons'] ?> bài học
                </div>
                <a href="<?= $learnUrl ?>" class="btn btn-cta btn-lg" style="width:100%; margin-bottom:0.5rem;">
                    <?= $hasLastLesson ? 'Tiếp tục học' : 'Bắt đầu học' ?>
                </a>
                <div style="color:rgba(255,255,255,0.6); font-size:0.78rem;">Trình độ: <?= $cefr ?> — <?= $skillLevel ?></div>
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

<section class="course-landing-body">
    <div class="container" style="max-width: 900px; margin: 0 auto; padding: 32px 16px;">

        <?php if ($hasLastLesson): ?>
        <!-- Resume Banner -->
        <div class="resume-banner">
            <div class="resume-banner-content">
                <div class="resume-banner-info">
                    <i class="fas fa-play-circle"></i>
                    <span>Bạn đang học dở: <strong><?= htmlspecialchars($lastLesson['title']) ?></strong></span>
                </div>
                <div class="resume-banner-actions">
                    <a href="<?= $learnUrl ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-play"></i> Tiếp tục học
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- 1. Hero -->
        <div class="overview-hero">
            <span class="course-level-badge cefr-<?= strtolower($cefr) ?>" style="font-size: 14px; padding: 6px 16px;"><?= $cefr ?></span>
            <h2><?= $courseTitle ?></h2>
            <?php if (!empty($course['subtitle'])): ?>
            <p class="overview-subtitle"><?= htmlspecialchars($course['subtitle']) ?></p>
            <?php endif; ?>
            <p class="overview-desc"><?= $courseDesc ?></p>
        </div>

        <!-- 2. Stats Row -->
        <div class="overview-stats">
            <div class="overview-stat">
                <div class="stat-icon"><i class="fas fa-clock"></i></div>
                <strong><?= $courseOverview['total_hours'] ?></strong>
                <span>Tổng giờ</span>
            </div>
            <div class="overview-stat">
                <div class="stat-icon"><i class="fas fa-layer-group"></i></div>
                <strong><?= $courseOverview['total_chapters'] ?></strong>
                <span>Chương</span>
            </div>
            <div class="overview-stat">
                <div class="stat-icon"><i class="fas fa-book-open"></i></div>
                <strong><?= $courseOverview['total_lessons'] ?></strong>
                <span>Bài học</span>
            </div>
            <div class="overview-stat">
                <div class="stat-icon"><i class="fas fa-question-circle"></i></div>
                <strong><?= $courseOverview['total_quizzes'] ?></strong>
                <span>Quiz</span>
            </div>
            <div class="overview-stat">
                <div class="stat-icon"><i class="fas fa-spell-check"></i></div>
                <strong><?= $courseOverview['total_vocab'] ?></strong>
                <span>Từ vựng</span>
            </div>
        </div>

        <!-- 3. About Section -->
        <div class="overview-section">
            <h3><i class="fas fa-bullseye"></i> Về khóa học này</h3>
            <div class="about-description"><?= nl2br(htmlspecialchars($course['description'] ?? '')) ?></div>
            <?php if (!empty($objectives)): ?>
            <h4>Bạn sẽ học được gì</h4>
            <ul class="objectives-list">
                <?php foreach ($objectives as $obj): ?>
                <li><i class="fas fa-check-circle"></i> <?= htmlspecialchars($obj) ?></li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>
        </div>

        <!-- 4. Skills Section -->
        <div class="overview-section">
            <h3><i class="fas fa-chart-bar"></i> Kỹ năng bạn sẽ đạt được</h3>
            <div class="skills-grid">
                <?php foreach ($skills as $skill): ?>
                <div class="skill-bar-item">
                    <div class="skill-header">
                        <span class="skill-icon" style="color:<?= $skill['color'] ?>"><i class="fas <?= $skill['icon'] ?>"></i></span>
                        <span class="skill-name"><?= $skill['name'] ?></span>
                        <span class="skill-level-badge"><?= $skillLevel ?></span>
                    </div>
                    <div class="skill-progress-track">
                        <div class="skill-progress-fill" style="width:<?= $skill['weight'] ?>%; background:<?= $skill['color'] ?>"></div>
                    </div>
                    <span class="skill-percent"><?= $skill['weight'] ?>%</span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- 5. Requirements Section -->
        <div class="overview-section">
            <h3><i class="fas fa-clipboard-list"></i> Yêu cầu đầu vào</h3>
            <?php if (!empty($course['target_audience'])): ?>
            <div class="target-badge">
                <i class="fas fa-user-graduate"></i>
                <span>Đối tượng: <?= htmlspecialchars($course['target_audience']) ?></span>
            </div>
            <?php endif; ?>
            <?php if (!empty($requirements)): ?>
            <ul class="requirements-list">
                <?php foreach ($requirements as $req): ?>
                <li><i class="fas fa-check"></i> <?= htmlspecialchars($req) ?></li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>
            <?php if (empty($requirements) && empty($course['target_audience'])): ?>
            <p class="overview-empty">Không có yêu cầu đặc biệt. Chỉ cần tinh thần ham học hỏi!</p>
            <?php endif; ?>
        </div>

        <!-- 6. Syllabus -->
        <div class="overview-syllabus">
            <h3><i class="fas fa-list"></i> Lộ trình khóa học</h3>
            <?php foreach ($course['chapters'] as $wi => $chapter): ?>
            <div class="syllabus-item">
                <span class="syllabus-num"><?= $wi + 1 ?></span>
                <div class="syllabus-info">
                    <strong><?= htmlspecialchars($chapter['name']) ?></strong>
                    <span><?= $chapter['lesson_count'] ?> bài học · <?= $chapter['test_count'] ?> quiz · <?= $chapter['vocab_count'] ?> từ vựng</span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- 7. Assessment Section -->
        <div class="overview-section">
            <h3><i class="fas fa-clipboard-check"></i> Đánh giá kết quả</h3>
            <div class="assessment-cards">
                <div class="assessment-card">
                    <div class="assessment-icon middle-test-icon">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <div class="assessment-info">
                        <strong>Bài kiểm tra giữa chương</strong>
                        <p><?= $courseOverview['middle_tests'] ?> bài kiểm tra — 1 bài sau mỗi chương để củng cố kiến thức vừa học. Điểm đạt: 60% trở lên.</p>
                    </div>
                </div>
                <div class="assessment-card">
                    <div class="assessment-icon final-test-icon">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <div class="assessment-info">
                        <strong>Bài thi cuối khóa</strong>
                        <p>1 bài thi tổng hợp toàn bộ kiến thức khóa học<?php if ($finalExam): ?>. Thời gian: <?= $finalExam['duration_minutes'] ?? 30 ?> phút. Điểm đạt: <?= $finalExam['pass_score'] ?? 70 ?>% trở lên<?php endif; ?>.</p>
                    </div>
                </div>
            </div>
            <div class="assessment-note">
                <i class="fas fa-certificate"></i>
                <span>Hoàn thành tất cả bài kiểm tra với điểm đạt yêu cầu để nhận <strong>chứng chỉ hoàn thành khóa học</strong>.</span>
            </div>
        </div>

        <!-- 8. Start Button -->
        <div class="overview-start" style="text-align: center; margin-top: 32px;">
            <a href="<?= $learnUrl ?>" class="btn btn-primary btn-lg">
                <?php if ($hasLastLesson): ?>
                <i class="fas fa-play"></i> Tiếp tục học
                <?php else: ?>
                <i class="fas fa-rocket"></i> Bắt đầu học
                <?php endif; ?>
            </a>
        </div>

        <!-- 9. Reviews -->
        <div class="overview-reviews" id="overviewReviews" style="margin-top: 40px;">
            <button class="btn btn-outline" onclick="CoursePlayer.loadReviews(<?= $course['id'] ?>)">
                <i class="fas fa-star"></i> Xem đánh giá khóa học
            </button>
            <div id="reviewsContainer" style="display:none"></div>
        </div>

    </div>
</section>

<script src="<?= BASE_URL ?>/js/course.js?v=<?= APP_VERSION ?>"></script>
<link rel="stylesheet" href="<?= BASE_URL ?>/css/course.css?v=<?= APP_VERSION ?>">
