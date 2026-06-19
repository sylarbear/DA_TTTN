<?php
function planDisplayData($plan) {
    $duration = (int)($plan['duration_months'] ?? 0);
    $price = (int)($plan['price'] ?? 0);
    $monthLabel = $duration > 0 ? $duration . ' tháng' : 'Pro';
    $saving = '';
    if ($duration === 3) $saving = 'Tiết kiệm 17% so với gói 1 tháng';
    if ($duration === 12) $saving = $price <= 350000 ? 'Gói Pro 12 tháng, tiết kiệm 42%' : 'Tiết kiệm 33%, đề xuất dài hạn';

    return [
        'name' => $duration > 0 ? 'Pro ' . $monthLabel : htmlspecialchars($plan['name'] ?? 'EngPath Pro'),
        'description' => $saving ?: 'Trải nghiệm đầy đủ các tính năng học tập trong ' . $monthLabel,
        'features' => [
            'Mở khóa tất cả khóa học',
            'Luyện nói với AI chấm điểm',
            'Bài test Listening và Reading',
            'Theo dõi tiến độ học tập',
            $duration >= 12 ? 'Ưu tiên hỗ trợ khi cần' : null
        ]
    ];
}
?>

<section class="learn-page-hero pro-hero">
    <div class="container learn-hero-grid">
        <div>
            <span class="busuu-label">EngPath Pro</span>
            <h1>Mở khóa toàn bộ lộ trình học tiếng Anh.</h1>
            <p>Pro giúp người học truy cập đầy đủ khóa học, bài test nâng cao, luyện speaking AI và theo dõi tiến độ học tập sâu hơn.</p>
            <div class="learn-hero-actions">
                <a href="#pricing" class="busuu-primary-btn">Xem gói Pro</a>
                <a href="#activation" class="busuu-secondary-btn">Nhập mã kích hoạt</a>
            </div>
        </div>

        <div class="pro-dashboard-preview">
            <div class="pro-badge-large"><i class="fas fa-crown"></i> PRO</div>
            <h3><?= $isPro ? 'Bạn đang dùng EngPath Pro' : 'Nâng cấp khi sẵn sàng' ?></h3>
            <p><?= $isPro && !empty($user['membership_expired_at']) ? 'Hết hạn: ' . date('d/m/Y', strtotime($user['membership_expired_at'])) : 'Mở toàn bộ nội dung học, speaking AI và các bài test nâng cao.' ?></p>
            <div class="pro-mini-metrics">
                <div><strong>100%</strong><span>Courses</span></div>
                <div><strong>AI</strong><span>Speaking</span></div>
                <div><strong>24/7</strong><span>Access</span></div>
            </div>
        </div>
    </div>
</section>

