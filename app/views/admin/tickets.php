<?php
$adminActive = 'tickets';
$adminTitle = 'Quản lý Tickets';
$adminSubtitle = 'Theo dõi hỗ trợ, phản hồi ticket và xử lý yêu cầu hủy đơn.';
require APP_PATH . '/views/admin/_nav.php';
?>

<section class="dashboard-section">
    <div class="container">
        <h2 style="margin-bottom:1.5rem;"><i class="fas fa-headset"></i> Quản lý Ticket hỗ trợ (<?= count($tickets) ?>)</h2>

        <?php if (empty($tickets)): ?>
            <div class="section-card" style="text-align:center; padding:3rem;">
                <i class="fas fa-inbox" style="font-size:3rem; color:var(--text-muted);"></i>
                <h3 style="color:var(--text-muted);">Không có ticket nào</h3>
            </div>
        <?php else: ?>
            <div class="tickets-admin-list">
                <?php foreach ($tickets as $ticket): ?>
                <div class="section-card" style="margin-bottom:1rem; padding:1.25rem;" id="ticket-<?= $ticket['id'] ?>">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:1rem; flex-wrap:wrap;">
                        <div style="flex:1; min-width:250px;">
                            <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.5rem; flex-wrap:wrap;">
                                <strong style="color:var(--primary);">#<?= $ticket['id'] ?></strong>
                                <?php
                                    $typeLabels = ['general' => 'Hỗ trợ chung', 'cancel_order' => 'Hủy đơn', 'bug_report' => 'Báo lỗi', 'feedback' => 'Góp ý'];
                    $typeColors = ['general' => '#6366f1', 'cancel_order' => '#ef4444', 'bug_report' => '#f59e0b', 'feedback' => '#10b981'];
                    $statusLabels = ['open' => 'Mở', 'in_progress' => 'Đang xử lý', 'resolved' => 'Đã xử lý', 'closed' => 'Đóng'];
                    $statusColors = ['open' => '#3b82f6', 'in_progress' => '#f59e0b', 'resolved' => '#10b981', 'closed' => '#6b7280'];
                    ?>
                                <span style="background:<?= $typeColors[$ticket['type']] ?>20; color:<?= $typeColors[$ticket['type']] ?>; padding:2px 8px; border-radius:20px; font-size:0.7rem; font-weight:600;">
                                    <?= $typeLabels[$ticket['type']] ?>
                                </span>
                                <span style="background:<?= $statusColors[$ticket['status']] ?>20; color:<?= $statusColors[$ticket['status']] ?>; padding:2px 8px; border-radius:20px; font-size:0.7rem; font-weight:600;" id="status-badge-<?= $ticket['id'] ?>">
                                    <?= $statusLabels[$ticket['status']] ?>
                                </span>
                                <span style="color:var(--text-muted); font-size:0.8rem;">
                                    <i class="fas fa-user"></i> <?= htmlspecialchars($ticket['username']) ?> (<?= htmlspecialchars($ticket['full_name'] ?? '') ?>)
                                </span>
                                <span style="color:var(--text-muted); font-size:0.8rem;">
                                    <i class="fas fa-clock"></i> <?= date('d/m/Y H:i', strtotime($ticket['created_at'])) ?>
                                </span>
                            </div>
                            <h4 style="margin:0 0 0.25rem;"><?= htmlspecialchars($ticket['subject']) ?></h4>
                            <p style="color:var(--text-secondary); font-size:0.9rem; margin:0; white-space:pre-line;"><?= htmlspecialchars($ticket['message']) ?></p>

                            <?php if ($ticket['type'] === 'cancel_order' && !empty($ticket['plan_name'])): ?>
                                <div style="margin-top:0.5rem; padding:0.5rem; background:#fef2f2; border-radius:var(--radius-sm); font-size:0.85rem;">
                                    <i class="fas fa-file-invoice" style="color:#ef4444;"></i>
                                    Đơn #<?= $ticket['related_order_id'] ?> — <?= htmlspecialchars($ticket['plan_name']) ?> — <?= number_format($ticket['order_amount']) ?>đ
                                    <span style="padding:2px 8px; border-radius:10px; font-size:0.7rem; background:<?= $ticket['order_status'] === 'pending' ? '#fef3c7' : '#dcfce7' ?>; color:<?= $ticket['order_status'] === 'pending' ? '#d97706' : '#16a34a' ?>;">
                                        <?= $ticket['order_status'] === 'pending' ? 'Đang chờ' : 'Đã xử lý' ?>
                                    </span>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($ticket['admin_reply'])): ?>
                                <div style="margin-top:0.75rem; padding:0.75rem; background:rgba(99,102,241,0.05); border-left:3px solid var(--primary); border-radius:0 var(--radius-sm) var(--radius-sm) 0;">
                                    <small style="color:var(--primary); font-weight:600;"><i class="fas fa-reply"></i> Đã phản hồi <?= $ticket['replied_at'] ? date('d/m H:i', strtotime($ticket['replied_at'])) : '' ?></small>
                                    <p style="margin:0.25rem 0 0; font-size:0.9rem;"><?= htmlspecialchars($ticket['admin_reply']) ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div style="display:flex; flex-direction:column; gap:0.5rem; min-width:120px;">
                            <?php if ($ticket['status'] !== 'closed'): ?>
                                <!-- Reply form toggle -->
                                <button class="btn btn-primary" style="padding:6px 14px; font-size:0.8rem;" 
                                        onclick="toggleReply(<?= $ticket['id'] ?>)">
                                    <i class="fas fa-reply"></i> Phản hồi
                                </button>
                                <?php if ($ticket['type'] === 'cancel_order' && !empty($ticket['related_order_id']) && $ticket['order_status'] === 'pending'): ?>
                                    <button class="btn" style="padding:6px 14px; font-size:0.8rem; background:#ef4444; color:white;" 
                                            onclick="approveCancel(<?= $ticket['id'] ?>, <?= $ticket['related_order_id'] ?>)">
                                        <i class="fas fa-check"></i> Duyệt hủy đơn
                                    </button>
                                <?php endif; ?>
                                <select onchange="changeTicketStatus(<?= $ticket['id'] ?>, this.value)" 
                                        style="padding:6px; font-size:0.8rem; border-radius:var(--radius-sm); border:1px solid var(--border-color);">
                                    <option value="">Đổi trạng thái</option>
                                    <option value="open" <?= $ticket['status'] === 'open' ? 'selected' : '' ?>>Mở</option>
                                    <option value="in_progress" <?= $ticket['status'] === 'in_progress' ? 'selected' : '' ?>>Đang xử lý</option>
                                    <option value="resolved" <?= $ticket['status'] === 'resolved' ? 'selected' : '' ?>>Đã xử lý</option>
                                    <option value="closed" <?= $ticket['status'] === 'closed' ? 'selected' : '' ?>>Đóng</option>
                                </select>
                            <?php else: ?>
                                <span style="color:var(--text-muted); font-size:0.8rem; text-align:center;">Đã đóng</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Reply form (hidden) -->
                    <div id="reply-form-<?= $ticket['id'] ?>" style="display:none; margin-top:1rem; padding-top:1rem; border-top:1px solid var(--border-color);">
                        <textarea id="reply-text-<?= $ticket['id'] ?>" class="form-input" rows="3" 
                                  placeholder="Nhập phản hồi cho user..." style="margin-bottom:0.5rem; padding:0.75rem;"></textarea>
                        <div style="display:flex; gap:0.5rem; justify-content:flex-end;">
                            <button class="btn btn-outline" style="padding:6px 14px; font-size:0.8rem;" onclick="toggleReply(<?= $ticket['id'] ?>)">Hủy</button>
                            <button class="btn btn-primary" style="padding:6px 14px; font-size:0.8rem;" onclick="submitReply(<?= $ticket['id'] ?>)">
                                <i class="fas fa-paper-plane"></i> Gửi phản hồi
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<script>
function toggleReply(id) {
    const form = document.getElementById('reply-form-' + id);
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
}

