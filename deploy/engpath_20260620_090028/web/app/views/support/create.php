<!-- Create Support Ticket Page -->
<section class="page-header">
    <div class="container">
        <nav class="breadcrumb">
            <a href="<?= BASE_URL ?>/support">Hỗ trợ</a>
            <span>/</span>
            <span>Gửi ticket mới</span>
        </nav>
        <h1><i class="fas fa-paper-plane"></i> Gửi yêu cầu hỗ trợ</h1>
    </div>
</section>

<section class="dashboard-section">
    <div class="container" style="max-width:700px;">

        <!-- Chính sách hủy đơn -->
        <div class="section-card" style="padding:1.25rem; margin-bottom:1.5rem; background:linear-gradient(135deg, rgba(99,102,241,0.03), rgba(139,92,246,0.03)); border-left:4px solid var(--primary);" id="cancelPolicyCard"
             <?= $preType !== 'cancel_order' ? 'style="display:none;"' : '' ?>>
            <h4 style="margin:0 0 0.75rem; color:var(--primary);"><i class="fas fa-info-circle"></i> Chính sách hủy đơn</h4>
            <table style="width:100%; font-size:0.85rem; border-collapse:collapse;">
                <thead>
                    <tr style="border-bottom:1px solid var(--border-color);">
                        <th style="text-align:left; padding:0.5rem; color:var(--text-muted);">Loại gói</th>
                        <th style="text-align:left; padding:0.5rem; color:var(--text-muted);">Thời hạn hủy</th>
                        <th style="text-align:left; padding:0.5rem; color:var(--text-muted);">Hoàn tiền</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="padding:0.5rem;">Tất cả gói</td>
                        <td style="padding:0.5rem;"><strong style="color:#10b981;">Trong 24 giờ</strong></td>
                        <td style="padding:0.5rem;"><span style="background:#dcfce7;color:#16a34a;padding:2px 8px;border-radius:10px;font-weight:600;">100%</span></td>
                    </tr>
                    <tr style="background:rgba(0,0,0,0.02);">
                        <td style="padding:0.5rem;">Gói 3+ tháng</td>
                        <td style="padding:0.5rem;">24 giờ — 7 ngày</td>
                        <td style="padding:0.5rem;"><span style="background:#fef3c7;color:#d97706;padding:2px 8px;border-radius:10px;font-weight:600;">50%</span></td>
                    </tr>
                    <tr>
                        <td style="padding:0.5rem;">Gói 1 tháng</td>
                        <td style="padding:0.5rem;"><span style="color:#ef4444;">Sau 24 giờ</span></td>
                        <td style="padding:0.5rem;"><span style="background:#fef2f2;color:#ef4444;padding:2px 8px;border-radius:10px;font-weight:600;">0%</span></td>
                    </tr>
                    <tr style="background:rgba(0,0,0,0.02);">
                        <td style="padding:0.5rem;">Gói 3+ tháng</td>
                        <td style="padding:0.5rem;"><span style="color:#ef4444;">Sau 7 ngày</span></td>
                        <td style="padding:0.5rem;"><span style="background:#fef2f2;color:#ef4444;padding:2px 8px;border-radius:10px;font-weight:600;">0%</span></td>
                    </tr>
                </tbody>
            </table>
            <p style="margin:0.75rem 0 0; font-size:0.8rem; color:var(--text-muted);">
                <i class="fas fa-exclamation-triangle" style="color:#f59e0b;"></i>
                Đơn đã kích hoạt (completed) không thể hủy trực tiếp. Chỉ đơn đang chờ duyệt (pending) mới được hủy.
            </p>
        </div>

        <!-- Pre-check eligibility alert -->
        <?php if ($preOrderEligibility): ?>
            <?php if ($preOrderEligibility['can_cancel']): ?>
                <div style="padding:1rem; margin-bottom:1rem; background:#dcfce7; border-radius:var(--radius-sm); color:#16a34a; font-size:0.9rem;">
                    <i class="fas fa-check-circle"></i> <strong>Đủ điều kiện hủy!</strong> <?= htmlspecialchars($preOrderEligibility['policy_note']) ?>
                </div>
            <?php else: ?>
                <div style="padding:1rem; margin-bottom:1rem; background:#fef2f2; border-radius:var(--radius-sm); color:#ef4444; font-size:0.9rem;">
                    <i class="fas fa-times-circle"></i> <strong>Không thể hủy:</strong> <?= htmlspecialchars($preOrderEligibility['reason']) ?>
                    <?php if ($preOrderEligibility['policy_note'] && $preOrderEligibility['policy_note'] !== 'refund_request'): ?>
                        <br><small><?= htmlspecialchars($preOrderEligibility['policy_note']) ?></small>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="section-card" style="padding:2rem;">
            <form id="ticketForm">
                <!-- Loại ticket -->
                <div class="form-group" style="margin-bottom:1.5rem;">
                    <label style="font-weight:600; margin-bottom:0.5rem; display:block;">
                        <i class="fas fa-tag"></i> Loại yêu cầu
                    </label>
                    <div class="ticket-types" style="display:grid; grid-template-columns:repeat(2,1fr); gap:0.5rem;">
                        <label class="ticket-type-option" style="display:flex;align-items:center;gap:0.5rem;padding:0.75rem 1rem;border:2px solid var(--border-color);border-radius:var(--radius-sm);cursor:pointer;transition:all 0.2s;">
                            <input type="radio" name="type" value="general" <?= $preType==='general'?'checked':'' ?> style="margin:0;">
                            <i class="fas fa-question-circle" style="color:#6366f1;"></i>
                            <span style="font-size:0.9rem;">Hỗ trợ chung</span>
                        </label>
                        <label class="ticket-type-option" style="display:flex;align-items:center;gap:0.5rem;padding:0.75rem 1rem;border:2px solid var(--border-color);border-radius:var(--radius-sm);cursor:pointer;transition:all 0.2s;">
                            <input type="radio" name="type" value="cancel_order" <?= $preType==='cancel_order'?'checked':'' ?> style="margin:0;">
                            <i class="fas fa-ban" style="color:#ef4444;"></i>
                            <span style="font-size:0.9rem;">Hủy đơn nâng cấp</span>
                        </label>
                        <label class="ticket-type-option" style="display:flex;align-items:center;gap:0.5rem;padding:0.75rem 1rem;border:2px solid var(--border-color);border-radius:var(--radius-sm);cursor:pointer;transition:all 0.2s;">
                            <input type="radio" name="type" value="bug_report" <?= $preType==='bug_report'?'checked':'' ?> style="margin:0;">
                            <i class="fas fa-bug" style="color:#f59e0b;"></i>
                            <span style="font-size:0.9rem;">Báo lỗi</span>
                        </label>
                        <label class="ticket-type-option" style="display:flex;align-items:center;gap:0.5rem;padding:0.75rem 1rem;border:2px solid var(--border-color);border-radius:var(--radius-sm);cursor:pointer;transition:all 0.2s;">
                            <input type="radio" name="type" value="feedback" <?= $preType==='feedback'?'checked':'' ?> style="margin:0;">
                            <i class="fas fa-comment" style="color:#10b981;"></i>
                            <span style="font-size:0.9rem;">Góp ý</span>
                        </label>
                    </div>
                </div>

                <!-- Chọn đơn cần hủy (hiện khi type=cancel_order) -->
                <div class="form-group" id="orderSelectGroup" style="margin-bottom:1.5rem; display:<?= $preType==='cancel_order'?'block':'none' ?>;">
                    <label style="font-weight:600; margin-bottom:0.5rem; display:block;">
                        <i class="fas fa-file-invoice"></i> Chọn đơn cần hủy
                    </label>
                    <?php if (empty($pendingOrders)): ?>
                        <div style="padding:1rem; background:#fef3c7; border-radius:var(--radius-sm); color:#92400e; font-size:0.9rem;">
                            <i class="fas fa-info-circle"></i> Bạn không có đơn nào đang chờ duyệt. Chỉ đơn <strong>đang chờ duyệt</strong> mới được hủy.
                        </div>
                    <?php else: ?>
                        <select name="order_id" id="orderSelect" class="form-input" style="padding:0.75rem;" onchange="checkOrderEligibility()">
                            <option value="">-- Chọn đơn --</option>
                            <?php foreach ($pendingOrders as $o): ?>
                                <option value="<?= $o['id'] ?>" 
                                        data-can-cancel="<?= $o['can_cancel'] ? '1' : '0' ?>"
                                        data-reason="<?= htmlspecialchars($o['cancel_reason']) ?>"
                                        data-refund="<?= $o['refund_percent'] ?>"
                                        data-policy="<?= htmlspecialchars($o['policy_note']) ?>"
                                        <?= $preOrderId==$o['id']?'selected':'' ?>>
                                    #<?= $o['id'] ?> — <?= htmlspecialchars($o['plan_name']) ?> (<?= number_format($o['amount']) ?>đ) — <?= date('d/m/Y H:i', strtotime($o['activated_at'])) ?>
                                    <?php if ($o['can_cancel']): ?>
                                        ✅ Hoàn <?= $o['refund_percent'] ?>%
                                    <?php else: ?>
                                        ❌ Hết hạn hủy
                                    <?php endif; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <!-- Eligibility result -->
                        <div id="eligibilityResult" style="margin-top:0.5rem; padding:0.75rem; border-radius:var(--radius-sm); font-size:0.85rem; display:none;"></div>
                    <?php endif; ?>
                </div>

                <!-- Tiêu đề -->
                <div class="form-group" style="margin-bottom:1.5rem;">
                    <label style="font-weight:600; margin-bottom:0.5rem; display:block;">
                        <i class="fas fa-heading"></i> Tiêu đề
                    </label>
                    <input type="text" name="subject" id="ticketSubject" class="form-input" 
                           placeholder="Mô tả ngắn gọn vấn đề..." maxlength="200"
                           style="padding:0.75rem;" required>
                </div>

                <!-- Nội dung -->
                <div class="form-group" style="margin-bottom:1.5rem;">
                    <label style="font-weight:600; margin-bottom:0.5rem; display:block;">
                        <i class="fas fa-align-left"></i> Nội dung chi tiết
                    </label>
                    <textarea name="message" id="ticketMessage" class="form-input" rows="6" 
                              placeholder="Mô tả chi tiết vấn đề của bạn..." 
                              style="padding:0.75rem; resize:vertical;" required></textarea>
                </div>

                <!-- Submit -->
                <div style="display:flex; gap:1rem; justify-content:flex-end;">
                    <a href="<?= BASE_URL ?>/support" class="btn btn-outline">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                    <button type="submit" class="btn btn-primary" id="submitTicketBtn">
                        <i class="fas fa-paper-plane"></i> Gửi ticket
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<script>
// Check eligibility when order is selected
function checkOrderEligibility() {
    const select = document.getElementById('orderSelect');
    const resultDiv = document.getElementById('eligibilityResult');
    const submitBtn = document.getElementById('submitTicketBtn');
    
    if (!select || !select.value) {
        if (resultDiv) resultDiv.style.display = 'none';
        return;
    }
    
    const option = select.options[select.selectedIndex];
    const canCancel = option.dataset.canCancel === '1';
    const reason = option.dataset.reason;
    const refund = option.dataset.refund;
    const policy = option.dataset.policy;
    
    if (resultDiv) {
        resultDiv.style.display = 'block';
        if (canCancel) {
            resultDiv.style.background = '#dcfce7';
            resultDiv.style.color = '#16a34a';
            resultDiv.innerHTML = '<i class="fas fa-check-circle"></i> <strong>Đủ điều kiện hủy</strong> — ' + policy + 
                (refund > 0 ? '<br><strong>Hoàn tiền: ' + refund + '%</strong>' : '');
            submitBtn.disabled = false;
        } else {
            resultDiv.style.background = '#fef2f2';
            resultDiv.style.color = '#ef4444';
            resultDiv.innerHTML = '<i class="fas fa-times-circle"></i> <strong>Không thể hủy:</strong> ' + reason +
                (policy ? '<br><small>' + policy + '</small>' : '');
            // Vẫn cho submit nhưng server sẽ reject
        }
    }
}

