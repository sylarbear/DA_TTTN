<?php

use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger as MonologLogger;

/**
 * Logger helper — tạo logger instance với cấu hình chuẩn
 * Dùng Monolog để ghi log ra file với rotation 30 ngày
 */
class Logger
{
    /** @var MonologLogger|null */
    private static $instance;

    /**
     * @return MonologLogger
     */
    public static function get()
    {
        if (self::$instance !== null) {
            return self::$instance;
        }

        $logDir = APP_PATH . '/../storage/logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        $logger = new MonologLogger('engpath');

        // File log với rotation (giữ 30 ngày)
        $logger->pushHandler(new RotatingFileHandler(
            $logDir . '/app.log',
            30,
            APP_ENV === 'development' ? MonologLogger::DEBUG : MonologLogger::WARNING
        ));

        // Trong development, cũng log ra stderr
        if (APP_ENV === 'development') {
            $logger->pushHandler(new StreamHandler('php://stderr', MonologLogger::DEBUG));
        }

        self::$instance = $logger;

        return self::$instance;
    }

    /**
     * @param  string     $message
     * @param  array      $context
     */
    public static function info($message, array $context = [])
    {
        self::get()->info($message, $context);
    }

    /**
     * @param  string     $message
     * @param  array      $context
     */
    public static function warning($message, array $context = [])
    {
        self::get()->warning($message, $context);
    }

    /**
     * @param  string     $message
     * @param  array      $context
     */
    public static function error($message, array $context = [])
    {
        self::get()->error($message, $context);
    }

    /**
     * @param  string     $message
     * @param  array      $context
     */
    public static function debug($message, array $context = [])
    {
        self::get()->debug($message, $context);
    }
}
