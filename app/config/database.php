<?php

/**
 * Database Configuration
 * Kết nối MySQL bằng PDO
 */

// Thông tin kết nối database (có thể override trong .env)
$env = static function ($key, $default = '') {
    if (class_exists('Env')) {
        return Env::get($key, $default);
    }

    $value = getenv($key);

    return $value === false ? $default : $value;
};

defined('DB_HOST') || define('DB_HOST', $env('DB_HOST', 'localhost'));
defined('DB_NAME') || define('DB_NAME', $env('DB_NAME', 'english_master'));
defined('DB_USER') || define('DB_USER', $env('DB_USER', 'root'));
defined('DB_PASS') || define('DB_PASS', $env('DB_PASS', ''));
defined('DB_CHARSET') || define('DB_CHARSET', $env('DB_CHARSET', 'utf8mb4'));

/**
 * Lấy kết nối PDO (Singleton pattern)
 * @return PDO
 */
function getDB()
{
    static $pdo = null;

    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            error_log('Database connection error: ' . $e->getMessage());
            die('Không thể kết nối cơ sở dữ liệu. Vui lòng liên hệ quản trị viên.');
        }
    }

    return $pdo;
}