// Toggle order select visibility + policy card
document.querySelectorAll('input[name="type"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const orderGroup = document.getElementById('orderSelectGroup');
        const policyCard = document.getElementById('cancelPolicyCard');
        const subjectInput = document.getElementById('ticketSubject');
        
        const isCancel = this.value === 'cancel_order';
        orderGroup.style.display = isCancel ? 'block' : 'none';
        policyCard.style.display = isCancel ? 'block' : 'none';
        
        if (isCancel && !subjectInput.value) {
            subjectInput.value = 'Yêu cầu hủy đơn nâng cấp';
        }
        
        // Highlight selected type
        document.querySelectorAll('.ticket-type-option').forEach(opt => {
            opt.style.borderColor = 'var(--border-color)';
            opt.style.background = 'transparent';
        });
        this.closest('.ticket-type-option').style.borderColor = 'var(--primary)';
        this.closest('.ticket-type-option').style.background = 'rgba(99,102,241,0.05)';
    });
    
    if (radio.checked) {
        radio.closest('.ticket-type-option').style.borderColor = 'var(--primary)';
        radio.closest('.ticket-type-option').style.background = 'rgba(99,102,241,0.05)';
    }
});

// Auto-fill subject if cancel_order pre-selected
if (document.querySelector('input[name="type"][value="cancel_order"]')?.checked) {
    const subject = document.getElementById('ticketSubject');
    if (!subject.value) subject.value = 'Yêu cầu hủy đơn nâng cấp';
    // Show policy card
    const policyCard = document.getElementById('cancelPolicyCard');
    if (policyCard) policyCard.style.display = 'block';
    checkOrderEligibility();
}

