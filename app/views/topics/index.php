<?php
$levelLabels = [
    'beginner' => ['label' => 'Beginner', 'icon' => 'fa-seedling', 'desc' => 'Nền tảng và từ vựng quen thuộc'],
    'intermediate' => ['label' => 'Intermediate', 'icon' => 'fa-fire', 'desc' => 'Mở rộng phản xạ và ngữ cảnh'],
    'advanced' => ['label' => 'Advanced', 'icon' => 'fa-crown', 'desc' => 'Chủ đề dài và yêu cầu cao hơn'],
];

$visibleTopics = [];
foreach ($topics as $topic) {
    if ($level && $topic['level'] !== $level) {
        continue;
    }
    $visibleTopics[] = $topic;
}
?>

<section class="learn-page-hero course-catalog-hero">
    <div class="container learn-hero-grid">
        <div>
            <span class="busuu-label">Course catalog</span>
            <h1>Chọn lộ trình tiếng Anh phù hợp với mục tiêu của bạn.</h1>
            <p>Mỗi khóa học gom từ vựng, bài học, bài test và speaking vào một hành trình dễ theo dõi hơn.</p>
            <div class="learn-hero-actions">
                <a href="#course-list" class="busuu-primary-btn">Xem khóa học</a>
                <a href="<?= BASE_URL ?>/speaking" class="busuu-secondary-btn">Luyện speaking</a>
            </div>
        </div>

        <div class="catalog-preview-card">
            <div class="catalog-preview-top">
                <span><?= count($topics) ?> courses</span>
                <strong>Learning path</strong>
            </div>
            <div class="catalog-path">
                <div><i class="fas fa-font"></i><span>Vocabulary</span></div>
                <div><i class="fas fa-book-open"></i><span>Lessons</span></div>
                <div><i class="fas fa-clipboard-check"></i><span>Tests</span></div>
                <div><i class="fas fa-microphone"></i><span>Speaking</span></div>
            </div>
        </div>
    </div>
</section>

<section class="course-filter-section">
    <div class="container">
        <div class="level-filter-grid">
            <a href="<?= BASE_URL ?>/topic" class="level-filter-card <?= empty($level) ? 'active' : '' ?>">
                <i class="fas fa-layer-group"></i>
                <strong>Tất cả</strong>
                <span><?= count($topics) ?> khóa học</span>
            </a>
            <?php foreach ($levelLabels as $key => $info): ?>
                <?php
                    $count = count(array_filter($topics, function ($t) use ($key) {
                        return ($t['level'] ?? '') === $key;
                    }));
                ?>
                <a href="<?= BASE_URL ?>/topic?level=<?= $key ?>" class="level-filter-card <?= $level === $key ? 'active' : '' ?>">
                    <i class="fas <?= $info['icon'] ?>"></i>
                    <strong><?= $info['label'] ?></strong>
                    <span><?= $count ?> khóa học · <?= $info['desc'] ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="topics-section modern-course-section" id="course-list">
    <div class="container">
        <div class="test-group-heading">
            <div>
                <span class="busuu-label">Available courses</span>
                <h2><?= count($visibleTopics) ?> khóa học đang hiển thị</h2>
            </div>
            <a href="<?= BASE_URL ?>/test" class="busuu-secondary-btn">Làm bài test</a>
        </div>

        <div class="modern-course-grid">
            <?php foreach ($visibleTopics as $index => $topic): ?>
                <?php
                    $level = $topic['level'] ?? 'beginner';
                $progressSeed = min(86, 28 + (($index * 13) % 52));
                ?>
                <a href="<?= BASE_URL ?>/topic/show/<?= $topic['id'] ?>" class="modern-course-card level-<?= htmlspecialchars($level) ?>" id="topic-<?= $topic['id'] ?>">
                    <div class="course-card-cover">
                        <div class="course-card-icon"><i class="fas <?= $levelLabels[$level]['icon'] ?? 'fa-book-open' ?>"></i></div>
                        <span class="topic-level level-<?= htmlspecialchars($level) ?>"><?= ucfirst(htmlspecialchars($level)) ?></span>
                    </div>
                    <div class="course-card-content">
                        <h3><?= htmlspecialchars($topic['name']) ?></h3>
                        <p><?= htmlspecialchars($topic['description']) ?></p>
                        <div class="course-card-progress">
                            <span style="width:<?= $progressSeed ?>%"></span>
                        </div>
                        <div class="course-meta-row">
                            <span><i class="fas fa-font"></i> <?= (int)$topic['vocab_count'] ?> từ</span>
                            <span><i class="fas fa-book"></i> <?= (int)$topic['lesson_count'] ?> bài</span>
                            <span><i class="fas fa-clipboard-check"></i> <?= (int)$topic['test_count'] ?> test</span>
                            <span><i class="fas fa-microphone"></i> <?= (int)$topic['speaking_count'] ?> speaking</span>
                        </div>
                    </div>
                    <div class="course-card-action">
                        <span>Bắt đầu học</span>
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
