<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="EngPath - nền tảng học tiếng Anh theo lộ trình, tích hợp từ vựng, bài học và kiểm tra.">
    <title><?= $title ?? APP_NAME ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300..800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/design-system.css?v=<?= APP_VERSION ?>">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/pages.css?v=<?= APP_VERSION ?>">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/components-v2.css?v=<?= APP_VERSION ?>">
    <style>
        /* Fix: old CSS has white elements for dark header. Override for visibility. */
        .navbar { background:#fff !important; box-shadow:0 1px 3px rgba(0,0,0,0.08) !important; }
        .nav-logo, .nav-logo span { color:#1e293b !important; }
        .nav-link { color:#475569 !important; }
        .nav-link:hover { color:#4f46e5 !important; background:#eef2ff !important; }
        .nav-search input { background:#f1f5f9 !important; border-color:#e2e8f0 !important; color:#1e293b !important; }
        .nav-search input::placeholder { color:#94a3b8 !important; }
        .nav-search > i { color:#94a3b8 !important; }
        .nav-search input:focus { background:#fff !important; border-color:#4f46e5 !important; }
        .user-btn { background:transparent !important; border-color:#e2e8f0 !important; color:#1e293b !important; }
        .user-btn:hover { border-color:#4f46e5 !important; background:#eef2ff !important; }
        .nav-toggle span { background:#1e293b !important; }
        .btn-login { color:#1e293b !important; border-color:#cbd5e1 !important; }
        .btn-login:hover { background:#f1f5f9 !important; }
        .btn-register { background:#4f46e5 !important; color:#fff !important; }
        .btn-register:hover { background:#4338ca !important; }
        .nav-more-btn { color:#475569 !important; }
        .nav-more-btn:hover { color:#4f46e5 !important; background:#eef2ff !important; border-color:#cbd5e1 !important; }
        .nav-pro-badge { background:linear-gradient(135deg,#f59e0b,#f97316) !important; color:#fff !important; }
        @media (max-width:768px) {
            .nav-menu { background:#fff !important; border-color:#e2e8f0 !important; }
            .nav-link { color:#1e293b !important; }
        }
    </style>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            brand: {
              50: '#eef2ff', 100:'#e0e7ff', 200:'#c7d2fe', 300:'#a5b4fc',
              400:'#818cf8', 500:'#6366f1', 600:'#4f46e5', 700:'#4338ca', 800:'#3730a3'
            },
            navy: { 500:'#64748b', 700:'#334155', 800:'#1e293b', 900:'#0f172a' }
          },
          fontFamily: { heading: ['"Plus Jakarta Sans"','Inter','sans-serif'], body: ['Inter','sans-serif'] }
        }
      }
    }
    </script>
    <script>window.BASE_URL = '<?= BASE_URL ?>';</script>
</head>
<body>
    <a href="#main-content" class="skip-link">Bỏ qua điều hướng</a>
    <nav class="navbar" id="navbar">
        <div class="nav-container">
            <a href="<?= BASE_URL ?>" class="nav-logo">
                <span class="brand-mark"><i class="fas fa-route"></i></span>
                <span><?= APP_NAME ?></span>
            </a>

            <button class="nav-toggle" id="navToggle" aria-label="Mở menu">
                <span></span><span></span><span></span>
            </button>

            <ul class="nav-menu" id="navMenu">
                <?php if (Middleware::isAdmin()): ?>
                    <li><a href="<?= BASE_URL ?>/admin" class="nav-link"><i class="fas fa-gauge-high"></i> Admin</a></li>
                    <li><a href="<?= BASE_URL ?>/admin/users" class="nav-link"><i class="fas fa-users"></i> Users</a></li>
                    <li><a href="<?= BASE_URL ?>/admin/topics" class="nav-link"><i class="fas fa-book-open"></i> Khóa học</a></li>
                    <li><a href="<?= BASE_URL ?>/admin/questions" class="nav-link"><i class="fas fa-circle-question"></i> Câu hỏi</a></li>
                    <li><a href="<?= BASE_URL ?>/admin/orders" class="nav-link"><i class="fas fa-file-invoice-dollar"></i> Đơn</a></li>
                    <li><a href="<?= BASE_URL ?>/admin/tickets" class="nav-link"><i class="fas fa-headset"></i> Hỗ trợ</a></li>
                    <li><a href="<?= BASE_URL ?>/auth/logout" class="nav-link btn-login"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a></li>
                <?php else: ?>
                    <li><a href="<?= BASE_URL ?>" class="nav-link"><i class="fas fa-home"></i> Trang chủ</a></li>
                    <li><a href="<?= BASE_URL ?>/course" class="nav-link"><i class="fas fa-book-open"></i> Khóa học</a></li>
                    <li><a href="<?= BASE_URL ?>/membership" class="nav-link"><i class="fas fa-crown"></i> Pro</a></li>

                    <?php if (Middleware::isLoggedIn()): ?>
                        <li class="nav-more">
                            <button class="nav-link nav-more-btn" id="navMoreBtn"><i class="fas fa-border-all"></i> Thêm <i class="fas fa-chevron-down" style="font-size:0.65rem;"></i></button>
                            <div class="nav-more-menu" id="navMoreMenu">
                                <a href="<?= BASE_URL ?>/dashboard"><i class="fas fa-chart-line"></i> Dashboard</a>
                                <a href="<?= BASE_URL ?>/leaderboard"><i class="fas fa-trophy"></i> Xếp hạng</a>
                                <a href="<?= BASE_URL ?>/support"><i class="fas fa-headset"></i> Hỗ trợ</a>
                                <?php if (!Middleware::isPro()): ?>
                                    <a href="<?= BASE_URL ?>/membership" class="nav-more-upgrade"><i class="fas fa-crown"></i> Nâng cấp Pro</a>
                                <?php endif; ?>
                            </div>
                        </li>

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
                                    <a href="<?= BASE_URL ?>/placement/intro"><i class="fas fa-clipboard-check"></i> Kiểm tra đầu vào</a>
                                    <a href="<?= BASE_URL ?>/membership"><i class="fas fa-crown"></i> <?= Middleware::isPro() ? 'Quản lý Pro' : 'Nâng cấp Pro' ?></a>
                                    <a href="<?= BASE_URL ?>/auth/logout"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
                                </div>
                            </div>
                        </li>
                    <?php else: ?>
                        <li><a href="<?= BASE_URL ?>/auth/login" class="nav-link btn-login"><i class="fas fa-sign-in-alt"></i> Đăng nhập</a></li>
                        <li><a href="<?= BASE_URL ?>/auth/register" class="nav-link btn-register"><i class="fas fa-user-plus"></i> Học miễn phí</a></li>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>

            <?php if (!Middleware::isAdmin()): ?>
            <div class="nav-search" id="navSearch">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Tìm kiếm..." autocomplete="off">
                <div class="search-dropdown" id="searchDropdown">
                    <div class="search-results" id="searchResults"></div>
                    <div class="search-empty" id="searchEmpty">
                        <i class="fas fa-search"></i>
                        <span>Nhập ít nhất 2 ký tự để tìm kiếm</span>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </nav>

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
                <span><?= htmlspecialchars($_SESSION['flash']['message']) ?></span>
                <button class="flash-close" onclick="this.parentElement.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>

    <main class="main-content" id="main-content">
