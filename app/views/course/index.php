<!-- Course Catalog — Coursera-style 3-group layout -->
<section class="page-header">
    <div class="container">
        <h1>Khóa học của tôi</h1>
        <p>Lộ trình học tập được cá nhân hóa dựa trên trình độ của bạn.</p>
    </div>
</section>

<section class="course-catalog">
    <div class="container">
        <?php if (empty($active) && empty($locked) && empty($mastered)): ?>
            <div class="course-empty">
                <i class="fas fa-clipboard-check"></i>
                <h3>Bạn chưa xác định trình độ</h3>
                <p>Hãy làm bài kiểm tra đầu vào để nhận lộ trình học phù hợp.</p>
                <a href="<?= BASE_URL ?>/placement/intro" class="btn btn-primary">Làm bài kiểm tra</a>
            </div>
        <?php endif; ?>

        <!-- Active Courses -->
        <?php if (!empty($active)): ?>
        <div class="course-group">
            <h2 class="course-group-title"><i class="fas fa-play-circle"></i> Đang học</h2>
            <div class="course-grid">
                <?php foreach ($active as $c): ?>
                <a href="<?= BASE_URL ?>/course/show/<?= $c['id'] ?>" class="course-card"
                   style="border-left: 4px solid <?= $c['status'] === 'in_progress' ? '#4f46e5' : '#22c55e' ?>">
                    <div class="course-card-badge cefr-<?= strtolower($c['cefr_level']) ?>">
                        <?= $c['cefr_level'] ?>
                    </div>
                    <h3><?= htmlspecialchars($c['title']) ?></h3>
                    <p><?= htmlspecialchars($c['description'] ?? '') ?></p>
                    <div class="course-progress-mini">
                        <div class="course-progress-bar">
                            <div class="course-progress-fill" style="width: <?= $c['completion_percent'] ?? 0 ?>%"></div>
                        </div>
                        <span><?= $c['completion_percent'] ?? 0 ?>%</span>
                    </div>
                    <div class="course-card-footer">
                        <?php if (!empty($c['last_lesson'])): ?>
                        <span class="course-card-resume">
                            <i class="fas fa-history"></i> <?= htmlspecialchars($c['last_lesson']['title']) ?>
                        </span>
                        <?php endif; ?>
                        <span class="course-card-status <?= $c['status'] === 'in_progress' ? 'status-active' : 'status-new' ?>">
                            <?php if (!empty($c['last_lesson'])): ?>
                                <i class="fas fa-play"></i> Tiếp tục học
                            <?php elseif ($c['status'] === 'in_progress'): ?>
                                Đang học
                            <?php else: ?>
                                <i class="fas fa-play"></i> Bắt đầu
                            <?php endif; ?>
                        </span>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Locked Courses -->
        <?php if (!empty($locked)): ?>
        <div class="course-group">
            <h2 class="course-group-title"><i class="fas fa-lock"></i> Sẽ mở sau</h2>
            <div class="course-grid">
                <?php foreach ($locked as $c): ?>
                <div class="course-card course-locked">
                    <div class="course-lock-icon"><i class="fas fa-lock"></i></div>
                    <div class="course-card-badge cefr-<?= strtolower($c['cefr_level']) ?>">
                        <?= $c['cefr_level'] ?>
                    </div>
                    <h3><?= htmlspecialchars($c['title']) ?></h3>
                    <p><?= htmlspecialchars($c['description'] ?? '') ?></p>
                    <span class="course-locked-text">Hoàn thành khóa trước để mở</span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Mastered Courses -->
        <?php if (!empty($mastered)): ?>
        <div class="course-group course-mastered-section">
            <h2 class="course-group-title"><i class="fas fa-check-circle"></i> Đã mastered</h2>
            <div class="course-grid">
                <?php foreach ($mastered as $c): ?>
                <a href="<?= BASE_URL ?>/course/show/<?= $c['id'] ?>" class="course-card course-mastered-card">
                    <div class="course-mastered-check"><i class="fas fa-check-circle"></i></div>
                    <div class="course-card-badge cefr-<?= strtolower($c['cefr_level']) ?> mastered-badge">
                        <?= $c['cefr_level'] ?>
                    </div>
                    <h3><?= htmlspecialchars($c['title']) ?></h3>
                    <p><?= htmlspecialchars($c['description'] ?? '') ?></p>
                    <div class="course-card-footer">
                        <span class="course-mastered-text">Đã hoàn thành</span>
                        <a href="<?= BASE_URL ?>/course/certificate/<?= $c['id'] ?>" class="cert-link" onclick="event.stopPropagation()">
                            <i class="fas fa-certificate"></i> Xem chứng chỉ
                        </a>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<link rel="stylesheet" href="<?= BASE_URL ?>/css/course.css?v=<?= time() ?>">
