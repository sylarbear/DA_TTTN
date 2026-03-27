<!-- Membership Pro Page -->
<section class="page-header">
    <div class="container">
        <h1><i class="fas fa-crown"></i> Nâng cấp Pro</h1>
        <p style="color:var(--text-secondary);">Mở khóa toàn bộ tính năng học tập nâng cao</p>
    </div>
</section>

<!-- Current Status -->
<?php if ($isPro): ?>
<section style="padding:1rem 0;">
    <div class="container">
        <div class="pro-status-banner">
            <div class="pro-badge-large">
                <i class="fas fa-crown"></i> PRO
            </div>
            <div>
                <h3>Bạn đang là hội viên Pro!</h3>
                <p>Hết hạn: <?= $user['membership_expired_at'] ? date('d/m/Y', strtotime($user['membership_expired_at'])) : 'Không giới hạn' ?></p>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Features Comparison -->
<section style="padding:2rem 0;">
    <div class="container">
        <h2 style="text-align:center; margin-bottom:0.5rem;"><i class="fas fa-table"></i> So sánh gói</h2>
        <p style="text-align:center; color:var(--text-muted); margin-bottom:2rem;">Chọn gói phù hợp với nhu cầu học tập của bạn</p>

        <div class="comparison-table">
            <table>
                <thead>
                    <tr>
                        <th>Tính năng</th>
                        <th>Free</th>
                        <th class="pro-col"><i class="fas fa-crown"></i> Pro</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td>Xem chủ đề & từ vựng</td><td><i class="fas fa-check text-success"></i></td><td class="pro-col"><i class="fas fa-check text-success"></i></td></tr>
                    <tr><td>Học bài (3 topic đầu)</td><td><i class="fas fa-check text-success"></i></td><td class="pro-col"><i class="fas fa-check text-success"></i></td></tr>
                    <tr><td>Làm bài test Quiz</td><td><i class="fas fa-check text-success"></i></td><td class="pro-col"><i class="fas fa-check text-success"></i></td></tr>
                    <tr><td>Dashboard tiến độ</td><td><i class="fas fa-check text-success"></i></td><td class="pro-col"><i class="fas fa-check text-success"></i></td></tr>
                    <tr><td>Mở khóa tất cả khóa học</td><td><i class="fas fa-times text-danger"></i></td><td class="pro-col"><i class="fas fa-check text-success"></i></td></tr>
                    <tr><td>Bài test Listening</td><td><i class="fas fa-times text-danger"></i></td><td class="pro-col"><i class="fas fa-check text-success"></i></td></tr>
                    <tr><td>Bài test Reading</td><td><i class="fas fa-times text-danger"></i></td><td class="pro-col"><i class="fas fa-check text-success"></i></td></tr>
                    <tr><td>Luyện nói (Speaking)</td><td><i class="fas fa-times text-danger"></i></td><td class="pro-col"><i class="fas fa-check text-success"></i></td></tr>
                    <tr><td>Hỗ trợ ưu tiên</td><td><i class="fas fa-times text-danger"></i></td><td class="pro-col"><i class="fas fa-check text-success"></i></td></tr>
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- Pricing Cards -->
<section style="padding:2rem 0;">
    <div class="container">
        <h2 style="text-align:center; margin-bottom:2rem;"><i class="fas fa-tags"></i> Bảng giá</h2>
        <div class="pricing-grid">
            <?php foreach ($plans as $plan): ?>
            <div class="pricing-card <?= $plan['is_popular'] ? 'popular' : '' ?>">
                <?php if ($plan['is_popular']): ?>
                    <div class="popular-badge">PHỔ BIẾN NHẤT</div>
                <?php endif; ?>
                <div class="pricing-header">
                    <h3><?= htmlspecialchars($plan['name']) ?></h3>
                    <div class="pricing-price">
                        <span class="price-amount"><?= number_format($plan['price']) ?></span>
                        <span class="price-currency">VNĐ</span>
                    </div>
                    <p class="pricing-desc"><?= htmlspecialchars($plan['description']) ?></p>
                </div>
                <div class="pricing-features">
                    <?php foreach (explode('|', $plan['features']) as $feature): ?>
                        <div class="feature-item">
                            <i class="fas fa-check-circle"></i>
                            <span><?= htmlspecialchars($feature) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="pricing-footer">
                    <button class="btn btn-primary btn-block" onclick="showPayment(<?= $plan['id'] ?>, '<?= htmlspecialchars($plan['name']) ?>', <?= $plan['price'] ?>)">
                        <i class="fas fa-shopping-cart"></i> Chọn gói này
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Activation Code Section -->
<section style="padding:2rem 0 3rem;">
    <div class="container">
        <div class="activation-card">
            <h3><i class="fas fa-key"></i> Kích hoạt bằng mã</h3>
            <p>Bạn đã có mã kích hoạt? Nhập vào đây để nâng cấp ngay.</p>
            <div class="activation-form">
                <input type="text" id="activationCode" placeholder="Nhập mã kích hoạt (VD: PRO1-FREE-TRIAL)" class="input-lg" autocomplete="off">
                <button class="btn btn-primary btn-lg" id="activateBtn" onclick="activateCode()">
                    <i class="fas fa-bolt"></i> Kích hoạt
                </button>
            </div>
            <div id="activationResult" style="margin-top:1rem;"></div>
        </div>
    </div>
