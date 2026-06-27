<section class="auth-split register-split">
    <div class="auth-story">
        <a href="<?= BASE_URL ?>" class="auth-brand">
            <span class="brand-mark"><i class="fas fa-route"></i></span>
            <strong><?= APP_NAME ?></strong>
        </a>
        <div class="auth-story-content">
            <span class="busuu-label">Start for free</span>
            <h1>Tạo tài khoản và bắt đầu học ngay hôm nay.</h1>
            <p>EngPath giúp bạn học theo từng chủ đề, kiểm tra nhanh và theo dõi tiến độ như một ứng dụng học ngoại ngữ thật.</p>
            <div class="auth-preview-card">
                <div><i class="fas fa-book-open"></i><span>Khóa học theo trình độ</span></div>
                <div><i class="fas fa-clipboard-check"></i><span>Quiz và test kỹ năng</span></div>
                <div><i class="fas fa-crown"></i><span>Có thể nâng cấp Pro</span></div>
            </div>
        </div>
    </div>

    <div class="auth-panel">
        <div class="auth-card">
            <div class="auth-header">
                <h2>Đăng ký tài khoản</h2>
                <p>Bắt đầu lộ trình tiếng Anh của bạn</p>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <ul>
                        <?php foreach ($errors as $err): ?>
                            <li><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($err) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?= BASE_URL ?>/auth/register" class="auth-form" id="registerForm">
                <div class="form-group">
                    <label for="full_name">Họ và tên</label>
                    <input type="text" id="full_name" name="full_name"
                           value="<?= htmlspecialchars($old['full_name'] ?? '') ?>"
                           placeholder="Nhập họ tên đầy đủ" required>
                </div>

                <div class="form-group">
                    <label for="username">Tên đăng nhập</label>
                    <input type="text" id="username" name="username"
                           value="<?= htmlspecialchars($old['username'] ?? '') ?>"
                           placeholder="Ít nhất 3 ký tự" required minlength="3">
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email"
                           value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                           placeholder="Nhập địa chỉ email" required>
                </div>

                <div class="form-group">
                    <label for="password">Mật khẩu</label>
                    <div class="password-wrapper">
                        <input type="password" id="password" name="password"
                               placeholder="Ít nhất 6 ký tự" required minlength="6">
                        <button type="button" class="password-toggle" onclick="togglePassword('password')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password_confirm">Xác nhận mật khẩu</label>
                    <div class="password-wrapper">
                        <input type="password" id="password_confirm" name="password_confirm"
                               placeholder="Nhập lại mật khẩu" required>
                        <button type="button" class="password-toggle" onclick="togglePassword('password_confirm')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-block" id="registerBtn">
                    <i class="fas fa-user-plus"></i> Đăng ký miễn phí
                </button>
            </form>

            <div class="auth-footer">
                <p>Đã có tài khoản? <a href="<?= BASE_URL ?>/auth/login">Đăng nhập</a></p>
            </div>
        </div>
    </div>
</section>
