<!-- Deposit Page -->
<section class="page-header">
    <div class="container">
        <nav class="breadcrumb"><a href="<?= BASE_URL ?>/wallet">Ví</a> <span>/</span> <span>Nạp tiền</span></nav>
        <h1><i class="fas fa-plus-circle"></i> Nạp tiền vào ví</h1>
    </div>
</section>

<section class="dashboard-section">
    <div class="container" style="max-width:650px;">
        <!-- Current Balance -->
        <div style="text-align:center; margin-bottom:1.5rem; padding:1rem; background:rgba(99,102,241,0.05); border-radius:var(--radius-sm);">
            <span style="color:var(--text-muted);">Số dư hiện tại:</span>
            <strong style="color:var(--primary); font-size:1.2rem; margin-left:0.5rem;"><?= number_format($balance) ?>đ</strong>
        </div>

        <?php if ($pendingDeposit): ?>
            <!-- Pending Deposit Alert -->
            <div class="section-card" style="padding:1.5rem; margin-bottom:1.5rem; border-left:4px solid #f59e0b;">
                <h4 style="color:#d97706; margin:0 0 0.5rem;"><i class="fas fa-clock"></i> Đang chờ duyệt</h4>
                <p style="margin:0; font-size:0.9rem;">
                    Bạn đã gửi yêu cầu nạp <strong><?= number_format($pendingDeposit['amount']) ?>đ</strong> 
                    lúc <?= date('d/m/Y H:i', strtotime($pendingDeposit['created_at'])) ?>. 
                    Vui lòng chờ Admin xác nhận chuyển khoản.
                </p>
            </div>
        <?php else: ?>
            <div class="section-card" style="padding:2rem;" id="depositForm">
                <!-- Amount Input -->
                <div class="form-group" style="margin-bottom:1.5rem;">
                    <label style="font-weight:600; margin-bottom:0.5rem; display:block;">
                        <i class="fas fa-money-bill-wave"></i> Số tiền muốn nạp
                    </label>
                    <input type="number" id="depositAmount" class="form-input" placeholder="Nhập số tiền (VNĐ)" 
                           min="10000" max="10000000" step="10000" style="padding:0.75rem; font-size:1.1rem;">
                    <div style="display:flex; gap:0.5rem; margin-top:0.5rem; flex-wrap:wrap;">
                        <button type="button" class="btn btn-outline" style="padding:4px 12px; font-size:0.8rem;" onclick="setAmount(50000)">50K</button>
                        <button type="button" class="btn btn-outline" style="padding:4px 12px; font-size:0.8rem;" onclick="setAmount(100000)">100K</button>
                        <button type="button" class="btn btn-outline" style="padding:4px 12px; font-size:0.8rem;" onclick="setAmount(200000)">200K</button>
                        <button type="button" class="btn btn-outline" style="padding:4px 12px; font-size:0.8rem;" onclick="setAmount(500000)">500K</button>
                    </div>
                </div>

                <!-- QR Preview (hiện sau khi nhập amount) -->
                <div id="qrSection" style="display:none; margin-bottom:1.5rem;">
                    <h4 style="margin:0 0 1rem;"><i class="fas fa-qrcode"></i> Quét mã QR để chuyển khoản</h4>
                    <div style="text-align:center; margin-bottom:1rem;">
                        <div style="background:#fff; display:inline-block; padding:12px; border-radius:16px; box-shadow:0 4px 20px rgba(0,0,0,0.1);">
                            <img id="qrCodeImage" src="" alt="QR Code" style="width:220px; height:220px; display:block;">
                        </div>
                    </div>
                    
                    <div style="background:var(--card-bg); border:1px solid var(--border-color); border-radius:var(--radius-sm); padding:1rem;">
                        <div style="display:flex; align-items:center; gap:0.75rem; margin-bottom:0.75rem; padding-bottom:0.5rem; border-bottom:1px solid var(--border-color);">
                            <div style="background:linear-gradient(135deg, #d4232b, #e53935); color:#fff; padding:4px 10px; border-radius:4px; font-weight:700; font-size:0.75rem;">TECHCOMBANK</div>
                            <small style="color:var(--text-muted);">Ngân hàng TMCP Kỹ Thương VN</small>
                        </div>
                        <div style="display:flex; justify-content:space-between; margin-bottom:0.4rem;">
                            <span style="color:var(--text-muted); font-size:0.85rem;">STK</span>
                            <div><strong>19036785007013</strong> <button onclick="copyText('19036785007013')" style="background:none;border:none;cursor:pointer;color:var(--primary);"><i class="fas fa-copy"></i></button></div>
                        </div>
                        <div style="display:flex; justify-content:space-between; margin-bottom:0.4rem;">
                            <span style="color:var(--text-muted); font-size:0.85rem;">Chủ TK</span>
                            <strong>PHAN QUANG THUAT</strong>
                        </div>
                        <div style="display:flex; justify-content:space-between; margin-bottom:0.4rem;">
                            <span style="color:var(--text-muted); font-size:0.85rem;">Số tiền</span>
                            <strong style="color:var(--success);" id="displayAmount"></strong>
                        </div>
                        <div style="display:flex; justify-content:space-between; align-items:center;">
                            <span style="color:var(--text-muted); font-size:0.85rem;">Nội dung CK</span>
                            <div>
                                <strong id="transferContent" style="color:#ef4444;"></strong>
                                <button onclick="copyText(document.getElementById('transferContent').textContent)" style="background:none;border:none;cursor:pointer;color:var(--primary);"><i class="fas fa-copy"></i></button>
                            </div>
                        </div>
                    </div>

                    <div style="margin-top:1rem; padding:0.75rem; background:#fef3c7; border-radius:var(--radius-sm); font-size:0.85rem; color:#92400e;">
                        <i class="fas fa-exclamation-triangle"></i> 
                        <strong>Quan trọng:</strong> Ghi đúng nội dung chuyển khoản để Admin xác nhận nhanh nhất.
                    </div>
                </div>

                <!-- Submit -->
                <div style="display:flex; gap:1rem; justify-content:flex-end; margin-top:1rem;">
                    <a href="<?= BASE_URL ?>/wallet" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Quay lại</a>
                    <button class="btn btn-primary" id="confirmDepositBtn" onclick="submitDeposit()" disabled>
                        <i class="fas fa-check-circle"></i> Đã chuyển khoản xong
                    </button>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<script>
