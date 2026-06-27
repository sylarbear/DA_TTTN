<?php
/**
 * App - Router & Dispatcher
 * Phân tích URL và gọi Controller/Method tương ứng
 * 
 * URL format: /controller/method/param1/param2
 */
class App {
    protected $controller = DEFAULT_CONTROLLER . 'Controller';
    protected $method = DEFAULT_METHOD;
    protected $params = [];

    /**
     * Các method cụ thể bị cấm gọi từ URL (internal-only methods)
     * Chỉ chặn method name chính xác, không dùng prefix (tránh false positive)
     */
    private static $blockedMethods = [
        'handleLogin', 'handleRegister', 'validateRegister',
        'processTransaction', 'logWebhook',
        'normalizeWords', 'generateFeedback', 'generateUniqueUsername',
        'getUserBadges', 'getXPAmounts',
        'model', 'view', 'viewPartial', 'json', 'redirect',
        'setFlash', 'isMethod', 'input', 'query'
    ];

    public function __construct() {
        $url = $this->parseUrl();

        // Xác định Controller (chỉ cho phép ký tự chữ cái để chống path traversal)
        $controllerName = !empty($url[0]) ? ucfirst(strtolower($url[0])) : DEFAULT_CONTROLLER;
        $controllerName = preg_replace('/[^a-zA-Z]/', '', $controllerName); // Chỉ cho phép a-zA-Z
        $controllerFile = APP_PATH . "/controllers/{$controllerName}Controller.php";

        if (file_exists($controllerFile)) {
            $this->controller = $controllerName . 'Controller';
            unset($url[0]);
        } else {
            // Controller không tồn tại → fallback về Home
            $this->controller = DEFAULT_CONTROLLER . 'Controller';
        }

        // Load controller
        require_once APP_PATH . "/controllers/{$this->controller}.php";
        $this->controller = new $this->controller;

        // Xác định Method (case-insensitive matching cho URL)
        if (isset($url[1])) {
            $methodName = preg_replace('/[^a-zA-Z0-9]/', '', $url[1]);

            if (!empty($methodName)) {
                // Tìm method khớp (case-insensitive) trong controller
                $resolvedMethod = $this->resolveMethod($methodName);

                if ($resolvedMethod 
                    && $this->isPublicMethod($resolvedMethod)
                    && !$this->isBlockedMethod($resolvedMethod)) {
                    $this->method = $resolvedMethod;
                    unset($url[1]);
                } else {
                    // Method không tồn tại → redirect về trang chủ
                    header("Location: " . BASE_URL);
                    exit;
                }
            }
        }

        // Kiểm tra method index tồn tại (fallback khi không có method trong URL)
        if ($this->method === 'index' && !method_exists($this->controller, 'index')) {
            header("Location: " . BASE_URL);
            exit;
        }

        // Lấy params còn lại
        $this->params = $url ? array_values($url) : [];

        // Admin chỉ làm việc trong khu quản trị; vẫn cho phép đăng xuất.
        if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
            $allowedAdminControllers = ['AdminController', 'AuthController'];
            if (!in_array(get_class($this->controller), $allowedAdminControllers)) {
                header("Location: " . BASE_URL . "/admin");
                exit;
            }
        }

        // Gọi controller->method(params)
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    /**
     * Tìm method trong controller (case-insensitive)
     * URL '/auth/Login' sẽ match method 'login()'
     * URL '/auth/googleCallback' sẽ match method 'googleCallback()'
     * @param string $methodName Tên method từ URL
     * @return string|null Tên method thực tế hoặc null
     */
    private function resolveMethod($methodName) {
        // Exact match trước (nhanh nhất)
        if (method_exists($this->controller, $methodName)) {
            return $methodName;
        }

        // Case-insensitive fallback
        $reflection = new ReflectionClass($this->controller);
        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if (strtolower($method->getName()) === strtolower($methodName)) {
                return $method->getName();
            }
        }

        return null;
    }

    /**
     * Kiểm tra method có phải public không (dùng Reflection)
     * @param string $methodName
     * @return bool
     */
    private function isPublicMethod($methodName) {
        try {
            $reflection = new ReflectionMethod($this->controller, $methodName);
            return $reflection->isPublic();
        } catch (ReflectionException $e) {
            return false;
        }
    }

    /**
     * Kiểm tra method có bị chặn không
     * @param string $methodName
     * @return bool
     */
    private function isBlockedMethod($methodName) {
        // Chặn __construct, __destruct, v.v.
        if (strpos($methodName, '__') === 0) {
            return true;
        }
        // Chặn methods cụ thể trong blocklist
        return in_array($methodName, self::$blockedMethods);
    }

    /**
     * Phân tích URL thành mảng
     * @return array
     */
    protected function parseUrl() {
        if (isset($_GET['url'])) {
            return explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL));
        }
        return [];
    }
}
