<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="EnglishMaster - Hệ thống học tiếng Anh trực tuyến theo chủ đề, tích hợp đánh giá kỹ năng nói bằng AI">
    <title><?= $title ?? APP_NAME ?></title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <!-- Main CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/components.css?v=<?= time() ?>">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/pages.css?v=<?= time() ?>">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar" id="navbar">
        <div class="nav-container">
            <a href="<?= BASE_URL ?>" class="nav-logo">
                <i class="fas fa-graduation-cap"></i>
                <span><?= APP_NAME ?></span>
            </a>

            <button class="nav-toggle" id="navToggle" aria-label="Toggle navigation">
                <span></span><span></span><span></span>
            </button>

            <ul class="nav-menu" id="navMenu">
                <li><a href="<?= BASE_URL ?>" class="nav-link"><i class="fas fa-home"></i> Trang chủ</a></li>
                <li><a href="<?= BASE_URL ?>/topic" class="nav-link"><i class="fas fa-book-open"></i> Chủ đề</a></li>
                <li><a href="<?= BASE_URL ?>/test" class="nav-link"><i class="fas fa-clipboard-check"></i> Bài test</a></li>
                <li><a href="<?= BASE_URL ?>/speaking" class="nav-link"><i class="fas fa-microphone"></i> Luyện nói</a></li>
                
                <?php if (Middleware::isLoggedIn()): ?>
                    <li><a href="<?= BASE_URL ?>/dashboard" class="nav-link"><i class="fas fa-chart-line"></i> Dashboard</a></li>
                    <li><a href="<?= BASE_URL ?>/grammar" class="nav-link"><i class="fas fa-graduation-cap"></i> Ngữ pháp</a></li>
                    <li><a href="<?= BASE_URL ?>/leaderboard" class="nav-link"><i class="fas fa-trophy"></i> Xếp hạng</a></li>
                    <?php if (!Middleware::isPro()): ?>
                        <li><a href="<?= BASE_URL ?>/membership" class="nav-link nav-upgrade-link"><i class="fas fa-crown"></i> Nâng cấp Pro</a></li>
                    <?php endif; ?>
                    <li class="nav-user">
                        <div class="user-dropdown">
                            <button class="user-btn" id="userDropdownBtn">
                                <i class="fas fa-user-circle"></i>
                                <span><?= htmlspecialchars($_SESSION['full_name'] ?? $_SESSION['username']) ?></span>
                                <?php if (Middleware::isPro()): ?>
                                    <span class="nav-pro-badge">PRO</span>
                                <?php endif; ?>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                            <div class="dropdown-menu" id="userDropdown">
                                <a href="<?= BASE_URL ?>/profile"><i class="fas fa-user"></i> Hồ sơ cá nhân</a>
                                <a href="<?= BASE_URL ?>/bookmark"><i class="fas fa-bookmark"></i> Từ đã lưu</a>
                                <a href="<?= BASE_URL ?>/dashboard"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                                <a href="<?= BASE_URL ?>/membership"><i class="fas fa-crown"></i> <?= Middleware::isPro() ? 'Quản lý Pro' : 'Nâng cấp Pro' ?></a>
                                <?php if (Middleware::isAdmin()): ?>
                                    <a href="<?= BASE_URL ?>/admin" style="color:var(--primary)!important;"><i class="fas fa-shield-alt"></i> Admin Panel</a>
                                <?php endif; ?>
                                <a href="<?= BASE_URL ?>/auth/logout"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
                            </div>
                        </div>
                    </li>
                <?php else: ?>
                    <li><a href="<?= BASE_URL ?>/auth/login" class="nav-link btn-login"><i class="fas fa-sign-in-alt"></i> Đăng nhập</a></li>
                    <li><a href="<?= BASE_URL ?>/auth/register" class="nav-link btn-register"><i class="fas fa-user-plus"></i> Đăng ký</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <!-- Flash Messages -->
    <?php if (isset($_SESSION['flash'])): ?>
        <div class="flash-message flash-<?= $_SESSION['flash']['type'] ?>" id="flashMessage">
            <div class="flash-content">
                <?php if ($_SESSION['flash']['type'] === 'success'): ?>
                    <i class="fas fa-check-circle"></i>
                <?php elseif ($_SESSION['flash']['type'] === 'error'): ?>
                    <i class="fas fa-exclamation-circle"></i>
                <?php else: ?>
                    <i class="fas fa-info-circle"></i>
                <?php endif; ?>
                <span><?= $_SESSION['flash']['message'] ?></span>
                <button class="flash-close" onclick="this.parentElement.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>

    <!-- Main Content -->
    <main class="main-content">