</section>

<!-- Order History -->
<?php if (!empty($orders)): ?>
<section style="padding:0 0 3rem;">
    <div class="container">
        <div class="section-card">
            <h3><i class="fas fa-history"></i> Lịch sử nâng cấp</h3>
            <div class="progress-table">
                <table>
                    <thead>
                        <tr>
                            <th>Gói</th>
                            <th>Mã</th>
                            <th>Ngày kích hoạt</th>
                            <th>Hết hạn</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?= htmlspecialchars($order['plan_name']) ?></td>
                            <td><code><?= htmlspecialchars($order['activation_code']) ?></code></td>
                            <td><?= date('d/m/Y H:i', strtotime($order['activated_at'])) ?></td>
                            <td><?= date('d/m/Y', strtotime($order['expired_at'])) ?></td>
                            <td><span class="answer-badge correct">Hoàn tất</span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Payment Modal -->
<div class="modal" id="paymentModal">
    <div class="modal-overlay" onclick="closePayment()"></div>
    <div class="modal-content" style="max-width:550px;">
        <div class="modal-header">
            <h2 id="paymentTitle">Thanh toán</h2>
        </div>
        <div class="payment-body">
            <!-- Step 1: Choose method -->
            <div id="paymentStep1">
                <div class="payment-summary">
                    <div class="payment-plan-name" id="paymentPlanName"></div>
                    <div class="payment-amount" id="paymentAmount"></div>
                </div>
                <h4 style="margin:1.5rem 0 1rem;">Chọn phương thức thanh toán:</h4>
                <div class="payment-methods">
                    <div class="payment-method" onclick="selectPayment('bank')">
                        <i class="fas fa-university"></i>
                        <span>Chuyển khoản ngân hàng</span>
                    </div>
                    <div class="payment-method" onclick="selectPayment('momo')">
                        <i class="fas fa-wallet"></i>
                        <span>Ví MoMo</span>
                    </div>
                    <div class="payment-method" onclick="selectPayment('qr')">
                        <i class="fas fa-qrcode"></i>
                        <span>Quét mã QR</span>
                    </div>
                </div>
            </div>
            <!-- Step 2: Payment details -->
            <div id="paymentStep2" style="display:none;">
                <div class="qr-payment-area">
                    <div class="qr-code-wrapper">
                        <img src="<?= BASE_URL ?>/images/qr_payment.png" alt="QR Code Thanh toán" class="qr-image">
                    </div>
                    <div class="bank-info">
                        <div class="bank-detail"><strong>Ngân hàng:</strong> Vietcombank</div>
                        <div class="bank-detail"><strong>Số TK:</strong> 1234 5678 9012</div>
                        <div class="bank-detail"><strong>Chủ TK:</strong> ENGLISH LEARNING</div>
                        <div class="bank-detail"><strong>Nội dung CK:</strong> <code id="transferNote">EM-PRO-</code></div>
                        <div class="bank-detail"><strong>Số tiền:</strong> <span id="transferAmount" style="color:var(--accent-green); font-weight:700;"></span></div>
                    </div>
                    <div class="payment-note">
                        <i class="fas fa-info-circle"></i>
                        Sau khi chuyển khoản, hệ thống sẽ tự động kích hoạt trong vòng <strong>5 phút</strong>.
                        Hoặc bạn có thể nhập mã kích hoạt bên dưới.
                    </div>
                </div>
                <div class="activation-form" style="margin-top:1rem;">
                    <input type="text" id="modalActivationCode" placeholder="Nhập mã kích hoạt" class="input-lg">
                    <button class="btn btn-primary btn-lg" onclick="activateFromModal()">
                        <i class="fas fa-bolt"></i> Kích hoạt
                    </button>
                </div>
                <div id="modalResult" style="margin-top:0.5rem;"></div>
                <button class="btn btn-outline" style="margin-top:1rem; width:100%;" onclick="backToStep1()">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let selectedPlanId = null;
