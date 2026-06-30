<!-- Placement Take Page — One question at a time, Duolingo-style -->
<section class="placement-take" id="placementApp">
    <!-- Progress bar -->
    <div class="placement-progress-bar">
        <div class="placement-progress-fill" id="progressFill" style="width: 0%"></div>
    </div>

    <div class="placement-container" id="questionContainer">
        <?php if ($question): ?>
        <div class="placement-question-card" id="questionCard">
            <!-- Skill indicator -->
            <div class="placement-skill-tag skill-<?= $question['skill_type'] ?>">
                <?php
                $skillLabels = [
                    'vocabulary' => 'Từ vựng',
                    'grammar'    => 'Ngữ pháp',
                    'reading'    => 'Đọc hiểu',
                    'listening'  => 'Nghe',
                ];
                ?>
                <i class="fas fa-<?= $question['skill_type'] === 'listening' ? 'headphones' : ($question['skill_type'] === 'reading' ? 'book-reader' : 'pencil-alt') ?>"></i>
                <?= $skillLabels[$question['skill_type']] ?? 'Từ vựng' ?>
            </div>

            <!-- Reading passage (only show for reading, not listening) -->
            <?php if ($question['skill_type'] === 'reading' && !empty($question['passage'])): ?>
            <div class="placement-passage">
                <p><?= nl2br(htmlspecialchars($question['passage'])) ?></p>
            </div>
            <?php endif; ?>

            <!-- Audio player for listening -->
            <?php if ($question['skill_type'] === 'listening'): ?>
            <div class="placement-audio">
                <?php if (!empty($question['audio_url'])): ?>
                <audio controls id="audioPlayer">
                    <source src="<?= htmlspecialchars($question['audio_url']) ?>" type="audio/mpeg">
                </audio>
                <?php else: ?>
                <button type="button" class="btn btn-outline btn-sm" id="listenBtn" onclick="speakQuestion('<?= htmlspecialchars(addslashes($question['passage'] ?? $question['question_text'])) ?>')">
                    <i class="fas fa-volume-up"></i> Nghe câu hỏi
                </button>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <!-- Question text -->
            <div class="placement-question-text">
                <p><?= htmlspecialchars($question['question_text']) ?></p>
            </div>

            <!-- Answer options -->
            <div class="placement-answer-options" id="answerOptions">
                <?php if ($question['question_type'] === 'fill_blank'): ?>
                <div class="placement-fill-blank">
                    <input type="text" id="fillInput" class="form-input placement-input"
                           placeholder="Nhập câu trả lời..." autocomplete="off">
                    <button type="button" class="btn btn-primary btn-lg" id="submitFill" onclick="submitFillBlank()">
                        Kiểm tra
                    </button>
                </div>
                <?php else: ?>
                <?php foreach ($question['options'] as $idx => $option): ?>
                <button type="button" class="placement-option-btn"
                        data-value="<?= htmlspecialchars($option) ?>"
                        data-index="<?= $idx ?>"
                        onclick="selectOption(this, '<?= htmlspecialchars(addslashes($option)) ?>')">
                    <span class="option-letter"><?= chr(65 + $idx) ?></span>
                    <span class="option-label"><?= htmlspecialchars($option) ?></span>
                </button>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        <?php else: ?>
        <div class="placement-empty">
            <p>Không có câu hỏi nào. Vui lòng thử lại sau.</p>
            <a href="<?= BASE_URL ?>" class="btn btn-primary">Quay về trang chủ</a>
        </div>
        <?php endif; ?>
    </div>
</section>

<script>
// ============================================
// Placement Test Data
// ============================================
const SESSION_ID = <?= $session['id'] ?? 0 ?>;
const TOTAL_QUESTIONS = <?= $session['total_questions'] ?? 15 ?>;
let questionsAnswered = <?= count($question ? [$question] : []) > 0 ? 0 : 999 ?>;
let currentQuestionId = <?= $question['id'] ?? 0 ?>;
let questionStartTime = Date.now();
let isAnswering = false;