<section class="eng-section pro-compare-section">
    <div class="container">
        <div class="busuu-section-heading compact">
            <span>Plan comparison</span>
            <h2>Free để bắt đầu, Pro để học trọn vẹn.</h2>
        </div>

        <div class="plan-compare-grid polished-compare">
            <div class="compare-card">
                <span class="plan-kicker">Free</span>
                <h3>Dùng thử nền tảng</h3>
                <p>Phù hợp khi bạn mới bắt đầu làm quen với EngPath.</p>
                <ul>
                    <li><i class="fas fa-check"></i> Xem chủ đề và từ vựng</li>
                    <li><i class="fas fa-check"></i> Học các topic đầu</li>
                    <li><i class="fas fa-check"></i> Làm quiz cơ bản</li>
                    <li><i class="fas fa-check"></i> Xem dashboard tiến độ</li>
                </ul>
            </div>
            <div class="compare-card highlighted">
                <span class="plan-kicker">Pro</span>
                <h3>Mở khóa đầy đủ</h3>
                <p>Phù hợp khi bạn muốn luyện đủ từ vựng, test, speaking và theo dõi sâu hơn.</p>
                <ul>
                    <li><i class="fas fa-check"></i> Tất cả khóa học</li>
                    <li><i class="fas fa-check"></i> Listening và Reading test</li>
                    <li><i class="fas fa-check"></i> Speaking AI feedback</li>
                    <li><i class="fas fa-check"></i> Hỗ trợ ưu tiên</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<section class="eng-section pricing-section polished-pricing" id="pricing">
    <div class="container">
        <div class="busuu-section-heading compact">
            <span>Bảng giá</span>
            <h2>Chọn gói phù hợp với thời gian học.</h2>
        </div>

        <div class="pricing-grid pricing-grid-polished">
            <?php foreach ($plans as $plan): ?>
                <?php $display = planDisplayData($plan); ?>
                <article class="pricing-card polished-price-card <?= !empty($plan['is_popular']) ? 'popular' : '' ?>">
                    <?php if (!empty($plan['is_popular'])): ?>
                        <div class="popular-badge">Phổ biến nhất</div>
                    <?php endif; ?>
                    <div class="pricing-header">
                        <h3><?= htmlspecialchars($display['name']) ?></h3>
                        <div class="pricing-price">
                            <span class="price-amount"><?= number_format((int)$plan['price']) ?></span>
                            <span class="price-currency">VNĐ</span>
                        </div>
                        <p class="pricing-desc"><?= htmlspecialchars($display['description']) ?></p>
                    </div>
                    <div class="pricing-features">
                        <?php foreach ($display['features'] as $feature): ?>
                            <?php if (!$feature) continue; ?>
                            <div class="feature-item">
                                <i class="fas fa-check-circle"></i>
                                <span><?= htmlspecialchars($feature) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="pricing-footer">
                        <button class="busuu-primary-btn price-action" onclick="showPayment(<?= (int) $plan['id'] ?>, '<?= htmlspecialchars($display['name'], ENT_QUOTES) ?>', <?= (int) $plan['price'] ?>)">
                            Chọn gói này
                        </button>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="eng-section activation-section" id="activation">
    <div class="container">
        <div class="activation-card polished-activation">
            <div>
                <span class="busuu-label">Mã kích hoạt</span>
                <h3>Kích hoạt Pro bằng mã có sẵn</h3>
                <p>Nếu bạn đã được cấp mã kích hoạt, nhập mã để nâng cấp tài khoản ngay.</p>
            </div>
            <div>
                <div class="activation-form">
                    <input type="text" id="activationCode" placeholder="Nhập mã kích hoạt" class="input-lg" autocomplete="off">
                    <button class="btn btn-primary btn-lg" id="activateBtn" onclick="activateCode()">
                        <i class="fas fa-bolt"></i> Kích hoạt
                    </button>
                </div>
                <div id="activationResult"></div>
            </div>
        </div>
    </div>
</section>

<?php if (!empty($orders)): ?>
<section class="eng-section">
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
                        <?php
                            $statusLabels = ['pending'=>'Đang chờ','completed'=>'Hoàn tất','cancelled'=>'Đã hủy'];
                            $status = $order['status'] ?? 'completed';
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($order['plan_name'] ?? '') ?></td>
                            <td><?= htmlspecialchars($order['activation_code'] ?? '-') ?></td>
                            <td><?= !empty($order['activated_at']) ? date('d/m/Y H:i', strtotime($order['activated_at'])) : '-' ?></td>
                            <td><?= !empty($order['expired_at']) ? date('d/m/Y', strtotime($order['expired_at'])) : '-' ?></td>
                            <td><span class="status-pill status-<?= htmlspecialchars($status) ?>"><?= $statusLabels[$status] ?? htmlspecialchars($status) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<div class="modal" id="paymentModal">
    <div class="modal-overlay" onclick="closePayment()"></div>
    <div class="modal-content payment-modal">
        <div class="modal-header">
            <h2><i class="fas fa-wallet"></i> Thanh toán bằng ví</h2>
            <button onclick="closePayment()" class="modal-close">&times;</button>
        </div>
        <div class="payment-body">
            <div class="payment-plan-box">
                <h3 id="paymentPlanName"></h3>
                <div id="paymentAmount" class="payment-amount"></div>
            </div>

            <div class="wallet-row">
                <span><i class="fas fa-wallet"></i> Số dư ví</span>
                <strong id="walletBalanceDisplay"><?= number_format($user['balance'] ?? 0) ?>đ</strong>
                <div id="balanceStatus"></div>
            </div>

            <div id="paymentAction"></div>

            <div class="modal-activation">
                <h4><i class="fas fa-key"></i> Có mã kích hoạt?</h4>
                <div class="activation-form">
                    <input type="text" id="modalActivationCode" placeholder="Nhập mã kích hoạt" class="input-lg" autocomplete="off">
                    <button class="btn btn-primary" onclick="activateFromModal()">Kích hoạt</button>
                </div>
                <div id="modalResult"></div>
            </div>
        </div>
    </div>
