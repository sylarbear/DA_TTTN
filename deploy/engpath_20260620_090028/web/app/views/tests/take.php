<!-- Take Test Page -->
<section class="page-header">
    <div class="container">
        <nav class="breadcrumb">
            <a href="<?= BASE_URL ?>/test">Bài test</a>
            <span>/</span>
            <span><?= htmlspecialchars($test['title']) ?></span>
        </nav>
        <h1><?= htmlspecialchars($test['title']) ?></h1>
        <div class="test-info-bar">
            <span class="test-type type-<?= $test['test_type'] ?>">
                <?= ucfirst($test['test_type']) ?>
            </span>
            <span><i class="fas fa-list-ol"></i> <?= count($test['questions']) ?> câu</span>
            <span><i class="fas fa-clock"></i> <span id="timer"><?= $test['duration_minutes'] ?>:00</span></span>
        </div>
    </div>
</section>

<section class="test-taking">
    <div class="container">
        <form id="testForm">
            <input type="hidden" name="test_id" value="<?= $test['id'] ?>">
            
            <?php foreach ($test['questions'] as $i => $q): ?>
                <div class="question-card" id="question-<?= $i + 1 ?>">
                    <div class="question-header">
                        <span class="question-number">Câu <?= $i + 1 ?> / <?= count($test['questions']) ?></span>
                        <span class="question-points"><?= $q['points'] ?> điểm</span>
                    </div>

                    <!-- Passage cho Reading -->
                    <?php if (!empty($q['passage'])): ?>
                        <div class="reading-passage">
                            <h4><i class="fas fa-book-reader"></i> Đoạn văn:</h4>
                            <p><?= nl2br(htmlspecialchars($q['passage'])) ?></p>
                        </div>
                    <?php endif; ?>

                    <!-- Audio cho Listening -->
                    <?php if (!empty($q['audio_url'])): ?>
                        <div class="listening-audio">
                            <audio controls>
                                <source src="<?= htmlspecialchars($q['audio_url']) ?>" type="audio/mpeg">
                            </audio>
                        </div>
                    <?php endif; ?>

                    <div class="question-text">
                        <p><?= htmlspecialchars($q['question_text']) ?></p>
                    </div>

                    <div class="question-options">
                        <?php if ($q['question_type'] === 'multiple_choice' || $q['question_type'] === 'true_false'): ?>
                            <?php foreach ($q['options'] as $j => $option): ?>
                                <label class="option-label" id="option-<?= $q['id'] ?>-<?= $j ?>">
                                    <input type="radio" name="answer[<?= $q['id'] ?>]" value="<?= htmlspecialchars($option) ?>">
                                    <span class="option-text"><?= htmlspecialchars($option) ?></span>
                                </label>
                            <?php endforeach; ?>
                        <?php elseif ($q['question_type'] === 'fill_blank'): ?>
                            <div class="fill-blank-input">
                                <input type="text" name="answer[<?= $q['id'] ?>]" 
                                       placeholder="Nhập câu trả lời..." class="form-input"
                                       id="fill-<?= $q['id'] ?>">
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <!-- Question Navigator -->
            <div class="question-nav">
                <div class="nav-dots">
                    <?php for ($i = 1; $i <= count($test['questions']); $i++): ?>
                        <a href="#question-<?= $i ?>" class="nav-dot" id="dot-<?= $i ?>"><?= $i ?></a>
                    <?php endfor; ?>
                </div>
            </div>

            <div class="test-actions">
                <button type="button" class="btn btn-primary btn-lg" onclick="submitTest()" id="submitTestBtn">
                    <i class="fas fa-paper-plane"></i> Nộp bài
                </button>
            </div>
        </form>
    </div>
</section>

<!-- Result Modal -->
<div class="modal" id="resultModal">
    <div class="modal-overlay"></div>
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="resultTitle">Kết quả</h2>
        </div>
        <div class="modal-body" id="resultBody">
            <!-- Filled by JS -->
        </div>
        <div class="modal-footer">
            <a href="<?= BASE_URL ?>/test" class="btn btn-outline">Quay lại danh sách</a>
            <a href="#" class="btn btn-primary" id="viewDetailBtn">Xem chi tiết</a>
        </div>
    </div>
</div>

<script>
// Timer
let timeLeft = <?= $test['duration_minutes'] ?> * 60;
let startTime = Date.now();
let testSubmitted = false;

const timerInterval = setInterval(() => {
    timeLeft--;
    const min = Math.floor(timeLeft / 60);
    const sec = timeLeft % 60;
    document.getElementById('timer').textContent = 
        String(min).padStart(2, '0') + ':' + String(sec).padStart(2, '0');
    
    if (timeLeft <= 60) {
        document.getElementById('timer').style.color = '#ef4444';
    }
    if (timeLeft <= 0) {
        clearInterval(timerInterval);
        doSubmitTest(); // Auto-submit khi hết giờ, bỏ qua confirm
    }
}, 1000);

