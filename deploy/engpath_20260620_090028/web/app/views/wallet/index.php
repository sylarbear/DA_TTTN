<!-- Wallet Index Page -->
<section class="page-header">
    <div class="container">
        <h1><i class="fas fa-wallet"></i> Ví của tôi</h1>
        <p style="color:var(--text-secondary);">Quản lý số dư và giao dịch</p>
    </div>
</section>

<section class="dashboard-section">
    <div class="container" style="max-width:900px;">
        <!-- Balance Card -->
        <div class="section-card" style="text-align:center; padding:2.5rem; margin-bottom:2rem; background:linear-gradient(135deg, rgba(99,102,241,0.05), rgba(139,92,246,0.08)); border:1px solid rgba(99,102,241,0.15);">
            <p style="color:var(--text-muted); margin:0 0 0.5rem; font-size:0.9rem;">Số dư khả dụng</p>
            <div style="font-size:2.5rem; font-weight:800; color:var(--primary); margin-bottom:1rem;">
                <?= number_format($balance) ?> <span style="font-size:1rem; font-weight:400; color:var(--text-muted);">VNĐ</span>
            </div>
            <div style="display:flex; gap:1rem; justify-content:center; flex-wrap:wrap;">
                <a href="<?= BASE_URL ?>/wallet/deposit" class="btn btn-primary" style="padding:0.75rem 2rem;">
                    <i class="fas fa-plus-circle"></i> Nạp tiền
                </a>
                <a href="<?= BASE_URL ?>/wallet/withdraw" class="btn btn-outline" style="padding:0.75rem 2rem;">
                    <i class="fas fa-arrow-circle-down"></i> Rút tiền
                </a>
                <a href="<?= BASE_URL ?>/membership" class="btn" style="padding:0.75rem 2rem; background:#f59e0b; color:white;">
                    <i class="fas fa-crown"></i> Mua gói Pro
                </a>
            </div>
        </div>

        <!-- Transaction History -->
        <div class="section-card" style="padding:1.5rem;">
            <h3 style="margin:0 0 1.5rem;"><i class="fas fa-history"></i> Lịch sử giao dịch</h3>

            <?php if (empty($transactions)): ?>
                <div style="text-align:center; padding:2rem; color:var(--text-muted);">
                    <i class="fas fa-receipt" style="font-size:2.5rem; margin-bottom:1rem;"></i>
                    <p>Chưa có giao dịch nào</p>
                </div>
            <?php else: ?>
                <div class="progress-table" style="overflow-x:auto;">
                    <table>
                        <thead><tr>
                            <th>Thời gian</th>
                            <th>Loại</th>
                            <th>Mô tả</th>
                            <th>Số tiền</th>
                            <th>Số dư sau</th>
                            <th>Trạng thái</th>
                        </tr></thead>
                        <tbody>
                        <?php foreach ($transactions as $tx): ?>
                            <?php
                                $typeLabels = ['deposit'=>'Nạp tiền','purchase'=>'Mua gói','refund'=>'Hoàn tiền','withdraw'=>'Rút tiền'];
                                $typeIcons = ['deposit'=>'plus-circle','purchase'=>'shopping-cart','refund'=>'undo','withdraw'=>'arrow-circle-down'];
                                $typeColors = ['deposit'=>'#10b981','purchase'=>'#ef4444','refund'=>'#3b82f6','withdraw'=>'#f59e0b'];
                                $statusLabels = ['pending'=>'Đang chờ','completed'=>'Hoàn tất','rejected'=>'Từ chối'];
                                $statusColors = ['pending'=>'#f59e0b','completed'=>'#10b981','rejected'=>'#ef4444'];
                                $isIncome = in_array($tx['type'], ['deposit', 'refund']);
                            ?>
                            <tr>
                                <td style="font-size:0.85rem; white-space:nowrap;"><?= date('d/m/Y H:i', strtotime($tx['created_at'])) ?></td>
                                <td>
                                    <span style="display:inline-flex; align-items:center; gap:4px; color:<?= $typeColors[$tx['type']] ?>; font-weight:600; font-size:0.85rem;">
                                        <i class="fas fa-<?= $typeIcons[$tx['type']] ?>"></i>
                                        <?= $typeLabels[$tx['type']] ?>
                                    </span>
                                </td>
                                <td style="font-size:0.85rem; max-width:200px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                    <?= htmlspecialchars($tx['description'] ?? '') ?>
                                </td>
                                <td style="font-weight:700; color:<?= $isIncome ? '#10b981' : '#ef4444' ?>; white-space:nowrap;">
                                    <?= $isIncome ? '+' : '-' ?><?= number_format($tx['amount']) ?>đ
                                </td>
                                <td style="font-size:0.85rem; white-space:nowrap;">
                                    <?= $tx['status'] === 'completed' ? number_format($tx['balance_after']) . 'đ' : '—' ?>
                                </td>
                                <td>
                                    <span style="background:<?= $statusColors[$tx['status']] ?>20; color:<?= $statusColors[$tx['status']] ?>; padding:3px 10px; border-radius:20px; font-size:0.75rem; font-weight:600;">
                                        <?= $statusLabels[$tx['status']] ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
