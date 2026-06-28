<?php
function planDisplayData($plan)
{
    $duration = (int)($plan['duration_months'] ?? 0);
    $price = (int)($plan['price'] ?? 0);
    $isLifetime = $duration === -1;
    $monthLabel = $isLifetime ? 'Lifetime' : ($duration > 0 ? $duration . ' tháng' : 'Pro');
    $saving = '';
    if ($duration === 3) {
        $saving = 'Tiết kiệm 17% so với gói 1 tháng';
    }
    if ($duration === 12) {
        $saving = $price <= 350000 ? 'Gói Pro 12 tháng, tiết kiệm 42%' : 'Tiết kiệm 33%, đề xuất dài hạn';
    }
    if ($isLifetime) {
        $saving = 'Học trọn đời không giới hạn, tiết kiệm nhất';
    }

    $baseFeatures = [
        'Mở khóa tất cả khóa học',
        'Luyện nói với AI chấm điểm',
        'Bài test Listening và Reading',
        'Theo dõi tiến độ học tập',
    ];

    if ($duration >= 12) {
        $baseFeatures[] = 'Ưu tiên hỗ trợ';
    }
    if ($isLifetime) {
        $baseFeatures[] = 'Cập nhật nội dung mới vĩnh viễn';
    }

    return [
        'name' => $isLifetime ? 'Pro Lifetime' : ($duration > 0 ? 'Pro ' . $monthLabel : htmlspecialchars($plan['name'] ?? 'EngPath Pro')),
        'description' => $saving ?: 'Trải nghiệm đầy đủ các tính năng học tập trong ' . $monthLabel,
        'features' => array_values(array_filter($baseFeatures)),
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
                        <button class="busuu-primary-btn price-action"
                                onclick="showPayment(<?= (int)$plan['id'] ?>, '<?= htmlspecialchars($display['name'], ENT_QUOTES) ?>', <?= (int)$plan['price'] ?>)"
                                <?= $hasPending ? 'disabled' : '' ?>>
                            <?= $hasPending ? 'Đang có đơn chờ duyệt' : 'Chọn gói này' ?>
                        </button>
                    </div>
                </article>
            <?php endforeach; ?>
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
                            <th>Ngày tạo</th>
                            <th>Hết hạn</th>
                            <th>Phương thức</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($orders as $order): ?>
                        <?php
                        $statusLabels = ['pending' => 'Đang chờ', 'completed' => 'Hoàn tất', 'cancelled' => 'Đã hủy'];
                        $status = $order['status'] ?? 'completed';
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($order['plan_name'] ?? '') ?></td>
                            <td><?= !empty($order['activated_at']) ? date('d/m/Y H:i', strtotime($order['activated_at'])) : '-' ?></td>
                            <td><?= !empty($order['expired_at']) ? date('d/m/Y', strtotime($order['expired_at'])) : '-' ?></td>
                            <td>Chuyển khoản</td>
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

<!-- QR Payment Modal -->
<div class="modal" id="paymentModal">
    <div class="modal-overlay" onclick="closePayment()"></div>
    <div class="modal-content payment-modal" style="max-width:540px;">
        <div class="modal-header">
            <h2><i class="fas fa-qrcode"></i> Chuyển khoản ngân hàng</h2>
            <button onclick="closePayment()" class="modal-close">&times;</button>
        </div>
        <div class="payment-body">
            <div class="payment-plan-box">
                <h3 id="paymentPlanName"></h3>
                <div id="paymentAmount" class="payment-amount"></div>
            </div>

            <div class="qr-payment-area" style="text-align:center; margin:1rem 0;">
                <div class="qr-code-wrapper" style="display:inline-block; padding:1rem; background:#fff; border-radius:12px; border:1px solid #e0e7f0;">
                    <img src="<?= BASE_URL ?>/images/qr_payment.png"
                         alt="QR Code"
                         style="width:200px; height:200px; object-fit:contain;">
                </div>
            </div>

            <div class="bank-info" style="background:#f5f7fa; border-radius:12px; padding:1rem; margin-bottom:1rem;">
                <p style="margin:0 0 0.5rem; font-weight:700;">Thông tin chuyển khoản</p>
                <p style="margin:0.2rem 0; font-size:0.9rem;"><strong>Ngân hàng:</strong> Techcombank</p>
                <p style="margin:0.2rem 0; font-size:0.9rem;"><strong>Số TK:</strong> 19036785007013</p>
                <p style="margin:0.2rem 0; font-size:0.9rem;"><strong>Chủ TK:</strong> PHAN QUANG THUAT</p>
            </div>

            <div class="form-group" style="margin-bottom:1rem;">
                <label style="font-weight:700; display:block; margin-bottom:0.4rem;">
                    <i class="fas fa-edit"></i> Nội dung chuyển khoản <span style="color:#f44;">*</span>
                </label>
                <input type="text" id="transferNote" class="form-input"
                       placeholder="EMPRO <?= $_SESSION['user_id'] ?? '' ?> GOI..."
                       style="font-family:monospace; font-size:1rem; padding:0.7rem; width:100%;"
                       autocomplete="off">
                <small style="color:#888; display:block; margin-top:0.3rem;">
                    Định dạng: <code style="background:#f0f4ff; padding:0.1rem 0.4rem; border-radius:3px;">EMPRO {Mã ND} GOI{Mã gói}</code>
                </small>
            </div>

            <div id="paymentAction">
                <button class="btn btn-primary btn-lg btn-block" id="submitOrderBtn" onclick="submitOrder()">
                    <i class="fas fa-paper-plane"></i> Xác nhận đã chuyển khoản
                </button>
                <div id="orderResult" style="margin-top:0.8rem;"></div>
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
    document.getElementById('transferNote').value = 'EMPRO <?= $_SESSION['user_id'] ?? '' ?> GOI' + planId;
    document.getElementById('orderResult').innerHTML = '';
    document.getElementById('submitOrderBtn').disabled = false;
    document.getElementById('submitOrderBtn').innerHTML = '<i class="fas fa-paper-plane"></i> Xác nhận đã chuyển khoản';
    document.getElementById('paymentModal').classList.add('active');
}

function closePayment() {
    document.getElementById('paymentModal').classList.remove('active');
}

function submitOrder() {
    const note = document.getElementById('transferNote').value.trim();
    if (!note) {
        document.getElementById('orderResult').innerHTML =
            '<div class="alert alert-error">Vui lòng nhập nội dung chuyển khoản.</div>';
        return;
    }

    const btn = document.getElementById('submitOrderBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';

    fetch('<?= BASE_URL ?>/membership/createOrder', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'same-origin',
        body: JSON.stringify({ plan_id: selectedPlanId, transfer_note: note })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            document.getElementById('orderResult').innerHTML = `
                <div class="alert alert-success" style="background:#ecfdf5; border:1px solid #a7f3d0; color:#059669; padding:1rem; border-radius:8px;">
                    <i class="fas fa-check-circle"></i> <strong>${data.message}</strong>
                </div>
            `;
            btn.style.display = 'none';
            setTimeout(() => location.reload(), 3000);
        } else {
            document.getElementById('orderResult').innerHTML =
                '<div class="alert alert-error">' + (data.error || 'Có lỗi xảy ra') + '</div>';
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-paper-plane"></i> Xác nhận đã chuyển khoản';
        }
    })
    .catch(() => {
        document.getElementById('orderResult').innerHTML =
            '<div class="alert alert-error">Lỗi kết nối. Vui lòng thử lại.</div>';
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-paper-plane"></i> Xác nhận đã chuyển khoản';
    });
}
</script>
