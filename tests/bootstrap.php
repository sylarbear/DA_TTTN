<?php

/**
 * PHPUnit Bootstrap
 */

require_once __DIR__ . '/../vendor/autoload.php';

// Định nghĩa các constant cần thiết cho testing
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));
}
if (!defined('APP_PATH')) {
    define('APP_PATH', ROOT_PATH . '/app');
}
if (!defined('PUBLIC_PATH')) {
    define('PUBLIC_PATH', ROOT_PATH . '/public');
}
if (!defined('APP_ENV')) {
    define('APP_ENV', 'testing');
}
if (!defined('BASE_URL')) {
    define('BASE_URL', 'http://localhost');
}

// Load backward compat aliases (PSR-4 → global)
require_once APP_PATH . '/config/aliases.php';