// ============================================
// Answer Submission
// ============================================
function selectOption(btn, value) {
    if (isAnswering) return;
    isAnswering = true;
    window.speechSynthesis.cancel();

    const responseTimeMs = Date.now() - questionStartTime;

    // Highlight selected
    btn.classList.add('selected');

    // Submit answer
    submitAnswer(value, responseTimeMs);
}

function submitFillBlank() {
    if (isAnswering) return;
    const input = document.getElementById('fillInput');
    const value = input.value.trim();
    if (!value) return;
    isAnswering = true;

    const responseTimeMs = Date.now() - questionStartTime;
    submitAnswer(value, responseTimeMs);
}

async function submitAnswer(answer, responseTimeMs) {
    try {
        const res = await fetch('<?= BASE_URL ?>/placement/next', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            credentials: 'same-origin',
            body: JSON.stringify({
                session_id: SESSION_ID,
                question_id: currentQuestionId,
                answer: answer,
                response_time_ms: responseTimeMs,
            }),
        });

        const data = await res.json();
        if (data.error) {
            alert(data.error);
            isAnswering = false;
            return;
        }

        // Show feedback
        showFeedback(data.feedback, answer);

        // Update progress
        updateProgress(data.progress, data.questionsAnswered);

        if (data.done) {
            // Redirect to result after short delay
            setTimeout(() => {
                window.location.href = data.redirect || '<?= BASE_URL ?>/placement/result';
            }, 2000);
        } else {
            // Load next question after delay
            setTimeout(() => {
                if (data.nextQuestion) {
                    renderNextQuestion(data.nextQuestion, data.questionsAnswered);
                }
                isAnswering = false;
            }, 1500);
        }
    } catch (err) {
        console.error('Placement submit error:', err);
        isAnswering = false;
    }
}

// ============================================
// Feedback Display
// ============================================
function showFeedback(feedback, userAnswer) {
    const options = document.querySelectorAll('.placement-option-btn');
    const correctAnswer = feedback.correct_answer;
    const isCorrect = feedback.is_correct;

    // Disable all options
    options.forEach(btn => {
        btn.disabled = true;
        const val = btn.getAttribute('data-value');
        if (val === correctAnswer) {
            btn.classList.add('correct');
        } else if (val === userAnswer && !isCorrect) {
            btn.classList.add('wrong');
        }
    });

    // Show explanation if available
    if (feedback.explanation) {
        const explanationEl = document.createElement('div');
        explanationEl.className = 'placement-explanation ' + (isCorrect ? 'is-correct' : 'is-wrong');
        explanationEl.innerHTML = '<i class="fas fa-' + (isCorrect ? 'check-circle' : 'info-circle') + '"></i> ' + feedback.explanation;
        document.getElementById('answerOptions').appendChild(explanationEl);
    }

    // Show correct answer highlight for fill_blank
    const fillInput = document.getElementById('fillInput');
    if (fillInput) {
        fillInput.disabled = true;
        fillInput.classList.add(isCorrect ? 'input-correct' : 'input-wrong');
        const submitBtn = document.getElementById('submitFill');
        if (submitBtn) submitBtn.style.display = 'none';
        if (!isCorrect && !feedback.explanation) {
            const correctEl = document.createElement('div');
            correctEl.className = 'placement-explanation is-wrong';
            correctEl.innerHTML = '<i class="fas fa-info-circle"></i> Đáp án đúng: <strong>' + correctAnswer + '</strong>';
            document.getElementById('answerOptions').appendChild(correctEl);
        }
    }
}

// ============================================
// Progress
// ============================================
function updateProgress(progress, answered) {
    const fill = document.getElementById('progressFill');
    if (fill) {
        fill.style.width = Math.min(progress, 100) + '%';
    }
}

