<?php


/**
 * AuthController
 * Đăng ký, đăng nhập, đăng xuất
 */
class AuthController extends Controller
{
    /**
     * Hiển thị form đăng nhập
     */
    public function login()
    {
        Middleware::guest();

        if ($this->isMethod('POST')) {
            $this->handleLogin();

            return;
        }

        $this->view('auth/login', [
            'title' => 'Đăng nhập - ' . APP_NAME,
        ]);
    }

    /**
     * Xử lý đăng nhập
     */
    private function handleLogin()
    {
        // Rate limit: 5 attempts per 60s
        if (RateLimiter::tooMany('login', 5, 60)) {
            $this->view('auth/login', [
                'title' => 'Đăng nhập - ' . APP_NAME,
                'error' => 'Quá nhiều lần thử. Vui lòng đợi ' . RateLimiter::resetIn('login') . ' giây.',
                'old' => ['username' => $this->input('username')],
            ]);

            return;
        }

        $username = $this->input('username');
        $password = $_POST['password'] ?? '';

        // Validate
        if (empty($username) || empty($password)) {
            $this->view('auth/login', [
                'title' => 'Đăng nhập - ' . APP_NAME,
                'error' => 'Vui lòng nhập đầy đủ thông tin.',
                'old' => ['username' => $username],
            ]);

            return;
        }

        $userModel = $this->model('User');
        $user = $userModel->authenticate($username, $password);

        if ($user) {
            // Regenerate session ID to prevent session fixation
            session_regenerate_id(true);
            // Lưu session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['avatar'] = $user['avatar'];
            $_SESSION['membership'] = $user['membership'] ?? 'free';
            $_SESSION['membership_expired_at'] = $user['membership_expired_at'] ?? null;

            // Auto-downgrade expired Pro in DB
            if (($_SESSION['membership'] === 'pro') && !empty($_SESSION['membership_expired_at'])
                && strtotime($_SESSION['membership_expired_at']) < time()) {
                $db = getDB();
                $db->prepare("UPDATE users SET membership = 'free' WHERE id = :id")
                   ->execute(['id' => $user['id']]);
                $_SESSION['membership'] = 'free';
            }

            $this->setFlash('success', 'Đăng nhập thành công! Chào mừng ' . $user['full_name']);

            if ($user['role'] === 'admin') {
                return $this->redirect('admin');
            }

            // Kiểm tra nếu user chưa có placement → redirect sang placement intro
            if (empty($user['placement_level'])) {
                return $this->redirect('placement/intro');
            }

            return $this->redirect('');
        } else {
            $this->view('auth/login', [
                'title' => 'Đăng nhập - ' . APP_NAME,
                'error' => 'Tên đăng nhập hoặc mật khẩu không đúng.',
                'old' => ['username' => $username],
            ]);
        }
    }

    /**
     * Hiển thị form đăng ký
     */
    public function register()
    {
        Middleware::guest();

        if ($this->isMethod('POST')) {
            $this->handleRegister();

            return;
        }

        $this->view('auth/register', [
            'title' => 'Đăng ký - ' . APP_NAME,
        ]);
    }

    /**
     * Xử lý đăng ký
     */
    private function handleRegister()
    {
        $data = [
            'username' => $this->input('username'),
            'email' => $this->input('email'),
            'full_name' => $this->input('full_name'),
            'password' => $_POST['password'] ?? '',
            'password_confirm' => $_POST['password_confirm'] ?? '',
        ];

        $errors = $this->validateRegister($data);

        if (!empty($errors)) {
            $this->view('auth/register', [
                'title' => 'Đăng ký - ' . APP_NAME,
                'errors' => $errors,
                'old' => $data,
            ]);

            return;
        }

        $userModel = $this->model('User');

        // Kiểm tra trùng username/email
        if ($userModel->usernameExists($data['username'])) {
            $errors[] = 'Tên đăng nhập đã tồn tại.';
        }
        if ($userModel->emailExists($data['email'])) {
            $errors[] = 'Email đã được sử dụng.';
        }

        if (!empty($errors)) {
            $this->view('auth/register', [
                'title' => 'Đăng ký - ' . APP_NAME,
                'errors' => $errors,
                'old' => $data,
            ]);

            return;
        }

        // Tạo tài khoản
        $userId = $userModel->register($data);

        if ($userId) {
            $this->setFlash('success', 'Đăng ký thành công! Vui lòng đăng nhập.');

            return $this->redirect('auth/login');
        } else {
            $this->view('auth/register', [
                'title' => 'Đăng ký - ' . APP_NAME,
                'errors' => ['Có lỗi xảy ra, vui lòng thử lại.'],
                'old' => $data,
            ]);
        }
    }

