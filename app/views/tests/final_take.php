<!-- Final Exam Take — 2 tabs Reading/Listening -->
<section class="page-header">
    <div class="container">
        <nav class="breadcrumb">
            <a href="<?= BASE_URL ?>/course">Khóa học</a>
            <span>/</span>
            <span><?= htmlspecialchars($test['title']) ?></span>
        </nav>
        <h1><?= htmlspecialchars($test['title']) ?></h1>
        <p>Bài thi gồm 2 phần: Reading (10 câu) và Listening (10 câu). Cần đạt tối thiểu 70%.</p>
    </div>
</section>

<section class="final-exam">
    <div class="container">
        <!-- Section Tabs -->
        <div class="final-tabs">
            <button class="final-tab active" onclick="switchSection('reading')" id="tab-reading">
                <i class="fas fa-book-reader"></i> Reading (50 điểm)
            </button>
            <button class="final-tab" onclick="switchSection('listening')" id="tab-listening">
                <i class="fas fa-headphones"></i> Listening (50 điểm)
            </button>
            <div class="final-timer">
                <i class="fas fa-clock"></i> <span id="timer"><?= $test['duration_minutes'] ?>:00</span>
            </div>
        </div>

        <form id="finalForm">
            <input type="hidden" name="test_id" value="<?= $test['id'] ?>">

            <!-- READING Section -->
            <div class="final-section active" id="section-reading">
                <?php if (!empty($test['reading_passage'])): ?>
                <div class="final-reading-passage">
                    <h3><i class="fas fa-book"></i> Đọc đoạn văn sau và trả lời câu hỏi</h3>
                    <div class="passage-text"><?= nl2br(htmlspecialchars($test['reading_passage'])) ?></div>
                </div>
                <?php endif; ?>

                <?php foreach ($readingQuestions as $i => $q): ?>
                <div class="question-card" id="rq-<?= $i ?>">
                    <div class="question-header">
                        <span class="question-number">Câu <?= $i + 1 ?></span>
                        <span class="question-points"><?= $q['points'] ?> điểm</span>
                    </div>
                    <div class="question-text"><p><?= htmlspecialchars($q['question_text']) ?></p></div>
                    <div class="question-options">
                        <?php foreach ($q['options'] as $j => $opt): ?>
                        <label class="option-label">
                            <input type="radio" name="answer[<?= $q['id'] ?>]" value="<?= htmlspecialchars($opt) ?>">
                            <span class="option-letter"><?= chr(65 + $j) ?></span>
                            <span class="option-text"><?= htmlspecialchars($opt) ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- LISTENING Section -->
            <div class="final-section" id="section-listening">
                <?php if (!empty($test['listening_transcript'])): ?>
                <div class="final-listening-player">
                    <button type="button" class="btn btn-primary btn-lg" onclick="playListening()" id="playBtn">
                        <i class="fas fa-play"></i> Nghe đoạn văn
                    </button>
                    <button type="button" class="btn btn-outline btn-sm" onclick="stopListening()" style="display:none" id="stopBtn">
                        <i class="fas fa-stop"></i> Dừng
                    </button>
                    <span class="listen-hint">Bạn có thể nghe lại nhiều lần</span>
                </div>
                <?php endif; ?>

                <?php foreach ($listeningQuestions as $i => $q): ?>
                <div class="question-card" id="lq-<?= $i ?>">
                    <div class="question-header">
                        <span class="question-number">Câu <?= $i + 11 ?></span>
                        <span class="question-points"><?= $q['points'] ?> điểm</span>
                    </div>
                    <div class="question-text"><p><?= htmlspecialchars($q['question_text']) ?></p></div>
                    <div class="question-options">
                        <?php foreach ($q['options'] as $j => $opt): ?>
                        <label class="option-label">
                            <input type="radio" name="answer[<?= $q['id'] ?>]" value="<?= htmlspecialchars($opt) ?>">
                            <span class="option-letter"><?= chr(65 + $j) ?></span>
                            <span class="option-text"><?= htmlspecialchars($opt) ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </form>

        <!-- Submit -->
        <div class="final-actions">
            <button type="button" class="btn btn-primary btn-lg" onclick="submitFinal()" id="submitBtn">
                <i class="fas fa-paper-plane"></i> Nộp bài
            </button>
        </div>

        <!-- Result Modal -->
        <div class="modal" id="finalResultModal">
            <div class="modal-overlay"></div>
            <div class="modal-content">
                <div class="modal-header"><h2 id="finalResultTitle">Kết quả</h2></div>
                <div class="modal-body" id="finalResultBody"></div>
                <div class="modal-footer">
                    <a href="<?= BASE_URL ?>/course" class="btn btn-primary" id="btnBackCourse">Về khóa học</a>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
