<?php
/**
 * Base Controller
 * Cung cấp các method dùng chung cho tất cả controllers
 */
class Controller {
    
    /**
     * Load model
     * @param string $model Tên model
     * @return object Instance của model
     */
    protected function model($model) {
        $modelFile = APP_PATH . "/models/{$model}.php";
        if (file_exists($modelFile)) {
            require_once $modelFile;
            return new $model();
        }
        error_log("Model not found: {$model}");
        http_response_code(500);
        die('Có lỗi hệ thống. Vui lòng thử lại sau.');
    }

    /**
     * Render view với dữ liệu
     * @param string $view Đường dẫn view (vd: 'auth/login')
     * @param array $data Dữ liệu truyền vào view
     */
    protected function view($view, $data = []) {
        $viewFile = APP_PATH . "/views/{$view}.php";
        if (file_exists($viewFile)) {
            // Extract data thành biến
            extract($data);
            
            // Load layout header
            require APP_PATH . '/views/layouts/header.php';
            
            // Load view chính
            require $viewFile;
            
            // Load layout footer
            require APP_PATH . '/views/layouts/footer.php';
        } else {
            error_log("View not found: {$view}");
            http_response_code(500);
            die('Có lỗi hệ thống. Vui lòng thử lại sau.');
        }
    }

    /**
     * Render view không có layout (cho AJAX, API...)
     * @param string $view
     * @param array $data
     */
    protected function viewPartial($view, $data = []) {
        $viewFile = APP_PATH . "/views/{$view}.php";
        if (file_exists($viewFile)) {
            extract($data);
            require_once $viewFile;
        }
    }

    /**
     * Trả về JSON response (cho AJAX)
     * @param mixed $data Dữ liệu
     * @param int $statusCode HTTP status code
     */
    protected function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Redirect đến URL khác
     * @param string $url
     */
    protected function redirect($url) {
        header("Location: " . BASE_URL . "/{$url}");
        exit;
    }

    /**
     * Set flash message (hiển thị 1 lần)
     * @param string $type success|error|warning|info
     * @param string $message
     */
    protected function setFlash($type, $message) {
        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message
        ];
    }

    /**
     * Kiểm tra request method
     * @param string $method GET|POST|PUT|DELETE
     * @return bool
     */
    protected function isMethod($method) {
        return $_SERVER['REQUEST_METHOD'] === strtoupper($method);
    }

    /**
     * Lấy dữ liệu POST đã trim
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    protected function input($key, $default = null) {
        if (isset($_POST[$key])) {
            return trim($_POST[$key]);
        }
        return $default;
    }

    /**
     * Lấy dữ liệu GET
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    protected function query($key, $default = null) {
        return isset($_GET[$key]) ? trim($_GET[$key]) : $default;
    }
}