    /**
     * Validate dữ liệu đăng ký
     * @param  array $data
     * @return array Mảng lỗi
     */
    private function validateRegister($data)
    {
        $errors = [];

        if (empty($data['username']) || strlen($data['username']) < 3) {
            $errors[] = 'Tên đăng nhập phải có ít nhất 3 ký tự.';
        }
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $data['username'])) {
            $errors[] = 'Tên đăng nhập chỉ chứa chữ cái, số và dấu gạch dưới.';
        }
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email không hợp lệ.';
        }
        if (empty($data['full_name'])) {
            $errors[] = 'Vui lòng nhập họ tên.';
        }
        if (strlen($data['password']) < 6) {
            $errors[] = 'Mật khẩu phải có ít nhất 6 ký tự.';
        }
        if ($data['password'] !== $data['password_confirm']) {
            $errors[] = 'Mật khẩu xác nhận không khớp.';
        }

        return $errors;
    }

    /**
     * Đăng xuất
     */
    public function logout()
    {
        session_destroy();
        header('Location: ' . BASE_URL . '/auth/login');
        exit;
    }

    /**
     * Redirect đến Google OAuth
     */
    public function google()
    {
        if (GOOGLE_CLIENT_ID === '') {
            $this->setFlash('error', 'Google Login chưa được cấu hình. Vui lòng liên hệ admin.');

            return $this->redirect('auth/login');
        }
        require_once APP_PATH . '/core/GoogleOAuth.php';
        $authUrl = GoogleOAuth::getAuthUrl();
        header("Location: $authUrl");
        exit;
    }

    /**
     * Callback sau khi Google xác thực
     */
    public function googleCallback()
    {
        require_once APP_PATH . '/core/GoogleOAuth.php';

        // Kiểm tra lỗi từ Google
        if (isset($_GET['error'])) {
            $this->setFlash('error', 'Đăng nhập Google bị từ chối.');

            return $this->redirect('auth/login');
        }

        $code = $_GET['code'] ?? '';
        $state = $_GET['state'] ?? '';

        // Xác thực CSRF state
        if (!GoogleOAuth::verifyState($state)) {
            $this->setFlash('error', 'Phiên đăng nhập không hợp lệ. Vui lòng thử lại.');

            return $this->redirect('auth/login');
        }

        // Đổi code lấy access token
        $tokenData = GoogleOAuth::getAccessToken($code);
        if (!$tokenData) {
            $this->setFlash('error', 'Không thể kết nối với Google. Vui lòng thử lại.');

            return $this->redirect('auth/login');
        }

        // Lấy thông tin user từ Google
        $googleUser = GoogleOAuth::getUserInfo($tokenData['access_token']);
        if (!$googleUser || empty($googleUser['email'])) {
            $this->setFlash('error', 'Không thể lấy thông tin từ Google. Vui lòng thử lại.');

            return $this->redirect('auth/login');
        }

        // Tìm hoặc tạo user
        $userModel = $this->model('User');
        $user = $userModel->findOrCreateByGoogle($googleUser);

        if (!$user) {
            $this->setFlash('error', 'Có lỗi xảy ra khi tạo tài khoản. Vui lòng thử lại.');

            return $this->redirect('auth/login');
        }

        // Đăng nhập
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['avatar'] = $user['avatar'];
        $_SESSION['membership'] = $user['membership'] ?? 'free';
        $_SESSION['membership_expired_at'] = $user['membership_expired_at'] ?? null;

        // Auto-downgrade expired Pro in DB (same as regular login)
        if (($_SESSION['membership'] === 'pro') && !empty($_SESSION['membership_expired_at'])
            && strtotime($_SESSION['membership_expired_at']) < time()) {
            $db = getDB();
            $db->prepare("UPDATE users SET membership = 'free' WHERE id = :id")
               ->execute(['id' => $user['id']]);
            $_SESSION['membership'] = 'free';
        }

        $this->setFlash('success', 'Đăng nhập Google thành công! Chào mừng ' . $user['full_name']);

        if ($user['role'] === 'admin') {
            return $this->redirect('admin');
        }

        // Kiểm tra nếu user chưa có placement → redirect sang placement intro
        if (empty($user['placement_level'])) {
            return $this->redirect('placement/intro');
        }

        return $this->redirect('');
    }
}
