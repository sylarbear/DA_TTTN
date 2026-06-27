<?php
/**
 * ChatbotController
 * AI Chatbot trợ lý học tiếng Anh
 */
class ChatbotController extends Controller {

    public function __construct() {
        Middleware::requireLogin();
    }

    /**
     * Chatbot không có trang riêng (là widget nổi)
     * Redirect về trang chủ nếu user truy cập trực tiếp
     */
    public function index() {
        $this->redirect('');
    }

    /**
     * API chat (AJAX)
     */
    public function send() {
        if (!$this->isMethod('POST')) {
            return $this->json(['error' => 'Method not allowed'], 405);
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $message = trim($input['message'] ?? '');
        $history = $input['history'] ?? [];

        if (empty($message)) {
            return $this->json(['error' => 'Vui lòng nhập tin nhắn'], 400);
        }

        // Load OpenAI service
        require_once APP_PATH . '/core/OpenAIService.php';

        if (!OpenAIService::isAvailable()) {
            return $this->json(['error' => 'AI Chatbot chưa được cấu hình. Admin cần nhập OpenAI API key trong Admin Panel.'], 503);
        }

        $response = OpenAIService::chatbot($message, $history);

        if ($response === null) {
            return $this->json(['error' => 'Không thể kết nối AI. Vui lòng thử lại sau.'], 500);
        }

        return $this->json([
            'success' => true,
            'message' => $response
        ]);
    }

    /**
     * Kiểm tra chatbot có sẵn không (AJAX)
     */
    public function status() {
        require_once APP_PATH . '/core/OpenAIService.php';
        return $this->json([
            'available' => OpenAIService::isAvailable(),
            'logged_in' => Middleware::isLoggedIn()
        ]);
    }
}
