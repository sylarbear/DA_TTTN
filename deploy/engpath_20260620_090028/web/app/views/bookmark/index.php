<!-- Bookmarked Words Page -->
<section class="page-header">
    <div class="container">
        <h1><i class="fas fa-bookmark"></i> Từ vựng đã lưu</h1>
        <p><?= count($bookmarks) ?> từ đã đánh dấu</p>
    </div>
</section>

<section class="dashboard-section">
    <div class="container">
        <?php if (empty($bookmarks)): ?>
            <div class="empty-state">
                <i class="fas fa-bookmark"></i>
                <p>Chưa có từ vựng nào được lưu.</p>
                <p style="color:var(--text-muted);">Nhấn biểu tượng ⭐ trên từ vựng để lưu lại.</p>
                <a href="<?= BASE_URL ?>/topic" class="btn btn-primary" style="margin-top:1rem;">Xem chủ đề</a>
            </div>
        <?php else: ?>
            <div class="vocab-grid">
                <?php foreach ($bookmarks as $b): ?>
                    <div class="vocab-card bookmark-card" id="bm-<?= $b['vocabulary_id'] ?>">
                        <div class="vocab-word">
                            <h3><?= htmlspecialchars($b['word']) ?></h3>
                            <span class="vocab-pronunciation"><?= htmlspecialchars($b['pronunciation']) ?></span>
                            <button class="btn-speak" onclick="speakWord('<?= htmlspecialchars($b['word']) ?>')" title="Phát âm"><i class="fas fa-volume-up"></i></button>
                        </div>
                        <div class="vocab-meaning">
                            <p class="meaning-vi"><strong>Nghĩa:</strong> <?= htmlspecialchars($b['meaning_vi']) ?></p>
                            <?php if ($b['example_sentence']): ?>
                                <p class="vocab-example"><em><?= htmlspecialchars($b['example_sentence']) ?></em></p>
                            <?php endif; ?>
                            <small style="color:var(--text-muted);"><i class="fas fa-book"></i> <?= htmlspecialchars($b['topic_name']) ?></small>
                        </div>
                        <?php if ($b['note']): ?>
                            <div style="margin-top:0.5rem; padding:0.5rem; background:var(--bg-surface); border-radius:var(--radius-sm); font-size:0.85rem;">
                                <i class="fas fa-sticky-note" style="color:var(--accent-orange);"></i> <?= htmlspecialchars($b['note']) ?>
                            </div>
                        <?php endif; ?>
                        <div style="display:flex; gap:0.5rem; margin-top:0.5rem;">
                            <button class="btn btn-sm btn-outline" onclick="removeBookmark(<?= $b['vocabulary_id'] ?>)"><i class="fas fa-trash"></i> Xóa</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<script>
function speakWord(word) {
    const u = new SpeechSynthesisUtterance(word);
    u.lang = 'en-US'; u.rate = 0.8;
    speechSynthesis.speak(u);
}
function removeBookmark(vocabId) {
    fetch('<?= BASE_URL ?>/bookmark/toggle', {
        method:'POST', headers:{'Content-Type':'application/json'}, credentials:'same-origin',
        body: JSON.stringify({ vocabulary_id: vocabId })
    }).then(r=>r.json()).then(d => {
        if (d.success) document.getElementById('bm-' + vocabId).remove();
    });
}
</script>
