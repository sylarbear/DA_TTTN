<!-- Grammar Lessons Index -->
<section class="page-header">
    <div class="container">
        <h1><i class="fas fa-graduation-cap"></i> Ngữ pháp tiếng Anh</h1>
        <p>Học ngữ pháp qua bài giảng ngắn gọn + quiz thực hành</p>
    </div>
</section>

<section class="dashboard-section">
    <div class="container">
        <?php
        $categoryNames = [
            'tense' => ['🕐 Thì (Tenses)', 'Các thì trong tiếng Anh'],
            'preposition' => ['📍 Giới từ (Prepositions)', 'Cách dùng giới từ'],
            'clause' => ['📝 Mệnh đề (Clauses)', 'Mệnh đề quan hệ, điều kiện'],
            'article' => ['📰 Mạo từ (Articles)', 'A, An, The'],
            'modal' => ['🔧 Động từ khuyết thiếu', 'Can, Could, Should, Must...'],
            'other' => ['📚 Khác', 'Các chủ đề ngữ pháp khác'],
        ];
        ?>
        <?php foreach ($grouped as $cat => $catLessons): ?>
            <div class="section-card" style="margin-bottom:1.5rem;">
                <h3><?= $categoryNames[$cat][0] ?? $cat ?></h3>
                <p style="color:var(--text-muted); margin-bottom:1rem;"><?= $categoryNames[$cat][1] ?? '' ?></p>
                <div class="grammar-grid">
                    <?php foreach ($catLessons as $l): ?>
                        <a href="<?= BASE_URL ?>/grammar/show/<?= $l['id'] ?>" class="grammar-card">
                            <div class="grammar-card-header">
                                <h4><?= htmlspecialchars($l['title']) ?></h4>
                                <span class="topic-level level-<?= $l['level'] ?>"><?= ucfirst($l['level']) ?></span>
                            </div>
                            <div class="grammar-card-footer">
                                <span><i class="fas fa-question-circle"></i> <?= $l['question_count'] ?> câu quiz</span>
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <?php if (empty($grouped)): ?>
            <div class="empty-state"><i class="fas fa-book"></i><p>Chưa có bài ngữ pháp nào.</p></div>
        <?php endif; ?>
    </div>
</section>
