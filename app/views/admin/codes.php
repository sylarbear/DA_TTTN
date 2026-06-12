<?php
$adminActive = 'codes';
$adminTitle = 'Quản lý Mã kích hoạt';
$adminSubtitle = 'Tạo, theo dõi và thu hồi mã kích hoạt gói học Pro.';
require APP_PATH . '/views/admin/_nav.php';
?>

<section class="dashboard-section">
    <div class="container">
        <!-- Create Code Form -->
        <div class="section-card" style="margin-bottom:1rem;">
            <h3><i class="fas fa-plus-circle"></i> Tạo mã kích hoạt mới</h3>
            <div style="display:flex; gap:0.8rem; margin-top:1rem; flex-wrap:wrap;">
                <input type="text" id="newCode" placeholder="Nhập mã (VD: PRO-2024-GIFT)" class="form-input" style="flex:1; min-width:200px; text-transform:uppercase;">
                <select id="newPlanId" class="form-input" style="width:200px;">
                    <?php foreach ($plans as $p): ?>
                        <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?> (<?= number_format($p['price']) ?>đ)</option>
                    <?php endforeach; ?>
                </select>
                <button class="btn btn-primary" onclick="createCode()"><i class="fas fa-plus"></i> Tạo mã</button>
            </div>
            <div id="createResult" style="margin-top:0.5rem;"></div>
        </div>

        <div class="section-card">
            <h3><i class="fas fa-list"></i> Danh sách mã (<?= count($codes) ?>)</h3>
            <div class="progress-table">
                <table>
                    <thead><tr><th>Mã</th><th>Gói</th><th>Trạng thái</th><th>Sử dụng bởi</th><th>Ngày dùng</th><th>Thao tác</th></tr></thead>
                    <tbody>
                    <?php foreach ($codes as $c): ?>
                        <tr id="code-<?= $c['id'] ?>">
                            <td><code style="font-size:0.9rem;"><?= htmlspecialchars($c['code']) ?></code></td>
                            <td><?= htmlspecialchars($c['plan_name']) ?></td>
                            <td>
                                <span class="answer-badge <?= $c['is_used'] ? 'incorrect' : 'correct' ?>">
                                    <?= $c['is_used'] ? 'Đã dùng' : 'Chưa dùng' ?>
                                </span>
                            </td>
                            <td><?= $c['used_by_name'] ? htmlspecialchars($c['used_by_name']) : '—' ?></td>
                            <td><?= $c['used_at'] ? date('d/m/Y H:i', strtotime($c['used_at'])) : '—' ?></td>
                            <td>
                                <?php if (!$c['is_used']): ?>
                                    <button class="btn btn-sm" style="background:var(--error);color:white;" onclick="deleteCode(<?= $c['id'] ?>)"><i class="fas fa-trash"></i></button>
                                <?php else: ?><span style="color:var(--text-muted);">—</span><?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<script>
function createCode() {
    const code = document.getElementById('newCode').value.trim();
    if(!code) { alert('Nhập mã kích hoạt'); return; }
    fetch('<?= BASE_URL ?>/admin/createCode', {
        method:'POST', headers:{'Content-Type':'application/json'}, credentials:'same-origin',
        body: JSON.stringify({ code: code, plan_id: document.getElementById('newPlanId').value })
    }).then(r=>r.json()).then(d => {
        if(d.success) { location.reload(); } 
        else { const errEl = document.getElementById('createResult'); errEl.innerHTML='<span style="color:var(--error);"></span>'; errEl.querySelector('span').textContent=d.error; }
    });
}
function deleteCode(id) {
    if(!confirm('Xóa mã này?')) return;
    fetch('<?= BASE_URL ?>/admin/deleteCode', {
        method:'POST', headers:{'Content-Type':'application/json'}, credentials:'same-origin',
        body: JSON.stringify({id: id})
    }).then(r=>r.json()).then(d => { if(d.success) document.getElementById('code-'+id).remove(); });
}
</script>
