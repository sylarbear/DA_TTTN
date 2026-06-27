<!-- Flashcard Page -->
<section class="page-header">
    <div class="container">
        <div class="breadcrumb"><a href="<?= BASE_URL ?>">Trang chủ</a> <span>›</span> <a href="<?= BASE_URL ?>/topic">Chủ đề</a> <span>›</span> <a href="<?= BASE_URL ?>/topic/show/<?= $topic['id'] ?>"><?= htmlspecialchars($topic['name']) ?></a> <span>›</span> Flashcard</div>
        <h1><i class="fas fa-clone"></i> Flashcard: <?= htmlspecialchars($topic['name']) ?></h1>
        <p><?= count($vocabularies) ?> từ vựng</p>
    </div>
</section>

<section class="dashboard-section">
    <div class="container">
        <?php if (empty($vocabularies)): ?>
            <div class="empty-state"><i class="fas fa-inbox"></i><p>Chủ đề này chưa có từ vựng</p></div>
        <?php else: ?>
            <div class="flashcard-container">
                <!-- Progress -->
                <div class="flashcard-progress">
                    <span id="progressText">1 / <?= count($vocabularies) ?></span>
                    <div class="flashcard-progress-bar"><div class="flashcard-progress-fill" id="progressFill" style="width:0%"></div></div>
                </div>

                <!-- Card -->
                <div class="flashcard" id="flashcard" onclick="flipCard()">
                    <div class="flashcard-inner">
                        <div class="flashcard-front">
                            <p style="color:var(--text-muted); margin-bottom:0.5rem; font-size:0.85rem;">Click để lật thẻ</p>
                            <h2 id="cardWord"></h2>
                            <p class="pronunciation" id="cardPronunciation"></p>
                            <button class="btn btn-sm btn-outline" style="margin-top:1rem;" onclick="event.stopPropagation(); speakWord()"><i class="fas fa-volume-up"></i> Nghe</button>
                        </div>
                        <div class="flashcard-back">
                            <h3 id="cardMeaning"></h3>
                            <p style="color:var(--text-secondary); font-size:0.95rem;" id="cardExample"></p>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flashcard-actions">
                    <button class="btn-dont-know" onclick="markCard(false)"><i class="fas fa-times"></i> Chưa nhớ</button>
                    <button class="btn btn-outline" onclick="flipCard()" style="padding:0.8rem 1.5rem;"><i class="fas fa-sync-alt"></i> Lật</button>
                    <button class="btn-know" onclick="markCard(true)"><i class="fas fa-check"></i> Đã nhớ</button>
                </div>

                <!-- Result (hidden) -->
                <div id="flashcardResult" style="display:none; text-align:center; width:100%; max-width:500px;">
                    <div class="section-card" style="padding:2rem;">
                        <div style="font-size:4rem; margin-bottom:1rem;">🎉</div>
                        <h2>Hoàn thành!</h2>
                        <p style="color:var(--text-secondary); margin:1rem 0;">
                            Đã nhớ: <strong style="color:var(--success);" id="knownCount">0</strong> · 
                            Chưa nhớ: <strong style="color:var(--error);" id="unknownCount">0</strong>
                        </p>
                        <div style="display:flex; gap:1rem; justify-content:center;">
                            <button class="btn btn-outline" onclick="restartAll()"><i class="fas fa-redo"></i> Ôn lại tất cả</button>
                            <button class="btn btn-primary" onclick="restartUnknown()"><i class="fas fa-sync"></i> Ôn từ chưa nhớ</button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php if (!empty($vocabularies)): ?>
<script>
const vocabs = <?= json_encode(array_values($vocabularies), JSON_UNESCAPED_UNICODE) ?>;
let deck = [...vocabs], currentIndex = 0, known = 0, unknown = 0;
let unknownWords = [];

function loadCard() {
    if (currentIndex >= deck.length) { showResult(); return; }
    const v = deck[currentIndex];
    document.getElementById('cardWord').textContent = v.word;
    document.getElementById('cardPronunciation').textContent = v.pronunciation || '';
    document.getElementById('cardMeaning').textContent = v.meaning_vi || v.meaning;
    document.getElementById('cardExample').textContent = v.example_sentence || '';
    document.getElementById('flashcard').classList.remove('flipped');
    document.getElementById('progressText').textContent = (currentIndex + 1) + ' / ' + deck.length;
    document.getElementById('progressFill').style.width = ((currentIndex / deck.length) * 100) + '%';
}

function flipCard() { document.getElementById('flashcard').classList.toggle('flipped'); }

function markCard(isKnown) {
    if (isKnown) {
        known++;
    } else {
        unknown++;
        unknownWords.push(deck[currentIndex]);
    }
    currentIndex++;
    loadCard();
}

function speakWord() {
    speechSynthesis.cancel();
    const utterance = new SpeechSynthesisUtterance(deck[currentIndex].word);
    utterance.lang = 'en-US';
    speechSynthesis.speak(utterance);
}

function showResult() {
    document.getElementById('flashcard').style.display = 'none';
    document.querySelector('.flashcard-actions').style.display = 'none';
    document.getElementById('flashcardResult').style.display = 'block';
    document.getElementById('knownCount').textContent = known;
    document.getElementById('unknownCount').textContent = unknown;
    document.getElementById('progressFill').style.width = '100%';
    
    // Award XP for completing flashcard session
    fetch('<?= BASE_URL ?>/topic/learnVocab', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        credentials: 'same-origin',
        body: JSON.stringify({topic_id: <?= $topic['id'] ?>})
    }).catch(() => {});
}

function restartAll() {
    deck = [...vocabs]; currentIndex = 0; known = 0; unknown = 0; unknownWords = [];
    document.getElementById('flashcard').style.display = ''; document.querySelector('.flashcard-actions').style.display = '';
    document.getElementById('flashcardResult').style.display = 'none';
    loadCard();
}

function restartUnknown() {
    if (unknownWords.length === 0) { alert('Bạn đã nhớ hết từ vựng! 🎉'); return; }
    deck = [...unknownWords]; currentIndex = 0; known = 0; unknown = 0; unknownWords = [];
    document.getElementById('flashcard').style.display = ''; document.querySelector('.flashcard-actions').style.display = '';
    document.getElementById('flashcardResult').style.display = 'none';
    loadCard();
}

loadCard();
</script>
<?php endif; ?>