</div>

<script>
let selectedPlanId = null;
let selectedPlanPrice = 0;
const userBalance = <?= intval($user['balance'] ?? 0) ?>;

function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

function showPayment(planId, planName, price) {
    selectedPlanId = planId;
    selectedPlanPrice = price;
    document.getElementById('paymentPlanName').textContent = planName;
    document.getElementById('paymentAmount').textContent = new Intl.NumberFormat('vi-VN').format(price) + ' VNĐ';

    const actionEl = document.getElementById('paymentAction');
    const statusEl = document.getElementById('balanceStatus');

    if (userBalance >= price) {
        statusEl.innerHTML = '<span class="status-pill status-completed">Đủ tiền</span>';
        const remaining = userBalance - price;
        actionEl.innerHTML = `
            <p class="payment-note">Sau khi mua, số dư còn lại: <strong>${new Intl.NumberFormat('vi-VN').format(remaining)}đ</strong></p>
            <button class="btn btn-primary btn-lg btn-block" id="buyBtn" onclick="buyWithWallet()">
                <i class="fas fa-shopping-cart"></i> Thanh toán bằng ví
            </button>
            <div id="buyResult"></div>
        `;
    } else {
        const shortage = price - userBalance;
        statusEl.innerHTML = '<span class="status-pill status-cancelled">Thiếu tiền</span>';
        actionEl.innerHTML = `
            <div class="payment-warning">
                Ví không đủ. Cần nạp thêm <strong>${new Intl.NumberFormat('vi-VN').format(shortage)}đ</strong>.
            </div>
            <a href="<?= BASE_URL ?>/wallet/deposit" class="btn btn-primary btn-lg btn-block">
                <i class="fas fa-plus-circle"></i> Nạp tiền vào ví
            </a>
        `;
    }

    document.getElementById('paymentModal').classList.add('active');
}

function closePayment() {
    document.getElementById('paymentModal').classList.remove('active');
}

function buyWithWallet() {
    const btn = document.getElementById('buyBtn');
    const resultEl = document.getElementById('buyResult');

    if (!confirm('Xác nhận thanh toán ' + new Intl.NumberFormat('vi-VN').format(selectedPlanPrice) + 'đ từ ví?')) return;

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';

    fetch('<?= BASE_URL ?>/membership/createOrder', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'same-origin',
        body: JSON.stringify({ plan_id: selectedPlanId })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            resultEl.innerHTML = `
                <div class="payment-success">
                    <i class="fas fa-crown"></i>
                    <h3>${escapeHtml(data.message)}</h3>
                    <p>Hết hạn: ${escapeHtml(data.expired_at)}</p>
                    <button class="btn btn-primary" onclick="location.reload()">Bắt đầu dùng Pro</button>
                </div>
            `;
            btn.style.display = 'none';
        } else {
            resultEl.innerHTML = '<div class="payment-error">' + escapeHtml(data.error || 'Không thể thanh toán') + '</div>';
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-shopping-cart"></i> Thanh toán bằng ví';
        }
    })
    .catch(() => {
        resultEl.innerHTML = '<div class="payment-error">Lỗi kết nối.</div>';
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-shopping-cart"></i> Thanh toán bằng ví';
    });
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
    resultEl.innerHTML = '<div class="payment-note"><i class="fas fa-spinner fa-spin"></i> Đang xử lý...</div>';
    if (btnId) document.getElementById(btnId).disabled = true;

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
                <div class="payment-success compact">
                    <i class="fas fa-check-circle"></i>
                    <h4>${escapeHtml(data.message)}</h4>
                    <p>Hết hạn: ${escapeHtml(data.expired_at)}</p>
                    <button class="btn btn-primary" onclick="location.reload()">Tải lại trang</button>
                </div>
            `;
        } else {
            resultEl.innerHTML = '<div class="payment-error">' + escapeHtml(data.error || 'Mã không hợp lệ') + '</div>';
        }
        if (btnId) document.getElementById(btnId).disabled = false;
    })
    .catch(() => {
        resultEl.innerHTML = '<div class="payment-error">Lỗi kết nối.</div>';
        if (btnId) document.getElementById(btnId).disabled = false;
    });
}
</script>
