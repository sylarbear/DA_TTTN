<?php
$adminActive = $adminActive ?? 'dashboard';
$adminTitle = $adminTitle ?? 'Admin Dashboard';
$adminSubtitle = $adminSubtitle ?? 'Quản lý hệ thống English Learning';

$adminBadges = ['orders' => 0, 'tickets' => 0, 'wallet' => 0];
try {
    $adminDb = getDB();
    $adminBadges['orders'] = (int) $adminDb->query("SELECT COUNT(*) FROM membership_orders WHERE status='pending'")->fetchColumn();
    $adminBadges['tickets'] = (int) $adminDb->query("SELECT COUNT(*) FROM support_tickets WHERE status IN ('open','in_progress')")->fetchColumn();
    $adminBadges['wallet'] = (int) $adminDb->query("SELECT COUNT(*) FROM wallet_transactions WHERE status='pending'")->fetchColumn();
} catch (Exception $e) {
    $adminBadges = ['orders' => 0, 'tickets' => 0, 'wallet' => 0];
}

$adminNavItems = [
    ['key' => 'dashboard', 'href' => BASE_URL . '/admin', 'icon' => 'fa-gauge-high', 'label' => 'Dashboard'],
    ['key' => 'users', 'href' => BASE_URL . '/admin/users', 'icon' => 'fa-users', 'label' => 'Users'],
    ['key' => 'topics', 'href' => BASE_URL . '/admin/topics', 'icon' => 'fa-book-open', 'label' => 'Khóa học'],
    ['key' => 'questions', 'href' => BASE_URL . '/admin/questions', 'icon' => 'fa-circle-question', 'label' => 'Câu hỏi'],
    ['key' => 'codes', 'href' => BASE_URL . '/admin/codes', 'icon' => 'fa-key', 'label' => 'Mã kích hoạt'],
    ['key' => 'orders', 'href' => BASE_URL . '/admin/orders', 'icon' => 'fa-file-invoice-dollar', 'label' => 'Đơn nâng cấp', 'badge' => $adminBadges['orders']],
    ['key' => 'tickets', 'href' => BASE_URL . '/admin/tickets', 'icon' => 'fa-headset', 'label' => 'Tickets', 'badge' => $adminBadges['tickets']],
    ['key' => 'wallet', 'href' => BASE_URL . '/admin/walletTransactions', 'icon' => 'fa-wallet', 'label' => 'Ví', 'badge' => $adminBadges['wallet']],
    ['key' => 'settings', 'href' => BASE_URL . '/admin/settings', 'icon' => 'fa-gear', 'label' => 'Cài đặt'],
];
?>

<section class="admin-hero">
    <div class="container admin-hero-inner">
        <div>
            <span class="admin-eyebrow"><i class="fas fa-shield-halved"></i> Admin Workspace</span>
            <h1><?= htmlspecialchars($adminTitle) ?></h1>
            <p><?= htmlspecialchars($adminSubtitle) ?></p>
        </div>
        <a href="<?= BASE_URL ?>/auth/logout" class="btn btn-outline admin-logout">
            <i class="fas fa-sign-out-alt"></i> Đăng xuất
        </a>
    </div>
</section>

<section class="admin-nav-shell">
    <div class="container">
        <div class="admin-nav">
            <?php foreach ($adminNavItems as $item): ?>
                <a href="<?= $item['href'] ?>" class="admin-nav-item <?= $adminActive === $item['key'] ? 'active' : '' ?>">
                    <i class="fas <?= $item['icon'] ?>"></i>
                    <span><?= htmlspecialchars($item['label']) ?></span>
                    <?php if (!empty($item['badge'])): ?>
                        <b class="admin-badge"><?= (int) $item['badge'] ?></b>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
