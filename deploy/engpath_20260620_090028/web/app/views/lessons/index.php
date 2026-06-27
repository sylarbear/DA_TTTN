<!-- Lessons List Page -->
<section class="page-header">
    <div class="container">
        <nav class="breadcrumb">
            <a href="<?= BASE_URL ?>/topic">Chủ đề</a>
            <span>/</span>
            <a href="<?= BASE_URL ?>/topic/show/<?= $topic['id'] ?>"><?= htmlspecialchars($topic['name']) ?></a>
            <span>/</span>
            <span>Bài học</span>
        </nav>
        <h1><i class="fas fa-book"></i> Bài học: <?= htmlspecialchars($topic['name']) ?></h1>
    </div>
</section>

<section class="lessons-section">
    <div class="container">
        <div class="lessons-list">
            <?php if (empty($lessons)): ?>
                <div class="empty-state">
                    <i class="fas fa-book-open"></i>
                    <p>Chưa có bài học nào cho chủ đề này.</p>
                </div>
            <?php else: ?>
                <?php foreach ($lessons as $i => $lesson): ?>
                    <a href="<?= BASE_URL ?>/lesson/show/<?= $lesson['id'] ?>" class="lesson-item">
                        <div class="lesson-number"><?= $i + 1 ?></div>
                        <div class="lesson-info">
                            <h3><?= htmlspecialchars($lesson['title']) ?></h3>
                            <p><?= htmlspecialchars($lesson['description']) ?></p>
                        </div>
                        <div class="lesson-arrow"><i class="fas fa-chevron-right"></i></div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>
