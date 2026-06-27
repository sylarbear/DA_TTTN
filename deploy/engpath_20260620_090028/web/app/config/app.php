<?php
/**
 * App Configuration
 * Cấu hình chung cho ứng dụng
 */

// Thông tin ứng dụng
define('APP_NAME', 'EngPath');
define('APP_VERSION', '1.0.0');

// Load secrets từ env.php TRƯỚC (file không push lên GitHub)
if (file_exists(__DIR__ . '/env.php')) {
    require_once __DIR__ . '/env.php';
} else {
    // Fallback: placeholder values nếu chưa tạo env.php
    defined('GOOGLE_CLIENT_ID') || define('GOOGLE_CLIENT_ID', '');
    defined('GOOGLE_CLIENT_SECRET') || define('GOOGLE_CLIENT_SECRET', '');
    defined('CASSO_API_KEY') || define('CASSO_API_KEY', '');
    defined('CASSO_WEBHOOK_SECRET') || define('CASSO_WEBHOOK_SECRET', '');
}

// URL gốc - tự động detect môi trường
if (defined('SITE_URL')) {
    define('BASE_URL', SITE_URL);
} elseif (isset($_SERVER['HTTP_HOST'])) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    if (strpos($host, 'localhost') !== false || strpos($host, '127.0.0.1') !== false) {
        define('BASE_URL', $protocol . '://' . $host . '/DA_TTTN/public');
    } else {
        define('BASE_URL', $protocol . '://' . $host);
    }
} else {
    define('BASE_URL', 'http://localhost/DA_TTTN/public');
}

// Controller và method mặc định
define('DEFAULT_CONTROLLER', 'Home');
define('DEFAULT_METHOD', 'index');

// Cấu hình upload
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
define('ALLOWED_AUDIO_TYPES', ['audio/mpeg', 'audio/wav', 'audio/ogg']);

// Cấu hình session
define('SESSION_LIFETIME', 3600); // 1 giờ

// Cấu hình Speaking Score
define('ACCURACY_WEIGHT', 0.4);
define('FLUENCY_WEIGHT', 0.3);
define('PRONUNCIATION_WEIGHT', 0.3);

define('GOOGLE_REDIRECT_URI', BASE_URL . '/auth/googleCallback');

// Bank info (public, hiển thị cho user)
define('BANK_ACCOUNT_NO', '19036785007013'); // STK Techcombank
