<?php

/**
 * Middleware
 * Xử lý xác thực và phân quyền
 */
class Middleware
{
    public static function requireLogin(): void
    {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['flash'] = [
                'type' => 'warning',
                'message' => 'Vui lòng đăng nhập để tiếp tục.',
            ];
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }
    }

    public static function requireAdmin(): void
    {
        self::requireLogin();
        if ($_SESSION['user_role'] !== 'admin') {
            $_SESSION['flash'] = [
                'type' => 'error',
                'message' => 'Bạn không có quyền truy cập trang này.',
            ];
            header('Location: ' . BASE_URL);
            exit;
        }
    }

    public static function requireStudent(): void
    {
        self::requireLogin();
        if (self::isAdmin()) {
            header('Location: ' . BASE_URL . '/admin');
            exit;
        }
    }

    public static function redirectAdminToPanel(): void
    {
        if (self::isAdmin()) {
            header('Location: ' . BASE_URL . '/admin');
            exit;
        }
    }

    public static function requirePro(): void
    {
        self::requireLogin();
        if (!self::isPro()) {
            $_SESSION['flash'] = [
                'type' => 'warning',
                'message' => 'Tính năng này dành cho hội viên Pro. Vui lòng nâng cấp để sử dụng.',
            ];
            header('Location: ' . BASE_URL . '/membership');
            exit;
        }
    }

    public static function guest(): void
    {
        if (isset($_SESSION['user_id'])) {
            $target = self::isAdmin() ? BASE_URL . '/admin' : BASE_URL;
            header('Location: ' . $target);
            exit;
        }
    }

    /**
     * Lấy thông tin user hiện tại từ session
     * @return array|null
     */
    public static function user(): ?array
    {
        if (isset($_SESSION['user_id'])) {
            return [
                'id' => $_SESSION['user_id'],
                'username' => $_SESSION['username'],
                'full_name' => $_SESSION['full_name'],
                'email' => $_SESSION['email'],
                'role' => $_SESSION['user_role'],
                'avatar' => $_SESSION['avatar'] ?? 'default.png',
                'membership' => $_SESSION['membership'] ?? 'free',
                'membership_expired_at' => $_SESSION['membership_expired_at'] ?? null,
            ];
        }

        return null;
    }

    public static function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']);
    }

    public static function isAdmin(): bool
    {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }

    public static function isPro(): bool
    {
        if (!isset($_SESSION['user_id'])) {
            return false;
        }
        if (self::isAdmin()) {
            return false;
        }
        if (($_SESSION['membership'] ?? 'free') !== 'pro') {
            return false;
        }
        $expired = $_SESSION['membership_expired_at'] ?? null;
        if ($expired && strtotime($expired) < time()) {
            return false;
        }

        return true;
    }
}
