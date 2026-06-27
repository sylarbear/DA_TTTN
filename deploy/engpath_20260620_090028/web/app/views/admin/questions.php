<?php
$adminActive = 'questions';
$adminTitle = 'Quản lý Câu hỏi';
$adminSubtitle = 'Chọn bài test và cập nhật bộ câu hỏi, đáp án, đoạn đọc/nghe.';
require APP_PATH . '/views/admin/_nav.php';
?>

<section class="dashboard-section">
    <div class="container">
        <!-- Test Selector -->
        <div class="section-card" style="margin-bottom:1rem;">
            <form method="GET" action="<?= BASE_URL ?>/admin/questions" style="display:flex; gap:0.8rem; align-items:center;">
                <label style="font-weight:600; white-space:nowrap;">Chọn bài test:</label>
                <select name="test_id" class="form-input" style="flex:1;" onchange="this.form.submit()">
                    <option value="">-- Chọn bài test --</option>
                    <?php foreach ($tests as $t): ?>
                        <option value="<?= $t['id'] ?>" <?= $testId == $t['id'] ? 'selected' : '' ?>>
                            [<?= ucfirst($t['test_type']) ?>] <?= htmlspecialchars($t['title']) ?> (<?= htmlspecialchars($t['topic_name']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>

        <?php if ($currentTest): ?>
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
            <h3><?= htmlspecialchars($currentTest['title']) ?> — <?= count($questions) ?> câu hỏi</h3>
            <button class="btn btn-primary" onclick="showQuestionModal()"><i class="fas fa-plus"></i> Thêm câu hỏi</button>
        </div>

        <div class="section-card">
            <div class="progress-table">
                <table>
                    <thead><tr><th>#</th><th>Câu hỏi</th><th>A</th><th>B</th><th>C</th><th>D</th><th>Đáp án</th><th>Thao tác</th></tr></thead>
                    <tbody>
                    <?php foreach ($questions as $i => $q):
                        $opts = json_decode($q['options_json'], true);
                        // Normalize: convert array format [0,1,2,3] to {A,B,C,D}
                        if ($opts && isset($opts[0])) {
                            $opts = ['A' => $opts[0] ?? '', 'B' => $opts[1] ?? '', 'C' => $opts[2] ?? '', 'D' => $opts[3] ?? ''];
                        }
                    ?>
                        <tr>
                            <td><?= $i+1 ?></td>
                            <td style="max-width:300px;"><?= htmlspecialchars(mb_substr($q['question_text'],0,80)) ?>...</td>
                            <td><?= htmlspecialchars(mb_substr($opts['A']??'',0,20)) ?></td>
                            <td><?= htmlspecialchars(mb_substr($opts['B']??'',0,20)) ?></td>
                            <td><?= htmlspecialchars(mb_substr($opts['C']??'',0,20)) ?></td>
                            <td><?= htmlspecialchars(mb_substr($opts['D']??'',0,20)) ?></td>
                            <td><span class="answer-badge correct"><?= htmlspecialchars($q['correct_answer']) ?></span></td>
                            <td style="white-space:nowrap;">
                                <button class="btn btn-sm btn-outline" onclick='editQuestion(<?= htmlspecialchars(json_encode($q), ENT_QUOTES, "UTF-8") ?>)'><i class="fas fa-edit"></i></button>
                                <button class="btn btn-sm" style="background:var(--error);color:white;" onclick="deleteQuestion(<?= $q['id'] ?>)"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php else: ?>
            <div class="empty-state"><i class="fas fa-hand-pointer"></i><p>Chọn bài test ở trên để xem và quản lý câu hỏi</p></div>
        <?php endif; ?>
    </div>
</section>

<!-- Question Modal -->
<div class="modal" id="questionModal">
    <div class="modal-overlay" onclick="closeModal('questionModal')"></div>
    <div class="modal-content" style="max-width:650px;">
        <div class="modal-header"><h2 id="qModalTitle">Thêm câu hỏi</h2></div>
        <input type="hidden" id="qId">
        <div class="auth-form">
            <div class="form-group"><label>Câu hỏi</label><textarea id="qText" class="form-input" rows="3"></textarea></div>
            <div class="form-group"><label>Passage / Đoạn văn (nếu có)</label><textarea id="qPassage" class="form-input" rows="2"></textarea></div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:0.8rem;">
                <div class="form-group"><label>A</label><input type="text" id="qA" class="form-input"></div>
                <div class="form-group"><label>B</label><input type="text" id="qB" class="form-input"></div>
                <div class="form-group"><label>C</label><input type="text" id="qC" class="form-input"></div>
                <div class="form-group"><label>D</label><input type="text" id="qD" class="form-input"></div>
            </div>
            <div class="form-group"><label>Đáp án đúng</label>
                <select id="qAnswer" class="form-input"><option value="A">A</option><option value="B">B</option><option value="C">C</option><option value="D">D</option></select>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('questionModal')">Hủy</button>
            <button class="btn btn-primary" onclick="saveQuestion()"><i class="fas fa-save"></i> Lưu</button>
        </div>
    </div>
</div>

<script>
function showQuestionModal() {
    document.getElementById('qModalTitle').textContent = 'Thêm câu hỏi';
    document.getElementById('qId').value = '';
    ['qText','qPassage','qA','qB','qC','qD'].forEach(id => document.getElementById(id).value = '');
    document.getElementById('qAnswer').value = 'A';
    document.getElementById('questionModal').classList.add('active');
}
function editQuestion(q) {
    let opts = typeof q.options_json === 'string' ? JSON.parse(q.options_json) : (q.options_json || {});
    // Normalize array format to {A,B,C,D}
    if (Array.isArray(opts)) {
        opts = {A: opts[0]||'', B: opts[1]||'', C: opts[2]||'', D: opts[3]||''};
    }
    document.getElementById('qModalTitle').textContent = 'Sửa câu hỏi #' + q.id;
    document.getElementById('qId').value = q.id;
    document.getElementById('qText').value = q.question_text;
    document.getElementById('qPassage').value = q.passage || '';
    document.getElementById('qA').value = opts.A || '';
    document.getElementById('qB').value = opts.B || '';
    document.getElementById('qC').value = opts.C || '';
    document.getElementById('qD').value = opts.D || '';
    document.getElementById('qAnswer').value = q.correct_answer;
    document.getElementById('questionModal').classList.add('active');
}
function closeModal(id) { document.getElementById(id).classList.remove('active'); }
function saveQuestion() {
    fetch('<?= BASE_URL ?>/admin/saveQuestion', {
        method:'POST', headers:{'Content-Type':'application/json'}, credentials:'same-origin',
        body: JSON.stringify({
            id: document.getElementById('qId').value,
            test_id: <?= $testId ?: 0 ?>,
            question_text: document.getElementById('qText').value,
            passage: document.getElementById('qPassage').value,
            option_a: document.getElementById('qA').value,
            option_b: document.getElementById('qB').value,
            option_c: document.getElementById('qC').value,
            option_d: document.getElementById('qD').value,
            correct_answer: document.getElementById('qAnswer').value
        })
    }).then(r=>r.json()).then(d => { if(d.success) { alert(d.message); location.reload(); } else alert(d.error); });
}
function deleteQuestion(id) {
    if(!confirm('Xóa câu hỏi này?')) return;
    fetch('<?= BASE_URL ?>/admin/deleteQuestion', {
        method:'POST', headers:{'Content-Type':'application/json'}, credentials:'same-origin',
        body: JSON.stringify({id: id})
    }).then(r=>r.json()).then(d => { if(d.success) location.reload(); });
}
</script>
