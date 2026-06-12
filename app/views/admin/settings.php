<?php
$adminActive = 'settings';
$adminTitle = 'Cài đặt hệ thống';
$adminSubtitle = 'Cấu hình API key và các tính năng AI của hệ thống.';
require APP_PATH . '/views/admin/_nav.php';
?>

<section class="dashboard-section">
    <div class="container" style="max-width:700px;">
        <!-- OpenAI API Key -->
        <div class="section-card">
            <h3><i class="fas fa-robot"></i> OpenAI API Key</h3>
            <p style="color:var(--text-secondary); margin:0.5rem 0 1rem;">Nhập API key từ <a href="https://platform.openai.com/api-keys" target="_blank">platform.openai.com</a> để kích hoạt tính năng AI Speaking + Chatbot.</p>
            
            <!-- Status -->
            <div style="display:flex; align-items:center; gap:0.8rem; margin-bottom:1.5rem; padding:1rem; border-radius:var(--radius-sm); background:<?= $hasKey ? '#ECFDF5' : '#FEF2F2' ?>;">
                <i class="fas fa-<?= $hasKey ? 'check-circle' : 'times-circle' ?>" style="font-size:1.5rem; color:<?= $hasKey ? 'var(--success)' : 'var(--error)' ?>;"></i>
                <div>
                    <strong><?= $hasKey ? 'AI đang hoạt động' : 'AI chưa được kích hoạt' ?></strong>
                    <?php if ($hasKey): ?>
                        <p style="color:var(--text-muted); font-size:0.85rem; margin:0;">Key hiện tại: <code><?= $maskedKey ?></code></p>
                    <?php else: ?>
                        <p style="color:var(--text-muted); font-size:0.85rem; margin:0;">Hệ thống đang dùng chấm điểm Speaking bằng thuật toán local.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="auth-form">
                <div class="form-group">
                    <label>API Key</label>
                    <input type="password" id="apiKeyInput" class="form-input" placeholder="sk-xxxxxxxxxxxxxxxxxxxxxxxx" value="">
                    <small style="color:var(--text-muted);">Key bắt đầu bằng "sk-". Để trống và bấm Lưu nếu muốn tắt AI.</small>
                </div>
                <div style="display:flex; gap:0.8rem;">
                    <button class="btn btn-primary" onclick="saveApiKey()"><i class="fas fa-save"></i> Lưu API Key</button>
                    <?php if ($hasKey): ?>
                        <button class="btn btn-outline" onclick="testApiKey()"><i class="fas fa-vial"></i> Test kết nối</button>
                    <?php endif; ?>
                </div>
                <div id="settingsResult" style="margin-top:0.8rem;"></div>
            </div>
        </div>

        <!-- AI Features Info -->
        <div class="section-card" style="margin-top:1rem;">
            <h3><i class="fas fa-info-circle"></i> Tính năng AI</h3>
            <div style="display:flex; flex-direction:column; gap:0.8rem; margin-top:1rem;">
                <div style="display:flex; align-items:center; gap:1rem; padding:0.8rem; background:var(--bg-surface); border-radius:var(--radius-sm);">
                    <i class="fas fa-microphone" style="color:var(--primary); font-size:1.3rem;"></i>
                    <div>
                        <strong>AI Speaking Evaluation</strong>
                        <p style="color:var(--text-muted); font-size:0.85rem; margin:0;">OpenAI phân tích ngữ pháp, từ vựng, phát âm + feedback chi tiết bằng tiếng Việt.</p>
                    </div>
                    <span class="answer-badge <?= $hasKey ? 'correct' : '' ?>" style="margin-left:auto;"><?= $hasKey ? 'ON' : 'OFF' ?></span>
                </div>
                <div style="display:flex; align-items:center; gap:1rem; padding:0.8rem; background:var(--bg-surface); border-radius:var(--radius-sm);">
                    <i class="fas fa-robot" style="color:var(--secondary); font-size:1.3rem;"></i>
                    <div>
                        <strong>AI Chatbot</strong>
                        <p style="color:var(--text-muted); font-size:0.85rem; margin:0;">Widget chat nổi hỗ trợ học ngữ pháp, từ vựng, dịch thuật.</p>
                    </div>
                    <span class="answer-badge <?= $hasKey ? 'correct' : '' ?>" style="margin-left:auto;"><?= $hasKey ? 'ON' : 'OFF' ?></span>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function saveApiKey() {
    const key = document.getElementById('apiKeyInput').value.trim();
    fetch('<?= BASE_URL ?>/admin/saveSettings', {
        method:'POST', headers:{'Content-Type':'application/json'}, credentials:'same-origin',
        body: JSON.stringify({ openai_key: key })
    }).then(r=>r.json()).then(d => {
        if(d.success) {
            const resEl = document.getElementById('settingsResult');
            resEl.innerHTML = '<div style="color:var(--success);"><i class="fas fa-check-circle"></i> <span id="settingsOkMsg"></span></div>';
            document.getElementById('settingsOkMsg').textContent = d.message;
            setTimeout(() => location.reload(), 1500);
        } else {
            document.getElementById('settingsResult').innerHTML = '<div style="color:var(--error);"><i class="fas fa-times-circle"></i> <span id="settingsErrMsg"></span></div>';
            document.getElementById('settingsErrMsg').textContent = d.error || 'Lỗi';
        }
    });
}

function testApiKey() {
    document.getElementById('settingsResult').innerHTML = '<div style="color:var(--text-muted);"><i class="fas fa-spinner fa-spin"></i> Đang kiểm tra kết nối...</div>';
    fetch('<?= BASE_URL ?>/chatbot/status', { credentials:'same-origin' })
    .then(r=>r.json()).then(d => {
        if(d.available) {
            document.getElementById('settingsResult').innerHTML = '<div style="color:var(--success);"><i class="fas fa-check-circle"></i> Kết nối thành công! AI sẵn sàng.</div>';
        } else {
            document.getElementById('settingsResult').innerHTML = '<div style="color:var(--error);"><i class="fas fa-times-circle"></i> Không thể kết nối. Kiểm tra lại API key.</div>';
        }
    });
}
</script>
