<?php
/**
 * Front Controller - Entry Point
 * Tất cả request đều đi qua file này
 */

// Bật hiển thị lỗi khi phát triển
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Bắt đầu session với cấu hình bảo mật
session_set_cookie_params([
    'lifetime' => 3600,      // 1 giờ
    'path'     => '/',
    'httponly'  => true,      // Không cho JS truy cập cookie
    'samesite'  => 'Lax',    // Chống CSRF
]);
session_start();

// Định nghĩa đường dẫn gốc
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('PUBLIC_PATH', __DIR__);

// Load config
require_once APP_PATH . '/config/app.php';
require_once APP_PATH . '/config/database.php';

// Load core classes
require_once APP_PATH . '/core/Model.php';
require_once APP_PATH . '/core/Controller.php';
require_once APP_PATH . '/core/Middleware.php';
require_once APP_PATH . '/core/App.php';

// Khởi chạy ứng dụng
$app = new App();
