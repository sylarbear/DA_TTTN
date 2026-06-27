<?php

/**
 * Router
 * Phân tích URL và resolve Controller/Method/Params
 * Tách từ App.php để dễ test và mở rộng
 */
class Router
{
    /** @var array Các method bị cấm gọi từ URL */
    private static $blockedMethods = [
        'handleLogin', 'handleRegister', 'validateRegister',
        'normalizeWords', 'generateFeedback', 'generateUniqueUsername',
        'getUserBadges', 'getXPAmounts',
        'model', 'view', 'viewPartial', 'json', 'redirect',
        'setFlash', 'isMethod', 'input', 'query',
    ];

    /**
     * Phân tích URL và trả về route info
     * @return array ['controller' => object, 'method' => string, 'params' => array]
     */
    public static function resolve(): array
    {
        $url = self::parseUrl();

        // Xác định Controller
        $controller = self::resolveController($url);

        // Xác định Method
        $method = self::resolveMethod($controller, $url);

        // Lấy params còn lại
        $params = $url ? array_values($url) : [];

        // Admin restriction
        self::enforceAdminRestriction($controller);

        return [
            'controller' => $controller,
            'method' => $method,
            'params' => $params,
        ];
    }

    /**
     * Parse URL từ query string
     * @return array
     */
    private static function parseUrl(): array
    {
        if (isset($_GET['url'])) {
            return explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL));
        }

        return [];
    }

    /**
     * Resolve controller từ URL segment đầu tiên
     * @param  array  $url (passed by reference — element 0 sẽ bị xóa nếu match)
     * @return object
     */
    private static function resolveController(array &$url)
    {
        $controllerName = !empty($url[0]) ? ucfirst(strtolower($url[0])) : DEFAULT_CONTROLLER;
        $controllerName = preg_replace('/[^a-zA-Z]/', '', $controllerName);
        $controllerFile = APP_PATH . "/controllers/{$controllerName}Controller.php";

        if (file_exists($controllerFile)) {
            $className = $controllerName . 'Controller';
            unset($url[0]);
        } else {
            $className = DEFAULT_CONTROLLER . 'Controller';
        }

        require_once APP_PATH . "/controllers/{$className}.php";

        return new $className();
    }

    /**
     * Resolve method từ URL segment thứ hai (case-insensitive)
     * @param  object $controller
     * @param  array  $url (passed by reference — element 1 sẽ bị xóa nếu match)
     * @return string
     */
    private static function resolveMethod($controller, array &$url): string
    {
        if (!isset($url[1])) {
            return self::validateMethod($controller, DEFAULT_METHOD);
        }

        $methodName = preg_replace('/[^a-zA-Z0-9]/', '', $url[1]);

        if (empty($methodName)) {
            return DEFAULT_METHOD;
        }

        // Exact match trước
        if (method_exists($controller, $methodName)) {
            $resolved = $methodName;
        } else {
            // Case-insensitive fallback
            $resolved = null;
            $reflection = new ReflectionClass($controller);
            foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                if (strtolower($method->getName()) === strtolower($methodName)) {
                    $resolved = $method->getName();
                    break;
                }
            }
        }

        if ($resolved
            && self::isPublicMethod($controller, $resolved)
            && !self::isBlockedMethod($resolved)) {
            unset($url[1]);

            return $resolved;
        }

        // Method không hợp lệ → redirect home
        header('Location: ' . BASE_URL);
        exit;
    }

    /**
     * Validate method tồn tại, public, và không bị chặn
     * @param  object $controller
     * @param  string $method
     * @return string
     */
    private static function validateMethod($controller, string $method): string
    {
        if ($method === 'index' && !method_exists($controller, 'index')) {
            header('Location: ' . BASE_URL);
            exit;
        }

        return $method;
    }

    /**
     * @param  object $controller
     * @param  string $methodName
     * @return bool
     */
    private static function isPublicMethod($controller, string $methodName): bool
    {
        try {
            $reflection = new ReflectionMethod($controller, $methodName);

            return $reflection->isPublic();
        } catch (ReflectionException $e) {
            return false;
        }
    }

    /**
     * @param  string $methodName
     * @return bool
     */
    private static function isBlockedMethod(string $methodName): bool
    {
        if (strpos($methodName, '__') === 0) {
            return true;
        }

        return in_array($methodName, self::$blockedMethods);
    }

    /**
     * Admin chỉ được dùng AdminController hoặc AuthController
     * @param object $controller
     */
    private static function enforceAdminRestriction($controller): void
    {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            return;
        }

        $allowed = ['AdminController', 'AuthController'];
        if (!in_array(get_class($controller), $allowed)) {
            header('Location: ' . BASE_URL . '/admin');
            exit;
        }
    }
}