let currentSection = 'reading';
let timerSeconds = <?= ($test['duration_minutes'] ?? 30) * 60 ?>;
let examStarted = false;

function switchSection(section) {
    document.querySelectorAll('.final-section').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('.final-tab').forEach(t => t.classList.remove('active'));
    document.getElementById('section-' + section).classList.add('active');
    document.getElementById('tab-' + section).classList.add('active');
    currentSection = section;
}

// Timer
function startTimer() {
    if (examStarted) return;
    examStarted = true;
    const timer = document.getElementById('timer');
    const interval = setInterval(() => {
        timerSeconds--;
        const m = Math.floor(timerSeconds / 60);
        const s = timerSeconds % 60;
        timer.textContent = m + ':' + (s < 10 ? '0' : '') + s;
        if (timerSeconds <= 0) {
            clearInterval(interval);
            submitFinal();
        }
    }, 1000);
}

// Listening TTS
let listeningUtterance = null;
function playListening() {
    const text = <?= json_encode($test['listening_transcript'] ?? '') ?>;
    if (!text || typeof SpeechSynthesisUtterance === 'undefined') return;

    stopListening();
    document.getElementById('playBtn').style.display = 'none';
    document.getElementById('stopBtn').style.display = 'inline-block';

    listeningUtterance = new SpeechSynthesisUtterance(text);
    listeningUtterance.lang = 'en-US';
    listeningUtterance.rate = 0.85;
    listeningUtterance.onend = () => {
        document.getElementById('playBtn').style.display = 'inline-block';
        document.getElementById('stopBtn').style.display = 'none';
    };
    window.speechSynthesis.speak(listeningUtterance);
}

function stopListening() {
    window.speechSynthesis.cancel();
    document.getElementById('playBtn').style.display = 'inline-block';
    document.getElementById('stopBtn').style.display = 'none';
}

// Submit
async function submitFinal() {
    if (!confirm('Bạn có chắc muốn nộp bài?')) return;
    stopListening();
    document.getElementById('submitBtn').disabled = true;

    const answers = {};
    document.querySelectorAll('#finalForm input[type=radio]:checked').forEach(r => {
        const name = r.name.match(/\[(\d+)\]/);
        if (name) answers[name[1]] = r.value;
    });

    try {
        const res = await fetch('<?= BASE_URL ?>/test/finalSubmit', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            credentials: 'same-origin',
            body: JSON.stringify({
                test_id: <?= $test['id'] ?>,
                answers: answers,
                time_spent: <?= $test['duration_minutes'] * 60 ?> - timerSeconds,
            }),
        });
        const data = await res.json();

        let html = '<div class="final-score-overview">';
        html += '<div class="final-total-score">' + data.percentage + '%</div>';
        html += '<p>' + (data.passed ? 'Chúc mừng! Bạn đã đạt.' : 'Chưa đạt. Cần tối thiểu 70%.') + '</p>';
        html += '</div>';

        html += '<div class="final-section-scores">';
        html += '<div class="section-score"><span>Reading</span><strong>' + data.reading_score + '/' + data.reading_total + '</strong></div>';
        html += '<div class="section-score"><span>Listening</span><strong>' + data.listening_score + '/' + data.listening_total + '</strong></div>';
        html += '</div>';

        if (data.certificate) {
            html += '<div class="final-cert-banner">' + data.certificate + '!</div>';
        }
        if (data.unlocked_course) {
            html += '<p>Khóa tiếp theo đã mở: <strong>' + data.unlocked_course + '</strong></p>';
        }

        document.getElementById('finalResultBody').innerHTML = html;
        document.getElementById('finalResultTitle').textContent = data.passed ? 'Đạt!' : 'Chưa đạt';
        document.getElementById('finalResultModal').style.display = 'block';
    } catch (err) {
        alert('Có lỗi xảy ra. Vui lòng thử lại.');
        document.getElementById('submitBtn').disabled = false;
    }
}

// Start timer on first interaction
document.addEventListener('click', startTimer, { once: true });
</script>

<link rel="stylesheet" href="<?= BASE_URL ?>/css/course.css">
