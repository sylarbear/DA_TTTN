<!-- Profile Page -->
<section class="page-header">
    <div class="container">
        <div class="breadcrumb"><a href="<?= BASE_URL ?>">Trang chủ</a> <span>›</span> Hồ sơ cá nhân</div>
        <h1><i class="fas fa-user"></i> Hồ sơ cá nhân</h1>
    </div>
</section>

<section class="dashboard-section">
    <div class="container">
        <!-- Profile Header -->
        <div class="profile-header">
            <div class="profile-avatar">
                <i class="fas fa-user"></i>
            </div>
            <div class="profile-info" style="flex:1;">
                <h2><?= htmlspecialchars($user['full_name'] ?? $user['username']) ?></h2>
                <p style="color:var(--text-secondary);">@<?= htmlspecialchars($user['username']) ?> · <?= htmlspecialchars($user['email']) ?></p>
                <div class="profile-badges">
                    <span class="answer-badge <?= $user['membership']==='pro' ? 'correct' : '' ?>"><?= strtoupper($user['membership']) ?></span>
                    <span class="answer-badge" style="background:var(--bg-surface);"><?= ucfirst($user['role']) ?></span>
                    <span style="color:var(--text-muted); font-size:0.85rem;"><i class="fas fa-calendar"></i> Tham gia <?= date('d/m/Y', strtotime($user['created_at'])) ?></span>
                </div>
            </div>
        </div>

        <!-- Stats Row -->
        <div class="stats-grid" style="grid-template-columns:repeat(4, 1fr); margin-bottom:1.5rem;">
            <div class="stat-card">
                <div class="stat-icon" style="background:linear-gradient(135deg, #5B6CFF, #8B5CF6);"><i class="fas fa-clipboard-check"></i></div>
                <div class="stat-content"><span class="stat-value"><?= $stats['total_tests'] ?></span><span class="stat-title">Bài test đã làm</span></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:linear-gradient(135deg, #10B981, #34D399);"><i class="fas fa-chart-line"></i></div>
                <div class="stat-content"><span class="stat-value"><?= $stats['avg_score'] ?>%</span><span class="stat-title">Điểm trung bình</span></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:linear-gradient(135deg, #F43F5E, #FB7185);"><i class="fas fa-microphone"></i></div>
                <div class="stat-content"><span class="stat-value"><?= $stats['speaking_attempts'] ?></span><span class="stat-title">Lần luyện nói</span></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:linear-gradient(135deg, #06B6D4, #22D3EE);"><i class="fas fa-book-open"></i></div>
                <div class="stat-content"><span class="stat-value"><?= $stats['topics_studied'] ?></span><span class="stat-title">Chủ đề đã học</span></div>
            </div>
        </div>

        <div class="profile-grid">
            <!-- Left: Edit Profile -->
            <div>
                <div class="section-card">
                    <h3><i class="fas fa-edit"></i> Chỉnh sửa thông tin</h3>
                    <div class="auth-form" style="margin-top:1rem;">
                        <div class="form-group"><label>Họ tên</label><input type="text" id="editName" value="<?= htmlspecialchars($user['full_name'] ?? '') ?>" class="form-input"></div>
                        <div class="form-group"><label>Email</label><input type="email" id="editEmail" value="<?= htmlspecialchars($user['email']) ?>" class="form-input"></div>
                        <button class="btn btn-primary" onclick="updateProfile()"><i class="fas fa-save"></i> Lưu thay đổi</button>
                    </div>
                </div>

                <div class="section-card" style="margin-top:1rem;">
                    <h3><i class="fas fa-lock"></i> Đổi mật khẩu</h3>
                    <div class="auth-form" style="margin-top:1rem;">
                        <div class="form-group"><label>Mật khẩu hiện tại</label><input type="password" id="currentPw" class="form-input"></div>
                        <div class="form-group"><label>Mật khẩu mới</label><input type="password" id="newPw" class="form-input"></div>
                        <div class="form-group"><label>Nhập lại mật khẩu mới</label><input type="password" id="confirmPw" class="form-input"></div>
                        <button class="btn btn-outline" onclick="changePassword()"><i class="fas fa-key"></i> Đổi mật khẩu</button>
                    </div>
                </div>
            </div>

            <!-- Right: Badges + Recent -->
            <div>
                <!-- Badges -->
                <div class="section-card">
                    <h3><i class="fas fa-medal"></i> Huy hiệu (<?= count(array_filter($badges, fn($b) => $b['earned'])) ?>/<?= count($badges) ?>)</h3>
                    <div class="badge-grid" style="margin-top:1rem;">
                        <?php foreach ($badges as $b): ?>
                            <div class="badge-card <?= $b['earned'] ? 'earned' : 'locked' ?>">
                                <div class="badge-icon"><?= $b['icon'] ?></div>
                                <div class="badge-name"><?= $b['name'] ?></div>
                                <div class="badge-desc"><?= $b['desc'] ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Recent Tests -->
                <div class="section-card" style="margin-top:1rem;">
                    <h3><i class="fas fa-history"></i> Bài test gần đây</h3>
                    <?php if (empty($recentTests)): ?>
                        <p class="empty-text">Chưa có bài test nào</p>
                    <?php else: ?>
                        <div class="activity-list" style="margin-top:0.8rem;">
                            <?php foreach ($recentTests as $t): ?>
                                <div class="activity-item">
                                    <div class="activity-icon type-<?= $t['test_type'] ?>"><i class="fas fa-<?= $t['test_type']==='quiz'?'question':($t['test_type']==='listening'?'headphones':'book-reader') ?>"></i></div>
                                    <div class="activity-info">
                                        <strong><?= htmlspecialchars($t['title']) ?></strong>
                                        <small><?= date('d/m/Y H:i', strtotime($t['completed_at'])) ?></small>
                                    </div>
                                    <span class="activity-score" style="color:<?= $t['percentage']>=70?'var(--success)':'var(--error)' ?>"><?= $t['percentage'] ?>%</span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function updateProfile() {
    fetch('<?= BASE_URL ?>/profile/update', {
        method:'POST', headers:{'Content-Type':'application/json'}, credentials:'same-origin',
        body: JSON.stringify({ full_name: document.getElementById('editName').value, email: document.getElementById('editEmail').value })
    }).then(r=>r.json()).then(d => {
        if(d.success) { showToast(d.message,'success'); setTimeout(()=>location.reload(),1000); }
        else showToast(d.error,'error');
    });
}
function changePassword() {
    const newPw = document.getElementById('newPw').value;
    if(newPw !== document.getElementById('confirmPw').value) { showToast('Mật khẩu mới không khớp','error'); return; }
    if(newPw.length < 6) { showToast('Mật khẩu mới phải ≥ 6 ký tự','error'); return; }
    fetch('<?= BASE_URL ?>/profile/changePassword', {
        method:'POST', headers:{'Content-Type':'application/json'}, credentials:'same-origin',
        body: JSON.stringify({ current_password: document.getElementById('currentPw').value, new_password: newPw })
    }).then(r=>r.json()).then(d => {
        if(d.success) { showToast(d.message,'success'); ['currentPw','newPw','confirmPw'].forEach(id=>document.getElementById(id).value=''); }
        else showToast(d.error,'error');
    });
}
function showToast(msg, type) {
    const t = document.createElement('div');
    t.className = 'flash-message flash-' + type;
    const icon = type === 'success' ? 'check' : 'exclamation';
    t.innerHTML = '<div class="flash-content"><i class="fas fa-' + icon + '-circle"></i><span></span></div>';
    t.querySelector('span').textContent = msg;
    document.body.appendChild(t);
    setTimeout(() => t.remove(), 3000);
}
</script>
