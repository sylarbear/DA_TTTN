<?php
$adminActive = 'wallet';
$adminTitle = 'Quản lý giao dịch ví';
$adminSubtitle = 'Duyệt giao dịch nạp/rút, hoàn tiền và theo dõi số dư học viên.';
require APP_PATH . '/views/admin/_nav.php';
?>

<section class="dashboard-section">
    <div class="container">
        <h2 style="margin-bottom:1.5rem;"><i class="fas fa-wallet"></i> Quản lý giao dịch ví (<?= count($transactions) ?>)</h2>

        <?php if (empty($transactions)): ?>
            <div class="section-card" style="text-align:center; padding:3rem;">
                <i class="fas fa-inbox" style="font-size:3rem; color:var(--text-muted);"></i>
                <h3 style="color:var(--text-muted);">Chưa có giao dịch nào</h3>
            </div>
        <?php else: ?>
            <div class="progress-table" style="overflow-x:auto;">
                <table>
                    <thead><tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Loại</th>
                        <th>Số tiền</th>
                        <th>Chi tiết</th>
                        <th>Thời gian</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr></thead>
                    <tbody>
                    <?php foreach ($transactions as $tx): ?>
                        <?php
                            $typeLabels = ['deposit'=>'Nạp tiền','purchase'=>'Mua gói','refund'=>'Hoàn tiền','withdraw'=>'Rút tiền'];
                            $typeColors = ['deposit'=>'#10b981','purchase'=>'#6366f1','refund'=>'#3b82f6','withdraw'=>'#f59e0b'];
                            $statusLabels = ['pending'=>'Chờ duyệt','completed'=>'Hoàn tất','rejected'=>'Từ chối'];
                            $statusColors = ['pending'=>'#f59e0b','completed'=>'#10b981','rejected'=>'#ef4444'];
                        ?>
                        <tr id="tx-<?= $tx['id'] ?>">
                            <td><strong>#<?= $tx['id'] ?></strong></td>
                            <td>
                                <strong><?= htmlspecialchars($tx['username']) ?></strong>
                                <br><small style="color:var(--text-muted);">Balance: <?= number_format($tx['balance']) ?>đ</small>
                            </td>
                            <td><span style="color:<?= $typeColors[$tx['type']] ?>; font-weight:600; font-size:0.85rem;"><?= $typeLabels[$tx['type']] ?></span></td>
                            <td style="font-weight:700; font-size:1rem;"><?= number_format($tx['amount']) ?>đ</td>
                            <td style="max-width:200px; font-size:0.8rem;">
                                <?= htmlspecialchars($tx['description'] ?? '') ?>
                                <?php if ($tx['type'] === 'withdraw' && !empty($tx['bank_name'])): ?>
                                    <br><strong><?= htmlspecialchars($tx['bank_name']) ?></strong> — <?= htmlspecialchars($tx['bank_account']) ?>
                                    <br><small><?= htmlspecialchars($tx['bank_holder']) ?></small>
                                <?php endif; ?>
                                <?php if (!empty($tx['transfer_note'])): ?>
                                    <br><small style="color:#ef4444;">ND: <?= htmlspecialchars($tx['transfer_note']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td style="font-size:0.8rem; white-space:nowrap;"><?= date('d/m/Y H:i', strtotime($tx['created_at'])) ?></td>
                            <td>
                                <span style="background:<?= $statusColors[$tx['status']] ?>20; color:<?= $statusColors[$tx['status']] ?>; padding:3px 10px; border-radius:20px; font-size:0.75rem; font-weight:600;">
                                    <?= $statusLabels[$tx['status']] ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($tx['status'] === 'pending'): ?>
                                    <div style="display:flex; gap:0.25rem;">
                                        <button class="btn btn-primary" style="padding:4px 10px; font-size:0.75rem;" onclick="approveTx(<?= $tx['id'] ?>)">
                                            <i class="fas fa-check"></i> Duyệt
                                        </button>
                                        <button class="btn" style="padding:4px 10px; font-size:0.75rem; background:#ef4444; color:white;" onclick="rejectTx(<?= $tx['id'] ?>)">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                <?php elseif (!empty($tx['admin_note'])): ?>
                                    <small style="color:var(--text-muted);"><?= htmlspecialchars($tx['admin_note']) ?></small>
                                <?php else: ?>
                                    <small style="color:var(--text-muted);">—</small>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</section>

<script>
function approveTx(id) {
    if (!confirm('Duyệt giao dịch #' + id + '?')) return;
    fetch('<?= BASE_URL ?>/admin/approveTransaction', {
        method: 'POST', headers: {'Content-Type':'application/json'}, credentials:'same-origin',
        body: JSON.stringify({id})
    }).then(r=>r.json()).then(res => {
        if (res.success) { alert(res.message); location.reload(); }
        else alert(res.error);
    });
}
function rejectTx(id) {
    const note = prompt('Lý do từ chối:');
    if (note === null) return;
    fetch('<?= BASE_URL ?>/admin/rejectTransaction', {
        method: 'POST', headers: {'Content-Type':'application/json'}, credentials:'same-origin',
        body: JSON.stringify({id, note: note || 'Từ chối bởi Admin'})
    }).then(r=>r.json()).then(res => {
        if (res.success) location.reload();
        else alert(res.error);
    });
}
</script>
