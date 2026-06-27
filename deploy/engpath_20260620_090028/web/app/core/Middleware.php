<?php
/**
 * Middleware
 * Xử lý xác thực và phân quyền
 */
class Middleware {

    /**
     * Kiểm tra user đã đăng nhập chưa
     * Redirect đến login nếu chưa
     */
    public static function requireLogin() {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['flash'] = [
                'type' => 'warning',
                'message' => 'Vui lòng đăng nhập để tiếp tục.'
            ];
            header("Location: " . BASE_URL . "/auth/login");
            exit;
        }
    }

    /**
     * Kiểm tra user có phải admin không
     */
    public static function requireAdmin() {
        self::requireLogin();
        if ($_SESSION['user_role'] !== 'admin') {
            $_SESSION['flash'] = [
                'type' => 'error',
                'message' => 'Bạn không có quyền truy cập trang này.'
            ];
            header("Location: " . BASE_URL);
            exit;
        }
    }

    /**
     * Chỉ cho phép học viên dùng các luồng học tập.
     * Admin được chuyển về khu quản trị để tránh lẫn vai trò.
     */
    public static function requireStudent() {
        self::requireLogin();
        if (self::isAdmin()) {
            header("Location: " . BASE_URL . "/admin");
            exit;
        }
    }

    /**
     * Chặn admin truy cập các trang dành cho học viên/public app.
     */
    public static function redirectAdminToPanel() {
        if (self::isAdmin()) {
            header("Location: " . BASE_URL . "/admin");
            exit;
        }
    }

    /**
     * Kiểm tra user có phải Pro không
     * Redirect đến trang nâng cấp nếu chưa
     */
    public static function requirePro() {
        self::requireLogin();
        if (!self::isPro()) {
            $_SESSION['flash'] = [
                'type' => 'warning',
                'message' => 'Tính năng này dành cho hội viên Pro. Vui lòng nâng cấp để sử dụng.'
            ];
            header("Location: " . BASE_URL . "/membership");
            exit;
        }
    }

    /**
     * Kiểm tra user đã login → redirect về home
     */
    public static function guest() {
        if (isset($_SESSION['user_id'])) {
            $target = self::isAdmin() ? BASE_URL . "/admin" : BASE_URL;
            header("Location: " . $target);
            exit;
        }
    }

    /**
     * Lấy thông tin user hiện tại từ session
     * @return array|null
     */
    public static function user() {
        if (isset($_SESSION['user_id'])) {
            return [
                'id' => $_SESSION['user_id'],
                'username' => $_SESSION['username'],
                'full_name' => $_SESSION['full_name'],
                'email' => $_SESSION['email'],
                'role' => $_SESSION['user_role'],
                'avatar' => $_SESSION['avatar'] ?? 'default.png',
                'membership' => $_SESSION['membership'] ?? 'free',
                'membership_expired_at' => $_SESSION['membership_expired_at'] ?? null
            ];
        }
        return null;
    }

    /**
     * Kiểm tra đã đăng nhập chưa (không redirect)
     */
    public static function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    /**
     * Kiểm tra có phải admin không (không redirect)
     */
    public static function isAdmin() {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }

    /**
     * Kiểm tra có phải Pro không (không redirect)
     */
    public static function isPro() {
        if (!isset($_SESSION['user_id'])) return false;
        if (self::isAdmin()) return false;
        if (($_SESSION['membership'] ?? 'free') !== 'pro') return false;
        // Kiểm tra hết hạn
        $expired = $_SESSION['membership_expired_at'] ?? null;
        if ($expired && strtotime($expired) < time()) return false;
        return true;
    }
}
