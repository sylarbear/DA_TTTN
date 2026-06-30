<!-- Placement Result Page -->
<?php
$cefrLabels = [
    'A1' => 'Sơ cấp 1',
    'A2' => 'Sơ cấp 2',
    'B1' => 'Trung cấp 1',
    'B2' => 'Trung cấp 2',
    'C1' => 'Nâng cao',
];

$cefrDescriptions = [
    'A1' => 'Bạn có thể hiểu và sử dụng các cụm từ cơ bản hàng ngày.',
    'A2' => 'Bạn có thể giao tiếp trong các tình huống đơn giản, quen thuộc.',
    'B1' => 'Bạn có thể xử lý hầu hết các tình huống khi đi du lịch và thảo luận về các chủ đề quen thuộc.',
    'B2' => 'Bạn có thể giao tiếp tự nhiên với người bản xứ và hiểu các văn bản phức tạp.',
    'C1' => 'Bạn có thể sử dụng tiếng Anh linh hoạt và hiệu quả trong mọi tình huống.',
];

$cefr = $result['final_cefr'] ?? 'A1';
$label = $cefrLabels[$cefr] ?? $cefr;
$desc = $cefrDescriptions[$cefr] ?? '';
$accuracy = $result['questions_answered'] > 0
    ? round($result['correct_count'] / $result['questions_answered'] * 100)
    : 0;
$confidence = ($result['confidence_score'] ?? 0) * 100;
?>
<section class="placement-result">
    <div class="placement-result-card">
        <!-- CEFR Badge -->
        <div class="placement-badge-wrapper">
            <div class="placement-badge cefr-<?= strtolower($cefr) ?>">
                <span class="placement-badge-level"><?= $cefr ?></span>
                <span class="placement-badge-label"><?= $label ?></span>
            </div>
        </div>

        <h1>Trình độ của bạn: <?= $cefr ?> — <?= $label ?></h1>
        <p class="placement-result-desc"><?= $desc ?></p>

        <!-- Stats -->
        <div class="placement-stats">
            <div class="placement-stat-item">
                <span class="placement-stat-value"><?= $result['questions_answered'] ?></span>
                <span class="placement-stat-label">câu đã trả lời</span>
            </div>
            <div class="placement-stat-item">
                <span class="placement-stat-value"><?= $accuracy ?>%</span>
                <span class="placement-stat-label">chính xác</span>
            </div>
            <div class="placement-stat-item">
                <span class="placement-stat-value"><?= round($confidence) ?>%</span>
                <span class="placement-stat-label">độ tin cậy</span>
            </div>
        </div>

        <!-- App Level & XP -->
        <div class="placement-xp-info">
            <div class="placement-xp-badge">
                <i class="fas fa-level-up-alt"></i>
                <span>Cấp độ <?= $appLevel ?></span>
            </div>
            <?php
            $xpMapping = ['A1' => 0, 'A2' => 100, 'B1' => 300, 'B2' => 500, 'C1' => 800];
            $xpEarned = $xpMapping[$cefr] ?? 0;
            $userXp = $user['total_xp'] ?? 0;
            $xpToAdd = max(0, $xpEarned - $userXp);
            ?>
            <?php if ($xpToAdd > 0): ?>
            <div class="placement-xp-badge xp-earned">
                <i class="fas fa-star"></i>
                <span>+<?= number_format($xpToAdd) ?> XP</span>
            </div>
            <?php endif; ?>
        </div>

        <!-- Recommended Topics -->
        <?php if (!empty($recommendedTopics)): ?>
        <div class="placement-recommendations">
            <h3>Chủ đề gợi ý cho bạn</h3>
            <div class="placement-topics-grid">
                <?php foreach ($recommendedTopics as $topic): ?>
                <a href="<?= BASE_URL ?>/topic/<?= $topic['id'] ?>" class="placement-topic-card">
                    <?php if (!empty($topic['thumbnail'])): ?>
                    <img src="<?= htmlspecialchars($topic['thumbnail']) ?>" alt="<?= htmlspecialchars($topic['name']) ?>">
                    <?php else: ?>
                    <div class="placement-topic-placeholder">
                        <i class="fas fa-book"></i>
                    </div>
                    <?php endif; ?>
                    <span><?= htmlspecialchars($topic['name']) ?></span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Actions -->
        <div class="placement-actions">
            <a href="<?= BASE_URL ?>" class="btn btn-primary btn-lg">
                <i class="fas fa-play"></i> Bắt đầu học
            </a>
            <a href="<?= BASE_URL ?>/placement/intro" class="btn btn-outline btn-lg">
                <i class="fas fa-redo"></i> Làm lại
            </a>
        </div>
    </div>
</section>

<link rel="stylesheet" href="<?= BASE_URL ?>/css/placement.css">
