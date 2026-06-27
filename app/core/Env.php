<?php


/**
 * Simple .env file loader (không cần Composer / third-party)
 * Load các biến từ file .env vào getenv() và $_ENV
 */
class Env
{
    /** @var bool Đã load chưa */
    private static $loaded = false;

    /**
     * Load .env file từ thư mục gốc project
     * @param string|null $path Đường dẫn tới thư mục chứa .env
     */
    public static function load($path = null)
    {
        if (self::$loaded) {
            return;
        }

        $path = $path ?: (defined('ROOT_PATH') ? ROOT_PATH : dirname(__DIR__, 2));
        $file = rtrim($path, '/\\') . DIRECTORY_SEPARATOR . '.env';

        if (!file_exists($file)) {
            self::$loaded = true;

            return;
        }

        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);

            // Bỏ qua comment và dòng trống
            if ($line === '' || strpos($line, '#') === 0) {
                continue;
            }

            // Parse KEY=VALUE
            if (strpos($line, '=') === false) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            // Bỏ quote nếu có
            if ((strpos($value, '"') === 0 && strrpos($value, '"') === strlen($value) - 1)
                || (strpos($value, "'") === 0 && strrpos($value, "'") === strlen($value) - 1)) {
                $value = substr($value, 1, -1);
            }

            // Chỉ set nếu chưa có trong environment
            if (getenv($key) === false && !isset($_ENV[$key])) {
                putenv("{$key}={$value}");
                $_ENV[$key] = $value;
            }
        }

        self::$loaded = true;
    }

    /**
     * Lấy giá trị từ environment (đã load từ .env)
     * @param  string      $key
     * @param  string|null $default
     * @return string|null
     */
    public static function get($key, $default = null)
    {
        self::load();

        $value = getenv($key);
        if ($value !== false) {
            return $value;
        }

        return $_ENV[$key] ?? $default;
    }
}
