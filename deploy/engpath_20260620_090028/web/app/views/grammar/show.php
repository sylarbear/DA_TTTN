<!-- Grammar Lesson Detail -->
<section class="page-header">
    <div class="container">
        <div class="breadcrumb"><a href="<?= BASE_URL ?>/grammar">Ngữ pháp</a> <span>›</span> <?= htmlspecialchars($lesson['title']) ?></div>
        <h1><i class="fas fa-book-open"></i> <?= htmlspecialchars($lesson['title']) ?></h1>
        <span class="topic-level level-<?= $lesson['level'] ?>"><?= ucfirst($lesson['level']) ?></span>
    </div>
</section>

<section class="dashboard-section">
    <div class="container" style="max-width:800px;">
        <!-- Lesson Content -->
        <div class="section-card">
            <div class="grammar-content"><?= $lesson['content_html'] ?></div>
            <?php if (!empty($lesson['examples'])): ?>
                <div style="margin-top:1.5rem; padding:1rem; background:var(--bg-surface); border-radius:var(--radius); border-left:4px solid var(--primary);">
                    <h4 style="margin-bottom:0.5rem;"><i class="fas fa-lightbulb" style="color:var(--accent-orange);"></i> Ví dụ</h4>
                    <?php foreach (explode("\n", $lesson['examples']) as $ex): ?>
                        <?php if (trim($ex) !== ''): ?>
                        <p style="margin:0.3rem 0; color:var(--text-secondary);"><em><?= htmlspecialchars($ex) ?></em></p>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Quiz -->
        <?php if (!empty($questions)): ?>
        <div class="section-card" style="margin-top:1.5rem;">
            <h3><i class="fas fa-pencil-alt"></i> Quiz thực hành (<?= count($questions) ?> câu)</h3>
            <form id="grammarQuiz">
                <?php foreach ($questions as $i => $q):
                    $opts = json_decode($q['options'], true);
                ?>
                    <div class="quiz-question" id="gq-<?= $q['id'] ?>">
                        <p class="quiz-q-text"><strong><?= $i + 1 ?>.</strong> <?= htmlspecialchars($q['question_text']) ?></p>
                        <div class="quiz-options">
                            <?php foreach ($opts as $key => $val): ?>
                                <label class="quiz-option" id="opt-<?= $q['id'] ?>-<?= $key ?>">
                                    <input type="radio" name="q_<?= $q['id'] ?>" value="<?= $key ?>">
                                    <span class="option-letter"><?= $key ?></span>
                                    <span><?= htmlspecialchars($val) ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                        <div class="quiz-explanation" id="exp-<?= $q['id'] ?>" style="display:none;"></div>
                    </div>
                <?php endforeach; ?>
                <button type="button" class="btn btn-primary" onclick="submitGrammarQuiz()" style="margin-top:1rem; width:100%;">
                    <i class="fas fa-check"></i> Nộp bài
                </button>
            </form>
            <div id="quizResult" style="display:none; margin-top:1rem;"></div>
        </div>
        <?php endif; ?>
    </div>
</section>

<script>
function submitGrammarQuiz() {
    const answers = {};
    document.querySelectorAll('#grammarQuiz input[type=radio]:checked').forEach(r => {
        const qId = r.name.replace('q_', '');
        answers[qId] = r.value;
    });

    fetch('<?= BASE_URL ?>/grammar/submitQuiz', {
        method:'POST', headers:{'Content-Type':'application/json'}, credentials:'same-origin',
        body: JSON.stringify({ lesson_id: <?= $lesson['id'] ?>, answers })
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            // Show results
            d.results.forEach(r => {
                const q = document.getElementById('gq-' + r.id);
                const exp = document.getElementById('exp-' + r.id);
                // Highlight correct/incorrect
                document.querySelectorAll(`input[name="q_${r.id}"]`).forEach(input => {
                    const label = input.parentElement;
                    if (input.value === r.correct_answer) label.classList.add('correct');
                    if (input.checked && !r.correct) label.classList.add('wrong');
                    input.disabled = true;
                });
                if (r.explanation) {
                    exp.style.display = 'block';
                    exp.textContent = '';
                    const icon = document.createElement('i');
                    icon.className = 'fas fa-info-circle';
                    exp.appendChild(icon);
                    exp.appendChild(document.createTextNode(' ' + r.explanation));
                }
            });
            document.getElementById('quizResult').style.display = 'block';
            document.getElementById('quizResult').innerHTML =
                `<div class="section-card" style="text-align:center; background:${d.score >= 70 ? '#ECFDF5' : '#FEF2F2'}; padding:1.5rem;">
                    <div style="font-size:2.5rem;">${d.score >= 70 ? '🎉' : '📝'}</div>
                    <h3>${d.correct}/${d.total} đúng (${d.score}%)</h3>
                    <p style="color:var(--text-secondary);">${d.score >= 50 ? 'Chúc mừng! +20 XP' : 'Hãy ôn lại bài giảng và thử lại!'}</p>
                </div>`;
        }
    });
}
</script>
