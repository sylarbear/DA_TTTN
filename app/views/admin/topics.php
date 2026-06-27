<?php
$adminActive = 'topics';
$adminTitle = 'Quản lý Khóa học';
$adminSubtitle = 'Quản lý chủ đề, level, mô tả và số lượng nội dung học tập.';
require APP_PATH . '/views/admin/_nav.php';
?>

<section class="dashboard-section">
    <div class="container">
        <div style="display:flex; justify-content:flex-end; margin-bottom:1rem;">
            <button class="btn btn-primary" onclick="showTopicModal()"><i class="fas fa-plus"></i> Thêm chủ đề</button>
        </div>

        <div class="section-card">
            <div class="progress-table">
                <table>
                    <thead><tr><th>ID</th><th>Tên chủ đề</th><th>Mô tả</th><th>Level</th><th>Bài học</th><th>Từ vựng</th><th>Tests</th><th>Thao tác</th></tr></thead>
                    <tbody>
                    <?php foreach ($topics as $t): ?>
                        <tr id="topic-<?= $t['id'] ?>">
                            <td><?= $t['id'] ?></td>
                            <td><strong><?= htmlspecialchars($t['name']) ?></strong></td>
                            <td style="max-width:250px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"><?= htmlspecialchars($t['description'] ?? '') ?></td>
                            <td><span class="topic-level level-<?= $t['level'] ?>"><?= ucfirst($t['level']) ?></span></td>
                            <td><?= $t['lesson_count'] ?></td>
                            <td><?= $t['vocab_count'] ?></td>
                            <td><?= $t['test_count'] ?></td>
                            <td>
                                <button class="btn btn-sm btn-outline" onclick='editTopic(<?= htmlspecialchars(json_encode($t), ENT_QUOTES, 'UTF-8') ?>)'><i class="fas fa-edit"></i></button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<!-- Topic Modal -->
<div class="modal" id="topicModal">
    <div class="modal-overlay" onclick="closeModal('topicModal')"></div>
    <div class="modal-content">
        <div class="modal-header"><h2 id="topicModalTitle">Thêm chủ đề</h2></div>
        <input type="hidden" id="topicId">
        <div class="auth-form">
            <div class="form-group"><label>Tên chủ đề</label><input type="text" id="topicName" class="form-input"></div>
            <div class="form-group"><label>Mô tả</label><textarea id="topicDesc" class="form-input" rows="3" style="resize:vertical;"></textarea></div>
            <div class="form-group"><label>Level</label>
                <select id="topicLevel" class="form-input">
                    <option value="beginner">Beginner</option>
                    <option value="intermediate">Intermediate</option>
                    <option value="advanced">Advanced</option>
                </select>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('topicModal')">Hủy</button>
            <button class="btn btn-primary" onclick="saveTopic()"><i class="fas fa-save"></i> Lưu</button>
        </div>
    </div>
</div>

<script>
function showTopicModal() {
    document.getElementById('topicModalTitle').textContent = 'Thêm chủ đề';
    document.getElementById('topicId').value = '';
    document.getElementById('topicName').value = '';
    document.getElementById('topicDesc').value = '';
    document.getElementById('topicLevel').value = 'beginner';
    document.getElementById('topicModal').classList.add('active');
}
function editTopic(t) {
    document.getElementById('topicModalTitle').textContent = 'Sửa chủ đề';
    document.getElementById('topicId').value = t.id;
    document.getElementById('topicName').value = t.name;
    document.getElementById('topicDesc').value = t.description || '';
    document.getElementById('topicLevel').value = t.level;
    document.getElementById('topicModal').classList.add('active');
}
function closeModal(id) { document.getElementById(id).classList.remove('active'); }
function saveTopic() {
    fetch('<?= BASE_URL ?>/admin/saveTopic', {
        method:'POST', headers:{'Content-Type':'application/json'}, credentials:'same-origin',
        body: JSON.stringify({
            id: document.getElementById('topicId').value,
            name: document.getElementById('topicName').value,
            description: document.getElementById('topicDesc').value,
            level: document.getElementById('topicLevel').value
        })
    }).then(r=>r.json()).then(d => {
        if(d.success) { alert(d.message); location.reload(); }
        else alert(d.error);
    });
}
</script>
