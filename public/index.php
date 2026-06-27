<?php

/**
 * Front Controller - Entry Point
 * Tất cả request đều đi qua file này
 */

// Định nghĩa đường dẫn gốc
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('PUBLIC_PATH', __DIR__);

// Composer autoload
require_once ROOT_PATH . '/vendor/autoload.php';

// Backward compatibility class aliases
require_once APP_PATH . '/config/aliases.php';

// Load .env trước khi load config
Env::load(ROOT_PATH);

// Load config
require_once APP_PATH . '/config/app.php';
require_once APP_PATH . '/config/database.php';

// Bật/tắt hiển thị lỗi dựa trên APP_ENV
if (APP_ENV === 'development') {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(E_ALL); // vẫn log lỗi
    ini_set('log_errors', '1');
}

// Bắt đầu session với cấu hình bảo mật
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
           || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);
session_set_cookie_params([
    'lifetime' => 3600,      // 1 giờ
    'path' => '/',
    'httponly' => true,      // Không cho JS truy cập cookie
    'samesite' => 'Lax',    // Chống CSRF
    'secure' => $isHttps,  // Chỉ gửi cookie qua HTTPS trên production
]);
session_start();

// Khởi chạy ứng dụng
$app = new App();