function setAmount(val) {
    document.getElementById('depositAmount').value = val;
    updateQR();
}

function copyText(text) {
    navigator.clipboard.writeText(text);
    alert('Đã copy: ' + text);
}

// QR + bank info update when amount changes
document.getElementById('depositAmount')?.addEventListener('input', updateQR);

function updateQR() {
    const amount = parseInt(document.getElementById('depositAmount').value) || 0;
    const qrSection = document.getElementById('qrSection');
    const confirmBtn = document.getElementById('confirmDepositBtn');
    
    if (amount >= 10000) {
        const content = 'NAP' + <?= $_SESSION['user_id'] ?>;
        const qrUrl = `https://img.vietqr.io/image/TCB-19036785007013-compact.png?amount=${amount}&addInfo=${content}&accountName=PHAN%20QUANG%20THUAT`;
        
        document.getElementById('qrCodeImage').src = qrUrl;
        document.getElementById('displayAmount').textContent = new Intl.NumberFormat('vi-VN').format(amount) + 'đ';
        document.getElementById('transferContent').textContent = content;
        
        qrSection.style.display = 'block';
        confirmBtn.disabled = false;
    } else {
        qrSection.style.display = 'none';
        confirmBtn.disabled = true;
    }
}

function submitDeposit() {
    const amount = parseInt(document.getElementById('depositAmount').value) || 0;
    if (amount < 10000) return alert('Số tiền tối thiểu 10,000đ');
    
    const btn = document.getElementById('confirmDepositBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang gửi...';
    
    fetch('<?= BASE_URL ?>/wallet/createDeposit', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'same-origin',
        body: JSON.stringify({
            amount: amount,
            transfer_note: 'NAP' + <?= $_SESSION['user_id'] ?>
        })
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            document.getElementById('depositForm').innerHTML = `
                <div style="text-align:center; padding:2rem;">
                    <i class="fas fa-check-circle" style="font-size:3.5rem; color:var(--success); margin-bottom:1rem;"></i>
                    <h2>Đã gửi yêu cầu nạp tiền!</h2>
                    <p style="color:var(--text-secondary);">${res.message}</p>
                    <a href="<?= BASE_URL ?>/wallet" class="btn btn-primary" style="margin-top:1rem;"><i class="fas fa-wallet"></i> Về ví</a>
                </div>
            `;
        } else {
            alert(res.error);
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check-circle"></i> Đã chuyển khoản xong';
        }
    })
    .catch(err => {
        alert('Lỗi: ' + err.message);
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check-circle"></i> Đã chuyển khoản xong';
    });
}
</script>
