<?php

/**
 * RateLimiter — giới hạn số request trong một khoảng thời gian
 * Dùng session để lưu trạng thái (không cần Redis/Memcached)
 *
 * Usage:
 *   RateLimiter::throttle('login', 5, 60);  // Tối đa 5 lần / 60 giây
 *   if (RateLimiter::tooMany('login', 5, 60)) { ... }
 */
class RateLimiter
{
    /** @var string */
    private const SESSION_KEY = '_ratelimit';

    /**
     * Kiểm tra xem đã vượt quá giới hạn chưa. Nếu chưa, tăng counter.
     *
     * @param  string $key      Tên hành động (vd: 'login', 'register', 'api')
     * @param  int    $max      Số lần tối đa
     * @param  int    $seconds  Trong khoảng thời gian (giây)
     * @return bool  true nếu bị chặn
     */
    public static function tooMany(string $key, int $max = 10, int $seconds = 60): bool
    {
        $now = time();
        $bucket = $_SESSION[self::SESSION_KEY][$key] ?? ['count' => 0, 'reset_at' => 0];

        // Reset nếu hết thời gian
        if ($now >= $bucket['reset_at']) {
            $bucket = [
                'count' => 1,
                'reset_at' => $now + $seconds,
            ];
            $_SESSION[self::SESSION_KEY][$key] = $bucket;

            return false;
        }

        $bucket['count']++;
        $_SESSION[self::SESSION_KEY][$key] = $bucket;

        return $bucket['count'] > $max;
    }

    /**
     * Lấy số lần còn lại
     * @param  string $key
     * @param  int    $max
     * @param  int    $seconds
     * @return int
     */
    public static function remaining(string $key, int $max = 10, int $seconds = 60): int
    {
        $now = time();
        $bucket = $_SESSION[self::SESSION_KEY][$key] ?? ['count' => 0, 'reset_at' => 0];

        if ($now >= $bucket['reset_at']) {
            return $max;
        }

        return max(0, $max - $bucket['count']);
    }

    /**
     * Thời gian còn lại (giây) cho đến khi reset
     * @param  string $key
     * @return int
     */
    public static function resetIn(string $key): int
    {
        $now = time();
        $bucket = $_SESSION[self::SESSION_KEY][$key] ?? ['count' => 0, 'reset_at' => 0];

        return max(0, $bucket['reset_at'] - $now);
    }

    /**
     * Ghi đè giới hạn cho endpoint này (VD: cho admin)
     * Nếu vượt quá, set HTTP 429 và exit
     *
     * @param string $key
     * @param int    $max
     * @param int    $seconds
     */
    public static function throttle(string $key, int $max = 10, int $seconds = 60): void
    {
        if (self::tooMany($key, $max, $seconds)) {
            http_response_code(429);
            header('Content-Type: application/json; charset=utf-8');
            header('Retry-After: ' . self::resetIn($key));
            echo json_encode([
                'error' => 'Too many requests. Vui lòng thử lại sau.',
                'retry_after' => self::resetIn($key),
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
    }
}
