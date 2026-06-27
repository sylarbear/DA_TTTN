<section class="course-detail-hero">
    <div class="container course-detail-grid">
        <div>
            <nav class="breadcrumb">
                <a href="<?= BASE_URL ?>/topic">Khóa học</a>
                <span>/</span>
                <span><?= htmlspecialchars($topic['name']) ?></span>
            </nav>
            <span class="topic-level level-<?= htmlspecialchars($topic['level']) ?>"><?= ucfirst(htmlspecialchars($topic['level'])) ?></span>
            <h1><?= htmlspecialchars($topic['name']) ?></h1>
            <p><?= htmlspecialchars($topic['description']) ?></p>
            <div class="course-hero-actions">
                <a href="<?= BASE_URL ?>/topic/flashcard/<?= $topic['id'] ?>" class="btn btn-primary"><i class="fas fa-clone"></i> Ôn flashcard</a>
                <a href="#tab-lessons" class="btn btn-outline" onclick="showTab('lessons')"><i class="fas fa-book"></i> Xem bài học</a>
            </div>
        </div>

        <div class="course-summary-card">
            <h3>Tiến độ khóa học</h3>
            <?php
                $vocabDone = $progress ? min($progress['vocab_learned'], $topic['vocab_count']) : 0;
                $lessonDone = $progress ? min($progress['lessons_completed'], $topic['lesson_count']) : 0;
                $testDone = $progress ? min($progress['tests_passed'], $topic['test_count']) : 0;
                ?>
            <div class="summary-row"><span>Từ vựng</span><strong><?= $vocabDone ?> / <?= (int)$topic['vocab_count'] ?></strong></div>
            <div class="summary-row"><span>Bài học</span><strong><?= $lessonDone ?> / <?= (int)$topic['lesson_count'] ?></strong></div>
            <div class="summary-row"><span>Bài test</span><strong><?= $testDone ?> / <?= (int)$topic['test_count'] ?></strong></div>
            <div class="summary-score">
                <small>Tổng điểm</small>
                <strong><?= $progress ? (int)$progress['total_score'] : 0 ?></strong>
            </div>
        </div>
    </div>
</section>

<section class="topic-content">
    <div class="container">
        <div class="tab-nav modern-tabs">
            <button class="tab-btn active" onclick="showTab('vocab')"><i class="fas fa-font"></i> Từ vựng (<?= count($vocabularies) ?>)</button>
            <button class="tab-btn" onclick="showTab('lessons')"><i class="fas fa-book"></i> Bài học (<?= count($lessons) ?>)</button>
            <a href="<?= BASE_URL ?>/test" class="btn btn-outline btn-sm"><i class="fas fa-clipboard-check"></i> Làm test</a>
        </div>

        <div class="tab-content active" id="tab-vocab">
            <div class="vocab-grid">
                <?php foreach ($vocabularies as $vocab): ?>
                    <div class="vocab-card" id="vocab-<?= $vocab['id'] ?>">
                        <div class="vocab-word">
                            <div>
                                <h3><?= htmlspecialchars($vocab['word']) ?></h3>
                                <span class="vocab-pronunciation"><?= htmlspecialchars($vocab['pronunciation']) ?></span>
                            </div>
                            <button class="btn-speak" onclick="speakWord('<?= htmlspecialchars($vocab['word']) ?>')" title="Phát âm">
                                <i class="fas fa-volume-up"></i>
                            </button>
                        </div>
                        <div class="vocab-meaning">
                            <p class="meaning-vi"><strong>Nghĩa:</strong> <?= htmlspecialchars($vocab['meaning_vi']) ?></p>
                            <?php if ($vocab['example_sentence']): ?>
                                <p class="vocab-example"><strong>Ví dụ:</strong> <em><?= htmlspecialchars($vocab['example_sentence']) ?></em></p>
                            <?php endif; ?>
                        </div>
                        <?php if (Middleware::isLoggedIn()): ?>
                            <button class="btn btn-sm btn-success btn-learn"
                                    onclick="markLearned(this, <?= $topic['id'] ?>)"
                                    data-vocab-id="<?= $vocab['id'] ?>">
                                <i class="fas fa-check"></i> Đã học
                            </button>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="tab-content" id="tab-lessons">
            <div class="lessons-list">
                <?php foreach ($lessons as $i => $lesson): ?>
                    <a href="<?= BASE_URL ?>/lesson/show/<?= $lesson['id'] ?>" class="lesson-item" id="lesson-<?= $lesson['id'] ?>">
                        <div class="lesson-number"><?= $i + 1 ?></div>
                        <div class="lesson-info">
                            <h3><?= htmlspecialchars($lesson['title']) ?></h3>
                            <p><?= htmlspecialchars($lesson['description']) ?></p>
                        </div>
                        <div class="lesson-arrow"><i class="fas fa-chevron-right"></i></div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<script>
function showTab(tab) {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
    const button = event && event.target ? event.target.closest('.tab-btn') : null;
    if (button) button.classList.add('active');
    document.getElementById('tab-' + tab).classList.add('active');
}

function speakWord(word) {
    speechSynthesis.cancel();
    const utterance = new SpeechSynthesisUtterance(word);
    utterance.lang = 'en-US';
    utterance.rate = 0.85;
    speechSynthesis.speak(utterance);
}

function markLearned(btn, topicId) {
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang lưu...';

    fetch('<?= BASE_URL ?>/topic/learnVocab', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'same-origin',
        body: JSON.stringify({topic_id: topicId})
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            btn.innerHTML = '<i class="fas fa-check-double"></i> Đã ghi nhận';
            btn.classList.add('learned');
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check"></i> Đã học';
    });
}
</script>