// Submit form
document.getElementById('ticketForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const btn = document.getElementById('submitTicketBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang gửi...';
    
    const type = document.querySelector('input[name="type"]:checked')?.value || 'general';
    const data = {
        type: type,
        subject: document.getElementById('ticketSubject').value.trim(),
        message: document.getElementById('ticketMessage').value.trim(),
        order_id: type === 'cancel_order' ? parseInt(document.getElementById('orderSelect')?.value || 0) : 0
    };
    
    fetch('<?= BASE_URL ?>/support/store', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'same-origin',
        body: JSON.stringify(data)
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            document.querySelector('.section-card:last-child').innerHTML = `
                <div style="text-align:center; padding:3rem;">
                    <i class="fas fa-check-circle" style="font-size:4rem; color:var(--success); margin-bottom:1rem;"></i>
                    <h2>Gửi ticket thành công!</h2>
                    <p style="color:var(--text-secondary); margin-bottom:1.5rem;">${res.message}</p>
                    <a href="<?= BASE_URL ?>/support" class="btn btn-primary">
                        <i class="fas fa-list"></i> Xem danh sách ticket
                    </a>
                </div>
            `;
        } else {
            let errorMsg = res.error || 'Có lỗi xảy ra.';
            if (res.policy_note) errorMsg += '\n\n📋 ' + res.policy_note;
            alert(errorMsg);
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-paper-plane"></i> Gửi ticket';
        }
    })
    .catch(err => {
        alert('Lỗi kết nối: ' + err.message);
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-paper-plane"></i> Gửi ticket';
    });
});
</script>
