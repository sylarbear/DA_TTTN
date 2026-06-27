<?php
$adminActive = 'orders';
$adminTitle = 'Quản lý Đơn nâng cấp';
$adminSubtitle = 'Duyệt đơn chuyển khoản QR và kích hoạt gói Pro cho học viên.';
require APP_PATH . '/views/admin/_nav.php';
?>

<section style="padding:2rem 0;">
    <div class="container">
        <!-- Pending Orders -->
        <?php 
        $pending = array_filter($orders, fn($o) => $o['status'] === 'pending');
        $completed = array_filter($orders, fn($o) => $o['status'] === 'completed');
        $cancelled = array_filter($orders, fn($o) => $o['status'] === 'cancelled');
        ?>

        <?php if (!empty($pending)): ?>
        <div class="section-card" style="border-left:4px solid #f59e0b; margin-bottom:2rem;">
            <h3 style="color:#f59e0b;"><i class="fas fa-clock"></i> Đơn chờ duyệt (<?= count($pending) ?>)</h3>
            <div class="progress-table" style="margin-top:1rem;">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Gói</th>
                            <th>Số tiền</th>
                            <th>Nội dung CK</th>
                            <th>Thời gian</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pending as $order): ?>
                        <tr id="order-row-<?= $order['id'] ?>">
                            <td><strong>#<?= $order['id'] ?></strong></td>
                            <td>
                                <div><strong><?= htmlspecialchars($order['full_name']) ?></strong></div>
                                <small style="color:var(--text-muted);">@<?= htmlspecialchars($order['username']) ?> — <?= htmlspecialchars($order['email']) ?></small>
                            </td>
                            <td><span class="answer-badge" style="background:rgba(99,102,241,0.1); color:var(--primary);"><?= htmlspecialchars($order['plan_name']) ?></span></td>
                            <td style="font-weight:700; color:var(--accent-green);"><?= number_format($order['amount']) ?> VNĐ</td>
                            <td><code style="background:var(--primary-soft); padding:3px 8px; border-radius:4px;"><?= htmlspecialchars($order['transfer_note'] ?? 'N/A') ?></code></td>
                            <td><?= date('d/m/Y H:i', strtotime($order['created_at'] ?? $order['activated_at'] ?? 'now')) ?></td>
                            <td>
                                <div style="display:flex; gap:0.5rem;">
                                    <button class="btn btn-primary" style="padding:6px 16px; font-size:0.85rem;" onclick="approveOrder(<?= $order['id'] ?>, '<?= htmlspecialchars($order['full_name']) ?>')">
                                        <i class="fas fa-check"></i> Duyệt
                                    </button>
                                    <button class="btn btn-outline" style="padding:6px 12px; font-size:0.85rem; color:var(--error); border-color:var(--error);" onclick="rejectOrder(<?= $order['id'] ?>)">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php else: ?>
        <div class="section-card" style="text-align:center; padding:3rem;">
            <i class="fas fa-check-circle fa-3x" style="color:var(--success); margin-bottom:1rem;"></i>
            <h3>Không có đơn chờ duyệt</h3>
            <p style="color:var(--text-muted);">Tất cả đơn đã được xử lý.</p>
        </div>
        <?php endif; ?>

        <!-- Completed & Cancelled Orders -->
        <?php if (!empty($completed) || !empty($cancelled)): ?>
        <div class="section-card">
            <h3><i class="fas fa-history"></i> Lịch sử đơn</h3>
            <div class="progress-table" style="margin-top:1rem;">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Gói</th>
                            <th>Số tiền</th>
                            <th>Phương thức</th>
                            <th>Trạng thái</th>
                            <th>Ngày</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_merge($completed, $cancelled) as $order): ?>
                        <tr>
                            <td>#<?= $order['id'] ?></td>
                            <td><?= htmlspecialchars($order['full_name']) ?> <small>(@<?= htmlspecialchars($order['username']) ?>)</small></td>
                            <td><?= htmlspecialchars($order['plan_name']) ?></td>
                            <td><?= number_format($order['amount']) ?> VNĐ</td>
                            <td>
                                <?php if ($order['payment_method'] === 'qr_transfer'): ?>
                                    <span style="color:var(--primary);"><i class="fas fa-qrcode"></i> QR</span>
                                <?php elseif ($order['payment_method'] === 'casso_auto'): ?>
                                    <span style="color:var(--success);"><i class="fas fa-robot"></i> Casso Auto</span>
                                <?php else: ?>
                                    <span><i class="fas fa-key"></i> Mã kích hoạt</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($order['status'] === 'completed'): ?>
                                    <span class="answer-badge correct">Hoàn tất</span>
                                <?php else: ?>
                                    <span class="answer-badge wrong">Từ chối</span>
                                <?php endif; ?>
                            </td>
                            <td><?= date('d/m/Y H:i', strtotime($order['created_at'] ?? $order['activated_at'] ?? 'now')) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- Simulate Webhook (chỉ hiện trên localhost) -->
        <?php if (strpos(BASE_URL, 'localhost') !== false): ?>
        <div class="section-card" style="border-left:4px solid var(--primary); margin-top:2rem;">
            <h3 style="color:var(--primary);"><i class="fas fa-flask"></i> Test: Giả lập thanh toán tự động</h3>
            <p style="color:var(--text-muted); margin:0.5rem 0 1rem;">Giả lập Casso webhook — nhập nội dung CK và số tiền để test tự động duyệt đơn (chỉ hiện trên localhost).</p>
            
            <div style="display:flex; gap:1rem; flex-wrap:wrap; align-items:flex-end;">
                <div style="flex:1; min-width:200px;">
                    <label style="font-size:0.85rem; font-weight:600; display:block; margin-bottom:4px;">Nội dung chuyển khoản</label>
                    <input type="text" id="simDesc" placeholder="VD: EMPRO 1 GOI1" class="input-lg" style="width:100%;" value="">
                </div>
                <div style="min-width:150px;">
                    <label style="font-size:0.85rem; font-weight:600; display:block; margin-bottom:4px;">Số tiền (VNĐ)</label>
                    <input type="number" id="simAmount" placeholder="50000" class="input-lg" style="width:100%;" value="50000">
                </div>
                <button class="btn btn-primary" style="padding:10px 24px;" onclick="simulateWebhook()">
                    <i class="fas fa-play"></i> Giả lập
                </button>
            </div>
            <div id="simResult" style="margin-top:1rem;"></div>

            <?php if (!empty($pending)): ?>
            <div style="margin-top:1rem; padding-top:1rem; border-top:1px solid var(--border);">
                <p style="font-size:0.85rem; color:var(--text-muted); margin-bottom:0.5rem;"><i class="fas fa-bolt"></i> Giả lập nhanh cho đơn đang chờ:</p>
                <div style="display:flex; gap:0.5rem; flex-wrap:wrap;">
                    <?php foreach ($pending as $order): ?>
                    <button class="btn btn-outline" style="padding:6px 14px; font-size:0.85rem;" 
                        onclick="simulateForOrder('<?= htmlspecialchars($order['transfer_note'] ?? 'EMPRO '.$order['user_id'].' GOI'.$order['plan_id']) ?>', <?= $order['amount'] ?>)">
                        <i class="fas fa-bolt"></i> #<?= $order['id'] ?> — <?= htmlspecialchars($order['transfer_note'] ?? 'N/A') ?> (<?= number_format($order['amount']) ?>đ)
                    </button>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<script>
