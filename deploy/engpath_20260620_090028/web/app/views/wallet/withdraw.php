<!-- Withdraw Page -->
<section class="page-header">
    <div class="container">
        <nav class="breadcrumb"><a href="<?= BASE_URL ?>/wallet">Ví</a> <span>/</span> <span>Rút tiền</span></nav>
        <h1><i class="fas fa-arrow-circle-down"></i> Rút tiền về ngân hàng</h1>
    </div>
</section>

<section class="dashboard-section">
    <div class="container" style="max-width:600px;">
        <!-- Balance -->
        <div style="text-align:center; margin-bottom:1.5rem; padding:1rem; background:rgba(99,102,241,0.05); border-radius:var(--radius-sm);">
            <span style="color:var(--text-muted);">Số dư khả dụng:</span>
            <strong style="color:var(--primary); font-size:1.2rem; margin-left:0.5rem;" id="currentBalance"><?= number_format($balance) ?>đ</strong>
        </div>

        <?php if ($balance < 50000): ?>
            <div class="section-card" style="padding:2rem; text-align:center;">
                <i class="fas fa-exclamation-circle" style="font-size:3rem; color:var(--text-muted); margin-bottom:1rem;"></i>
                <h3 style="color:var(--text-muted);">Số dư không đủ</h3>
                <p style="color:var(--text-muted);">Số tiền rút tối thiểu là 50,000đ. Số dư hiện tại: <?= number_format($balance) ?>đ</p>
                <a href="<?= BASE_URL ?>/wallet" class="btn btn-outline" style="margin-top:1rem;"><i class="fas fa-arrow-left"></i> Quay lại</a>
            </div>
        <?php else: ?>
            <div class="section-card" style="padding:2rem;">
                <form id="withdrawForm">
                    <!-- Số tiền -->
                    <div class="form-group" style="margin-bottom:1.5rem;">
                        <label style="font-weight:600; margin-bottom:0.5rem; display:block;"><i class="fas fa-money-bill-wave"></i> Số tiền rút</label>
                        <input type="number" id="withdrawAmount" class="form-input" placeholder="Nhập số tiền (VNĐ)" 
                               min="50000" max="<?= $balance ?>" step="10000" style="padding:0.75rem;" required>
                        <small style="color:var(--text-muted);">Tối thiểu 50,000đ — Tối đa <?= number_format($balance) ?>đ</small>
                    </div>

                    <!-- Ngân hàng -->
                    <div class="form-group" style="margin-bottom:1.5rem;">
                        <label style="font-weight:600; margin-bottom:0.5rem; display:block;"><i class="fas fa-university"></i> Ngân hàng</label>
                        <select id="bankName" class="form-input" style="padding:0.75rem;" required>
                            <option value="">-- Chọn ngân hàng --</option>
                            <option value="Vietcombank">Vietcombank (VCB)</option>
                            <option value="Techcombank">Techcombank (TCB)</option>
                            <option value="BIDV">BIDV</option>
                            <option value="Agribank">Agribank</option>
                            <option value="VietinBank">VietinBank</option>
                            <option value="MBBank">MBBank</option>
                            <option value="ACB">ACB</option>
                            <option value="TPBank">TPBank</option>
                            <option value="Sacombank">Sacombank</option>
                            <option value="VPBank">VPBank</option>
                            <option value="SHB">SHB</option>
                            <option value="Momo">Ví Momo</option>
                        </select>
                    </div>

                    <!-- STK -->
                    <div class="form-group" style="margin-bottom:1.5rem;">
                        <label style="font-weight:600; margin-bottom:0.5rem; display:block;"><i class="fas fa-credit-card"></i> Số tài khoản</label>
                        <input type="text" id="bankAccount" class="form-input" placeholder="Nhập số tài khoản" style="padding:0.75rem;" required>
                    </div>

                    <!-- Chủ TK -->
                    <div class="form-group" style="margin-bottom:1.5rem;">
                        <label style="font-weight:600; margin-bottom:0.5rem; display:block;"><i class="fas fa-user"></i> Chủ tài khoản</label>
                        <input type="text" id="bankHolder" class="form-input" placeholder="VD: NGUYEN VAN A" style="padding:0.75rem; text-transform:uppercase;" required>
                    </div>

                    <div style="padding:0.75rem; background:#eff6ff; border-radius:var(--radius-sm); font-size:0.85rem; color:#1e40af; margin-bottom:1.5rem;">
                        <i class="fas fa-info-circle"></i> Admin sẽ chuyển khoản cho bạn trong <strong>1-3 ngày làm việc</strong> sau khi duyệt yêu cầu.
                    </div>

                    <!-- Submit -->
                    <div style="display:flex; gap:1rem; justify-content:flex-end;">
                        <a href="<?= BASE_URL ?>/wallet" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Quay lại</a>
                        <button type="submit" class="btn btn-primary" id="submitWithdrawBtn">
                            <i class="fas fa-paper-plane"></i> Gửi yêu cầu rút tiền
                        </button>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </div>
</section>

<script>
document.getElementById('withdrawForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const amount = parseInt(document.getElementById('withdrawAmount').value) || 0;
    const bankName = document.getElementById('bankName').value;
    const bankAccount = document.getElementById('bankAccount').value.trim();
    const bankHolder = document.getElementById('bankHolder').value.trim();
    
    if (amount < 50000) return alert('Số tiền rút tối thiểu 50,000đ');
    if (!bankName) return alert('Vui lòng chọn ngân hàng');
    if (!bankAccount) return alert('Vui lòng nhập số tài khoản');
    if (!bankHolder) return alert('Vui lòng nhập tên chủ tài khoản');
    
    if (!confirm(`Xác nhận rút ${new Intl.NumberFormat('vi-VN').format(amount)}đ về ${bankName} - ${bankAccount}?`)) return;
    
    const btn = document.getElementById('submitWithdrawBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang gửi...';
    
    fetch('<?= BASE_URL ?>/wallet/createWithdraw', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'same-origin',
        body: JSON.stringify({ amount, bank_name: bankName, bank_account: bankAccount, bank_holder: bankHolder })
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            document.querySelector('.section-card').innerHTML = `
                <div style="text-align:center; padding:2rem;">
                    <i class="fas fa-check-circle" style="font-size:3.5rem; color:var(--success); margin-bottom:1rem;"></i>
                    <h2>Yêu cầu rút tiền đã gửi!</h2>
                    <p style="color:var(--text-secondary);">${res.message}</p>
                    <a href="<?= BASE_URL ?>/wallet" class="btn btn-primary" style="margin-top:1rem;"><i class="fas fa-wallet"></i> Về ví</a>
                </div>
            `;
        } else {
            alert(res.error);
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-paper-plane"></i> Gửi yêu cầu rút tiền';
        }
    });
});
</script>
