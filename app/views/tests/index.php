<?php
$typeIcons = ['quiz' => 'fa-question-circle', 'listening' => 'fa-headphones', 'reading' => 'fa-book-reader'];
$typeLabels = ['quiz' => 'Quiz', 'listening' => 'Listening', 'reading' => 'Reading'];
$typeDescriptions = [
    'quiz' => 'Ôn nhanh từ vựng và kiến thức trong từng chủ đề.',
    'listening' => 'Luyện nghe hiểu và chọn đáp án chính xác.',
    'reading' => 'Đọc đoạn văn, phân tích ý chính và trả lời câu hỏi.'
];
$groupedTests = [];
foreach ($tests ?? [] as $test) {
    $groupedTests[$test['test_type']][] = $test;
}
?>

<section class="learn-page-hero test-center-hero">
    <div class="container learn-hero-grid">
        <div>
            <span class="busuu-label">Test center</span>
            <h1>Kiểm tra nhanh để biết bạn đang tiến bộ ở đâu.</h1>
            <p>Chọn Quiz, Listening hoặc Reading theo chủ đề. Mỗi bài test được trình bày rõ thời gian, số câu và mức điểm cần đạt.</p>
            <div class="learn-hero-actions">
                <a href="#test-list" class="busuu-primary-btn">Xem bài test</a>
                <a href="<?= BASE_URL ?>/dashboard" class="busuu-secondary-btn">Xem tiến độ</a>
            </div>
        </div>
        <div class="test-hero-card">
            <div class="test-score-preview">
                <span>Pass score</span>
                <strong>60%</strong>
            </div>
            <div class="test-preview-list">
                <div><i class="fas fa-question-circle"></i><span>Quiz theo chủ đề</span></div>
                <div><i class="fas fa-headphones"></i><span>Listening practice</span></div>
                <div><i class="fas fa-book-reader"></i><span>Reading check</span></div>
            </div>
        </div>
    </div>
</section>

<section class="test-type-strip">
    <div class="container test-type-grid">
        <?php foreach (['quiz', 'listening', 'reading'] as $type): ?>
            <a href="#type-<?= $type ?>" class="test-type-card type-<?= $type ?>">
                <i class="fas <?= $typeIcons[$type] ?>"></i>
                <strong><?= $typeLabels[$type] ?></strong>
                <span><?= count($groupedTests[$type] ?? []) ?> bài</span>
            </a>
        <?php endforeach; ?>
    </div>
</section>

<section class="tests-section modern-tests" id="test-list">
    <div class="container">
        <?php if (empty($tests)): ?>
            <div class="empty-state">
                <i class="fas fa-clipboard"></i>
                <p>Chưa có bài test nào.</p>
            </div>
        <?php else: ?>
            <?php foreach (['quiz', 'listening', 'reading'] as $type): ?>
                <?php if (empty($groupedTests[$type])) continue; ?>
                <div class="test-group" id="type-<?= $type ?>">
                    <div class="test-group-heading">
                        <div>
                            <span class="busuu-label"><?= $typeLabels[$type] ?></span>
                            <h2><?= $typeDescriptions[$type] ?></h2>
                        </div>
                        <span><?= count($groupedTests[$type]) ?> bài test</span>
                    </div>

                    <div class="tests-grid modern-test-grid">
                        <?php foreach ($groupedTests[$type] as $test): ?>
                            <article class="test-card modern-test-card" id="test-<?= $test['id'] ?>">
                                <div class="test-card-header">
                                    <span class="test-type type-<?= htmlspecialchars($test['test_type']) ?>">
                                        <i class="fas <?= $typeIcons[$test['test_type']] ?? 'fa-clipboard-check' ?>"></i>
                                        <?= $typeLabels[$test['test_type']] ?? ucfirst($test['test_type']) ?>
                                    </span>
                                    <span class="test-topic"><?= htmlspecialchars($test['topic_name']) ?></span>
                                </div>
                                <div class="test-card-body">
                                    <h3><?= htmlspecialchars($test['title']) ?></h3>
                                    <div class="test-meta modern-test-meta">
                                        <span><i class="fas fa-list-ol"></i><b><?= (int) $test['question_count'] ?></b> câu</span>
                                        <span><i class="fas fa-clock"></i><b><?= (int) $test['duration_minutes'] ?></b> phút</span>
                                        <span><i class="fas fa-trophy"></i><b><?= (int) $test['pass_score'] ?>%</b> đạt</span>
                                    </div>
                                </div>
                                <div class="test-card-footer">
                                    <?php if (Middleware::isLoggedIn()): ?>
                                        <a href="<?= BASE_URL ?>/test/take/<?= $test['id'] ?>" class="busuu-primary-btn test-start-btn">
                                            Làm bài <i class="fas fa-arrow-right"></i>
                                        </a>
                                    <?php else: ?>
                                        <a href="<?= BASE_URL ?>/auth/login" class="busuu-secondary-btn test-start-btn">
                                            Đăng nhập để làm bài
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>
