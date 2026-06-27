<?php

/**
 * CSRF — Cross-Site Request Forgery Protection
 * Tự động tạo token cho form và verify khi POST
 *
 * Usage:
 *   Trong form:  <input type="hidden" name="_csrf" value="<?= CSRF::token() ?>">
 *   Trong controller:  CSRF::verify();  // throws nếu không hợp lệ
 */
class CSRF
{
    /** @var string */
    private const TOKEN_KEY = '_csrf_token';

    /**
     * Tạo hoặc lấy CSRF token cho session hiện tại
     * @return string
     */
    public static function token(): string
    {
        if (empty($_SESSION[self::TOKEN_KEY])) {
            $_SESSION[self::TOKEN_KEY] = bin2hex(random_bytes(32));
        }

        return $_SESSION[self::TOKEN_KEY];
    }

    /**
     * Xác thực CSRF token từ POST data
     * @param  string|null $token Token từ request (nếu null, tự lấy từ $_POST['_csrf'])
     * @return bool
     */
    public static function verify(?string $token = null): bool
    {
        if ($token === null) {
            $token = $_POST['_csrf'] ?? '';
        }

        if (empty($_SESSION[self::TOKEN_KEY])) {
            return false;
        }

        return hash_equals($_SESSION[self::TOKEN_KEY], $token);
    }

    /**
     * Verify CSRF và redirect nếu fail
     */
    public static function verifyOrFail(): void
    {
        if (!self::verify()) {
            $_SESSION['flash'] = [
                'type' => 'error',
                'message' => 'Phiên làm việc đã hết hạn. Vui lòng thử lại.',
            ];
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? BASE_URL));
            exit;
        }
    }

    /**
     * Tạo hidden input field cho form
     * @return string
     */
    public static function hiddenField(): string
    {
        return '<input type="hidden" name="_csrf" value="' . self::token() . '">';
    }

    /**
     * Xóa token (dùng sau khi xử lý thành công, tùy chọn)
     */
    public static function regenerate(): void
    {
        unset($_SESSION[self::TOKEN_KEY]);
    }
}