// ============================================
// Next Question Rendering
// ============================================
function renderNextQuestion(question, answered) {
    const container = document.getElementById('questionContainer');
    questionsAnswered = answered;
    currentQuestionId = question.id;

    const skillLabels = {
        'vocabulary': 'Từ vựng', 'grammar': 'Ngữ pháp',
        'reading': 'Đọc hiểu', 'listening': 'Nghe',
    };
    const skillIcon = question.skill_type === 'listening' ? 'headphones'
        : (question.skill_type === 'reading' ? 'book-reader' : 'pencil-alt');

    let html = '<div class="placement-question-card" id="questionCard">';

    // Skill tag
    html += '<div class="placement-skill-tag skill-' + question.skill_type + '">';
    html += '<i class="fas fa-' + skillIcon + '"></i> ' + (skillLabels[question.skill_type] || 'Từ vựng');
    html += '</div>';

    // Passage (only show for reading)
    if (question.skill_type === 'reading' && question.passage) {
        html += '<div class="placement-passage"><p>' + question.passage.replace(/\n/g, '<br>') + '</p></div>';
    }

    // Audio
    if (question.skill_type === 'listening') {
        html += '<div class="placement-audio">';
        if (question.audio_url) {
            html += '<audio controls id="audioPlayer"><source src="' + question.audio_url + '" type="audio/mpeg"></audio>';
        } else {
            html += '<button type="button" class="btn btn-outline btn-sm" onclick="speakQuestion(\'' + (question.passage || question.question_text).replace(/'/g, "\\'") + '\')"><i class="fas fa-volume-up"></i> Nghe</button>';
        }
        html += '</div>';
    }

    // Question text
    html += '<div class="placement-question-text"><p>' + question.question_text + '</p></div>';

    // Options
    html += '<div class="placement-answer-options" id="answerOptions">';
    if (question.question_type === 'fill_blank') {
        html += '<div class="placement-fill-blank">';
        html += '<input type="text" id="fillInput" class="form-input placement-input" placeholder="Nhập câu trả lời..." autocomplete="off">';
        html += '<button type="button" class="btn btn-primary btn-lg" id="submitFill" onclick="submitFillBlank()">Kiểm tra</button>';
        html += '</div>';
    } else if (question.options && question.options.length) {
        const letters = 'ABCDEFGH';
        question.options.forEach((opt, i) => {
            html += '<button type="button" class="placement-option-btn" data-value="' + opt + '" data-index="' + i + '" onclick="selectOption(this, \'' + opt.replace(/'/g, "\\'") + '\')">';
            html += '<span class="option-letter">' + (letters[i] || '?') + '</span>';
            html += '<span class="option-label">' + opt + '</span>';
            html += '</button>';
        });
    }
    html += '</div>';
    html += '</div>';

    container.innerHTML = html;
    questionStartTime = Date.now();
    isAnswering = false;
    window.speechSynthesis.cancel();

    // Auto-focus input for fill_blank
    setTimeout(() => {
        const input = document.getElementById('fillInput');
        if (input) input.focus();
    }, 100);
}

// ============================================
// Keyboard shortcuts
// ============================================
document.addEventListener('keydown', function(e) {
    if (isAnswering) return;

    const keys = ['1', '2', '3', '4', 'a', 'b', 'c', 'd'];
    const idx = keys.indexOf(e.key.toLowerCase());
    if (idx >= 0) {
        const btn = document.querySelector('.placement-option-btn[data-index="' + (idx % 4) + '"]');
        if (btn) {
            btn.click();
            return;
        }
    }

    if (e.key === 'Enter') {
        const fillBtn = document.getElementById('submitFill');
        if (fillBtn) {
            fillBtn.click();
            return;
        }
    }
});

// ============================================
// Text-to-Speech fallback for listening questions
// ============================================
function speakQuestion(text) {
    if (!text && typeof SpeechSynthesisUtterance !== 'undefined') {
        const questionEl = document.querySelector('.placement-question-text p');
        text = questionEl ? questionEl.textContent : '';
    }
    if (text && typeof SpeechSynthesisUtterance !== 'undefined') {
        const utterance = new SpeechSynthesisUtterance(text);
        utterance.lang = 'en-US';
        utterance.rate = 0.9;
        window.speechSynthesis.speak(utterance);
    }
}
</script>

<link rel="stylesheet" href="<?= BASE_URL ?>/css/placement.css">