// Track answered questions
document.querySelectorAll('input[type="radio"], input[type="text"]').forEach(input => {
    const eventType = input.type === 'text' ? 'input' : 'change';
    input.addEventListener(eventType, function() {
        const qId = this.name.match(/\d+/)[0];
        const questions = <?= json_encode(array_column($test['questions'], 'id')) ?>;
        const idx = questions.indexOf(parseInt(qId)) + 1;
        const dot = document.getElementById('dot-' + idx);
        if (dot) dot.classList.add('answered');
    });
});

// Nút Nộp bài - hiện confirm modal thay vì native confirm()
function submitTest() {
    if (testSubmitted) return;
    
    // Hiển thị confirm modal
    const modal = document.getElementById('resultModal');
    const body = document.getElementById('resultBody');
    const title = document.getElementById('resultTitle');
    const viewBtn = document.getElementById('viewDetailBtn');
    
    title.textContent = 'Xác nhận nộp bài';
    body.innerHTML = `
        <div style="text-align:center; padding:1rem;">
            <i class="fas fa-question-circle fa-3x" style="color:var(--warning); margin-bottom:1rem;"></i>
            <p style="font-size:1.1rem; margin-bottom:0.5rem;">Bạn có chắc muốn nộp bài?</p>
            <p style="color:var(--text-muted);">Sau khi nộp bạn không thể sửa đổi câu trả lời.</p>
        </div>
    `;
    viewBtn.href = '#';
    viewBtn.textContent = 'Nộp bài';
    viewBtn.onclick = function(e) { e.preventDefault(); modal.classList.remove('active'); doSubmitTest(); };
    
    // Thêm nút Hủy
    const footer = modal.querySelector('.modal-footer');
    const cancelLink = footer.querySelector('.btn-outline');
    cancelLink.href = '#';
    cancelLink.textContent = 'Tiếp tục làm bài';
    cancelLink.onclick = function(e) { e.preventDefault(); modal.classList.remove('active'); };
    
    modal.classList.add('active');
}

// Thực hiện submit bài test
function doSubmitTest() {
    if (testSubmitted) return;
    testSubmitted = true;
    
    clearInterval(timerInterval);
    const btn = document.getElementById('submitTestBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang chấm điểm...';

    // Thu thập câu trả lời từ form
    const formData = new FormData(document.getElementById('testForm'));
    const answers = {};
    for (let [key, value] of formData.entries()) {
        const match = key.match(/answer\[(\d+)\]/);
        if (match) {
            answers[match[1]] = value;
        }
    }

    const timeSpent = Math.floor((Date.now() - startTime) / 1000);
    const submitUrl = '<?= BASE_URL ?>/test/submit';

    console.log('[English Learning] Submitting test...', {test_id: <?= $test['id'] ?>, answers, timeSpent, url: submitUrl});

    fetch(submitUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'same-origin',
        body: JSON.stringify({
            test_id: <?= $test['id'] ?>,
            answers: answers,
            time_spent: timeSpent
        })
    })
    .then(response => {
        console.log('[English Learning] Response status:', response.status);
        if (!response.ok) {
            throw new Error('Server responded with status ' + response.status);
        }
        return response.text();
    })
    .then(text => {
        console.log('[English Learning] Response body:', text);
        let data;
        try {
            data = JSON.parse(text);
        } catch(e) {
            console.error('[English Learning] Invalid JSON response:', text.substring(0, 200));
            throw new Error('Server trả về dữ liệu không hợp lệ.');
        }
        if (data.success) {
            showResult(data);
        } else {
            alert('Có lỗi xảy ra: ' + (data.error || 'Unknown'));
            resetButton();
        }
    })
    .catch(err => {
        console.error('[English Learning] Fetch error:', err);
        alert('Lỗi kết nối: ' + err.message + '\nVui lòng thử lại.');
        resetButton();
    });
}

function resetButton() {
    testSubmitted = false;
    const btn = document.getElementById('submitTestBtn');
    btn.disabled = false;
    btn.innerHTML = '<i class="fas fa-paper-plane"></i> Nộp bài';
}

function showResult(data) {
    const modal = document.getElementById('resultModal');
    const body = document.getElementById('resultBody');
    const title = document.getElementById('resultTitle');
    const passed = data.passed;

    title.textContent = 'Kết quả bài kiểm tra';
    body.innerHTML = `
        <div class="result-score ${passed ? 'passed' : 'failed'}">
            <div class="score-circle">
                <span class="score-number">${data.percentage}%</span>
                <span class="score-label">${data.score}/${data.total}</span>
            </div>
            <h3>${passed ? '🎉 Chúc mừng! Bạn đã đạt!' : '😔 Chưa đạt. Hãy cố gắng thêm!'}</h3>
        </div>
    `;

    // Cập nhật footer
    const footer = modal.querySelector('.modal-footer');
    footer.innerHTML = `
        <a href="<?= BASE_URL ?>/test" class="btn btn-outline">Quay lại danh sách</a>
        <a href="<?= BASE_URL ?>/test/result/${data.result_id}" class="btn btn-primary">Xem chi tiết</a>
    `;

    modal.classList.add('active');
}

// Đóng modal khi click overlay
document.querySelector('.modal-overlay').addEventListener('click', function() {
    document.getElementById('resultModal').classList.remove('active');
});
</script>

