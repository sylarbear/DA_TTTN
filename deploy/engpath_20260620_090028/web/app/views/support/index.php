<!-- Support Tickets Page -->
<section class="page-header">
    <div class="container">
        <h1><i class="fas fa-headset"></i> Hỗ trợ</h1>
        <p style="color:var(--text-secondary);">Gửi yêu cầu hỗ trợ và theo dõi trạng thái</p>
    </div>
</section>

<section class="dashboard-section">
    <div class="container" style="max-width:900px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
            <h3 style="margin:0;"><i class="fas fa-ticket-alt"></i> Ticket của bạn</h3>
            <a href="<?= BASE_URL ?>/support/create" class="btn btn-primary">
                <i class="fas fa-plus"></i> Gửi ticket mới
            </a>
        </div>

        <?php if (empty($tickets)): ?>
            <div class="section-card" style="text-align:center; padding:3rem;">
                <i class="fas fa-inbox" style="font-size:3rem; color:var(--text-muted); margin-bottom:1rem;"></i>
                <h3 style="color:var(--text-muted);">Chưa có ticket nào</h3>
                <p style="color:var(--text-muted);">Bạn có thắc mắc hoặc cần hỗ trợ? Gửi ticket ngay!</p>
                <a href="<?= BASE_URL ?>/support/create" class="btn btn-primary" style="margin-top:1rem;">
                    <i class="fas fa-paper-plane"></i> Gửi ticket đầu tiên
                </a>
            </div>
        <?php else: ?>
            <div class="tickets-list">
                <?php foreach ($tickets as $ticket): ?>
                <div class="section-card ticket-card" style="margin-bottom:1rem; padding:1.25rem;">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:1rem; flex-wrap:wrap;">
                        <div style="flex:1; min-width:200px;">
                            <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.5rem;">
                                <?php
                                    $typeLabels = ['general'=>'Hỗ trợ chung','cancel_order'=>'Hủy đơn','bug_report'=>'Báo lỗi','feedback'=>'Góp ý'];
                                    $typeIcons = ['general'=>'question-circle','cancel_order'=>'ban','bug_report'=>'bug','feedback'=>'comment'];
                                    $typeColors = ['general'=>'#6366f1','cancel_order'=>'#ef4444','bug_report'=>'#f59e0b','feedback'=>'#10b981'];
                                ?>
                                <span style="background:<?= $typeColors[$ticket['type']] ?? '#6366f1' ?>20; color:<?= $typeColors[$ticket['type']] ?? '#6366f1' ?>; padding:3px 10px; border-radius:20px; font-size:0.75rem; font-weight:600;">
                                    <i class="fas fa-<?= $typeIcons[$ticket['type']] ?? 'question-circle' ?>"></i>
                                    <?= $typeLabels[$ticket['type']] ?? 'Khác' ?>
                                </span>
                                <?php
                                    $statusLabels = ['open'=>'Mở','in_progress'=>'Đang xử lý','resolved'=>'Đã xử lý','closed'=>'Đóng'];
                                    $statusColors = ['open'=>'#3b82f6','in_progress'=>'#f59e0b','resolved'=>'#10b981','closed'=>'#6b7280'];
                                ?>
                                <span style="background:<?= $statusColors[$ticket['status']] ?>20; color:<?= $statusColors[$ticket['status']] ?>; padding:3px 10px; border-radius:20px; font-size:0.75rem; font-weight:600;">
                                    <?= $statusLabels[$ticket['status']] ?>
                                </span>
                                <?php if ($ticket['type'] === 'cancel_order' && !empty($ticket['plan_name'])): ?>
                                    <span style="color:var(--text-muted); font-size:0.8rem;">— Đơn: <?= htmlspecialchars($ticket['plan_name']) ?></span>
                                <?php endif; ?>
                            </div>
                            <h4 style="margin:0 0 0.25rem;">#<?= $ticket['id'] ?> — <?= htmlspecialchars($ticket['subject']) ?></h4>
                            <p style="color:var(--text-secondary); font-size:0.85rem; margin:0;">
                                <?= htmlspecialchars(mb_substr($ticket['message'], 0, 120)) ?><?= mb_strlen($ticket['message']) > 120 ? '...' : '' ?>
                            </p>
                        </div>
                        <div style="text-align:right; font-size:0.8rem; color:var(--text-muted); min-width:100px;">
                            <div><i class="fas fa-clock"></i> <?= date('d/m/Y H:i', strtotime($ticket['created_at'])) ?></div>
                        </div>
                    </div>

                    <?php if (!empty($ticket['admin_reply'])): ?>
                    <div style="margin-top:1rem; padding:1rem; background:linear-gradient(135deg, rgba(99,102,241,0.05), rgba(139,92,246,0.05)); border-left:3px solid var(--primary); border-radius:0 var(--radius-sm) var(--radius-sm) 0;">
                        <div style="font-size:0.8rem; color:var(--primary); font-weight:600; margin-bottom:0.5rem;">
                            <i class="fas fa-reply"></i> Phản hồi từ Admin
                            <?php if ($ticket['replied_at']): ?>
                                <span style="font-weight:400; color:var(--text-muted);">— <?= date('d/m/Y H:i', strtotime($ticket['replied_at'])) ?></span>
                            <?php endif; ?>
                        </div>
                        <p style="margin:0; color:var(--text-primary); font-size:0.9rem; white-space:pre-line;"><?= htmlspecialchars($ticket['admin_reply']) ?></p>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