let selectedPlanPrice = 0;

function showPayment(planId, planName, price) {
    selectedPlanId = planId;
    selectedPlanPrice = price;
    document.getElementById('paymentPlanName').textContent = planName;
    document.getElementById('paymentAmount').textContent = new Intl.NumberFormat('vi-VN').format(price) + ' VNĐ';
    document.getElementById('paymentStep1').style.display = 'block';
    document.getElementById('paymentStep2').style.display = 'none';
    document.getElementById('paymentModal').classList.add('active');
}

function closePayment() {
    document.getElementById('paymentModal').classList.remove('active');
}

function selectPayment(method) {
    document.getElementById('paymentStep1').style.display = 'none';
    document.getElementById('paymentStep2').style.display = 'block';
    document.getElementById('transferNote').textContent = 'EM-PRO-<?= $_SESSION['user_id'] ?? 0 ?>';
    document.getElementById('transferAmount').textContent = new Intl.NumberFormat('vi-VN').format(selectedPlanPrice) + ' VNĐ';
}

function backToStep1() {
    document.getElementById('paymentStep1').style.display = 'block';
    document.getElementById('paymentStep2').style.display = 'none';
}

function activateCode() {
    const code = document.getElementById('activationCode').value.trim();
    if (!code) { alert('Vui lòng nhập mã kích hoạt.'); return; }
    doActivate(code, 'activationResult', 'activateBtn');
}

function activateFromModal() {
    const code = document.getElementById('modalActivationCode').value.trim();
    if (!code) { alert('Vui lòng nhập mã kích hoạt.'); return; }
    doActivate(code, 'modalResult');
}

function doActivate(code, resultId, btnId) {
    const resultEl = document.getElementById(resultId);
    resultEl.innerHTML = '<div style="color:var(--text-muted);"><i class="fas fa-spinner fa-spin"></i> Đang xử lý...</div>';

    if (btnId) {
        const btn = document.getElementById(btnId);
        btn.disabled = true;
    }

    fetch('<?= BASE_URL ?>/membership/activate', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'same-origin',
        body: JSON.stringify({ code: code })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            resultEl.innerHTML = `
                <div style="background:rgba(67,233,123,0.1); border:1px solid var(--success); border-radius:var(--radius-sm); padding:1rem; text-align:center;">
                    <i class="fas fa-check-circle fa-2x" style="color:var(--success);"></i>
                    <h4 style="margin:0.5rem 0;">${data.message}</h4>
                    <p>Hết hạn: ${data.expired_at}</p>
                    <button class="btn btn-primary" onclick="location.reload()" style="margin-top:0.5rem;">
                        <i class="fas fa-sync"></i> Tải lại trang
                    </button>
                </div>
            `;
        } else {
            resultEl.innerHTML = '<div style="color:var(--error);"><i class="fas fa-exclamation-circle"></i> ' + data.error + '</div>';
        }
        if (btnId) document.getElementById(btnId).disabled = false;
    })
    .catch(err => {
        resultEl.innerHTML = '<div style="color:var(--error);"><i class="fas fa-exclamation-circle"></i> Lỗi kết nối. Thử lại.</div>';
        if (btnId) document.getElementById(btnId).disabled = false;
    });
}
</script>