function approveOrder(id, name) {
    if (!confirm('Duyệt đơn nâng cấp Pro cho "' + name + '"?')) return;
    
    fetch('<?= BASE_URL ?>/admin/approveOrder', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'same-origin',
        body: JSON.stringify({ id: id })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert('✅ ' + data.message);
            location.reload();
        } else {
            alert('❌ ' + data.error);
        }
    })
    .catch(() => alert('Lỗi kết nối.'));
}

function rejectOrder(id) {
    if (!confirm('Từ chối đơn này?')) return;
    
    fetch('<?= BASE_URL ?>/admin/rejectOrder', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'same-origin',
        body: JSON.stringify({ id: id })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('❌ ' + data.error);
        }
    })
    .catch(() => alert('Lỗi kết nối.'));
}

function simulateForOrder(desc, amount) {
    document.getElementById('simDesc').value = desc;
    document.getElementById('simAmount').value = amount;
    simulateWebhook();
}

function simulateWebhook() {
    const desc = document.getElementById('simDesc').value.trim();
    const amount = parseInt(document.getElementById('simAmount').value) || 0;
    const resultEl = document.getElementById('simResult');

    if (!desc) { resultEl.innerHTML = '<div style="color:var(--error);">Vui lòng nhập nội dung CK.</div>'; return; }
    if (!amount) { resultEl.innerHTML = '<div style="color:var(--error);">Vui lòng nhập số tiền.</div>'; return; }

    resultEl.innerHTML = '<div style="color:var(--primary);"><i class="fas fa-spinner fa-spin"></i> Đang giả lập webhook Casso...</div>';

    fetch('<?= BASE_URL ?>/webhook/simulate', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'same-origin',
        body: JSON.stringify({ description: desc, amount: amount })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            resultEl.innerHTML = `
                <div style="background:rgba(67,233,123,0.1); border:1px solid var(--success); padding:1rem; border-radius:var(--radius-md);">
                    <i class="fas fa-check-circle" style="color:var(--success);"></i>
                    <strong style="color:var(--success);">Giả lập thành công!</strong>
                    <p style="margin:0.5rem 0 0; font-size:0.85rem;">Đơn đã tự động duyệt. User đã được nâng cấp Pro.</p>
                    <button class="btn btn-primary" onclick="location.reload()" style="margin-top:0.5rem; padding:6px 16px; font-size:0.85rem;">
                        <i class="fas fa-sync"></i> Tải lại trang
                    </button>
                </div>`;
        } else {
            const errDiv = document.createElement('div');
            errDiv.style.color = 'var(--error)';
            errDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i> ';
            const errSpan = document.createElement('span');
            errSpan.textContent = data.errors ? data.errors.join(', ') : 'Không thể xử lý.';
            errDiv.appendChild(errSpan);
            resultEl.innerHTML = '';
            resultEl.appendChild(errDiv);
        }
    })
    .catch(() => resultEl.innerHTML = '<div style="color:var(--error);">Lỗi kết nối.</div>');
}
</script>
