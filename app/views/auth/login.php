<section class="auth-split">
    <div class="auth-story">
        <a href="<?= BASE_URL ?>" class="auth-brand">
            <span class="brand-mark"><i class="fas fa-route"></i></span>
            <strong><?= APP_NAME ?></strong>
        </a>
        <div class="auth-story-content">
            <span class="busuu-label">Welcome back</span>
            <h1>Tiếp tục lộ trình tiếng Anh của bạn.</h1>
            <p>Đăng nhập để xem tiến độ, học tiếp chủ đề đang dang dở và luyện speaking với AI.</p>
            <div class="auth-preview-card">
                <div><i class="fas fa-chart-line"></i><span>Dashboard tiến độ</span></div>
                <div><i class="fas fa-clone"></i><span>Flashcard theo chủ đề</span></div>
                <div><i class="fas fa-microphone-lines"></i><span>Speaking feedback</span></div>
            </div>
        </div>
    </div>

    <div class="auth-panel">
        <div class="auth-card">
            <div class="auth-header">
                <h2>Đăng nhập</h2>
                <p>Chào mừng bạn trở lại EngPath</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?= BASE_URL ?>/auth/login" class="auth-form" id="loginForm">
                <div class="form-group">
                    <label for="username">Tên đăng nhập hoặc Email</label>
                    <input type="text" id="username" name="username"
                           value="<?= htmlspecialchars($old['username'] ?? '') ?>"
                           placeholder="Nhập username hoặc email" required autofocus>
                </div>

                <div class="form-group">
                    <label for="password">Mật khẩu</label>
                    <div class="password-wrapper">
                        <input type="password" id="password" name="password"
                               placeholder="Nhập mật khẩu" required>
                        <button type="button" class="password-toggle" onclick="togglePassword('password')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-block" id="loginBtn">
                    <i class="fas fa-sign-in-alt"></i> Đăng nhập
                </button>
            </form>

            <div class="auth-divider"><span>hoặc</span></div>

            <a href="<?= BASE_URL ?>/auth/google" class="btn-google">
                <svg width="20" height="20" viewBox="0 0 48 48"><path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/><path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/><path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/><path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/></svg>
                Đăng nhập bằng Google
            </a>

            <div class="auth-footer">
                <p>Chưa có tài khoản? <a href="<?= BASE_URL ?>/auth/register">Đăng ký miễn phí</a></p>
            </div>

            <div class="auth-demo">
                <p><strong>Tài khoản demo:</strong></p>
                <small>Admin: admin / admin123</small><br>
                <small>Student: student1 / student123</small>
            </div>
        </div>
    </div>
</section>