function submitReply(id) {
    const reply = document.getElementById('reply-text-' + id).value.trim();
    if (!reply) return alert('Vui lòng nhập nội dung phản hồi.');

    fetch('<?= BASE_URL ?>/admin/replyTicket', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'same-origin',
        body: JSON.stringify({ ticket_id: id, reply: reply })
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            location.reload();
        } else {
            alert(res.error || 'Lỗi');
        }
    })
    .catch(err => alert('Lỗi: ' + err.message));
}

function changeTicketStatus(id, status) {
    if (!status) return;
    fetch('<?= BASE_URL ?>/admin/updateTicketStatus', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'same-origin',
        body: JSON.stringify({ ticket_id: id, status: status })
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            location.reload();
        } else {
            alert(res.error || 'Lỗi');
        }
    });
}

function approveCancel(ticketId, orderId) {
    if (!confirm('Duyệt hủy đơn #' + orderId + '? Hành động này không thể hoàn tác.')) return;
    
    fetch('<?= BASE_URL ?>/admin/approveCancelOrder', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'same-origin',
        body: JSON.stringify({ ticket_id: ticketId, order_id: orderId })
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            alert('Đã duyệt hủy đơn thành công!');
            location.reload();
        } else {
            alert(res.error || 'Lỗi');
        }
    });
}
</script>
