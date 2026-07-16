<section class="min-h-[calc(100vh-64px)] grid lg:grid-cols-[1fr_430px] bg-white">
    <!-- Left: Brand Story -->
    <div class="relative hidden lg:flex flex-col justify-center overflow-hidden px-16 bg-gradient-to-br from-brand-50 via-white to-blue-50">
        <div class="absolute -right-36 -bottom-40 w-[430px] h-[430px] rounded-full bg-amber-200/60"></div>
        <a href="<?= BASE_URL ?>" class="absolute top-8 left-16 flex items-center gap-3 text-slate-800 font-heading text-xl font-extrabold z-10">
            <span class="w-9 h-9 rounded-lg bg-brand-600 text-white inline-grid place-items-center"><i class="fas fa-route text-sm"></i></span>
            <?= APP_NAME ?>
        </a>
        <div class="relative z-10 max-w-xl">
            <span class="text-brand-600 font-extrabold text-xs uppercase tracking-wider">Start for free</span>
            <h1 class="font-heading text-5xl lg:text-6xl font-extrabold text-slate-800 leading-tight mt-2 mb-6">Tạo tài khoản và bắt đầu học ngay hôm nay.</h1>
            <p class="text-slate-500 text-lg leading-relaxed mb-10">EngPath giúp bạn học theo từng chủ đề, kiểm tra nhanh và theo dõi tiến độ như một ứng dụng học ngoại ngữ thật.</p>
            <div class="flex flex-col gap-3">
                <div class="flex items-center gap-3 text-slate-600"><i class="fas fa-book-open w-5 text-brand-500"></i> Khóa học theo trình độ</div>
                <div class="flex items-center gap-3 text-slate-600"><i class="fas fa-clipboard-check w-5 text-brand-500"></i> Quiz và test kỹ năng</div>
                <div class="flex items-center gap-3 text-slate-600"><i class="fas fa-crown w-5 text-brand-500"></i> Có thể nâng cấp Pro</div>
            </div>
        </div>
    </div>

    <!-- Right: Register Form -->
    <div class="flex items-center justify-center px-6 py-12">
        <div class="w-full max-w-[400px]">
            <div class="text-center mb-10">
                <div class="lg:hidden text-brand-600 text-4xl mb-4"><i class="fas fa-route"></i></div>
                <h2 class="font-heading text-2xl font-bold text-slate-800 mb-1">Đăng ký tài khoản</h2>
                <p class="text-slate-500 text-sm">Bắt đầu lộ trình tiếng Anh của bạn</p>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="p-4 bg-red-50 border border-red-200 rounded-lg text-red-600 text-sm mb-6">
                    <ul class="list-none">
                        <?php foreach ($errors as $err): ?>
                            <li class="flex items-center gap-2"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($err) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?= BASE_URL ?>/auth/register" class="flex flex-col gap-4" id="registerForm">
                <div class="flex flex-col gap-1.5">
                    <label for="full_name" class="text-sm font-semibold text-slate-600">Họ và tên</label>
                    <input type="text" id="full_name" name="full_name"
                           value="<?= htmlspecialchars($old['full_name'] ?? '') ?>"
                           placeholder="Nhập họ tên đầy đủ" required
                           class="px-4 py-3 bg-slate-50 border border-slate-200 rounded-lg focus:border-brand-500 focus:bg-white focus:ring-2 focus:ring-brand-500/10 outline-none transition text-slate-800 text-sm">
                </div>
                <div class="flex flex-col gap-1.5">
                    <label for="username" class="text-sm font-semibold text-slate-600">Tên đăng nhập</label>
                    <input type="text" id="username" name="username"
                           value="<?= htmlspecialchars($old['username'] ?? '') ?>"
                           placeholder="Ít nhất 3 ký tự" required minlength="3"
                           class="px-4 py-3 bg-slate-50 border border-slate-200 rounded-lg focus:border-brand-500 focus:bg-white focus:ring-2 focus:ring-brand-500/10 outline-none transition text-slate-800 text-sm">
                </div>
                <div class="flex flex-col gap-1.5">
                    <label for="email" class="text-sm font-semibold text-slate-600">Email</label>
                    <input type="email" id="email" name="email"
                           value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                           placeholder="Nhập địa chỉ email" required
                           class="px-4 py-3 bg-slate-50 border border-slate-200 rounded-lg focus:border-brand-500 focus:bg-white focus:ring-2 focus:ring-brand-500/10 outline-none transition text-slate-800 text-sm">
                </div>
                <div class="flex flex-col gap-1.5">
                    <label for="password" class="text-sm font-semibold text-slate-600">Mật khẩu</label>
                    <div class="relative">
                        <input type="password" id="password" name="password" placeholder="Ít nhất 6 ký tự" required minlength="6"
                               class="w-full px-4 py-3 pr-12 bg-slate-50 border border-slate-200 rounded-lg focus:border-brand-500 focus:bg-white focus:ring-2 focus:ring-brand-500/10 outline-none transition text-slate-800 text-sm">
                        <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600" onclick="togglePassword('password')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="flex flex-col gap-1.5">
                    <label for="password_confirm" class="text-sm font-semibold text-slate-600">Xác nhận mật khẩu</label>
                    <div class="relative">
                        <input type="password" id="password_confirm" name="password_confirm" placeholder="Nhập lại mật khẩu" required
                               class="w-full px-4 py-3 pr-12 bg-slate-50 border border-slate-200 rounded-lg focus:border-brand-500 focus:bg-white focus:ring-2 focus:ring-brand-500/10 outline-none transition text-slate-800 text-sm">
                        <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600" onclick="togglePassword('password_confirm')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="w-full py-3 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-lg text-sm transition mt-2" id="registerBtn">
                    <i class="fas fa-user-plus mr-2"></i> Đăng ký miễn phí
                </button>
            </form>

            <div class="text-center mt-8 pt-6 border-t border-gray-100">
                <p class="text-sm text-slate-500">Đã có tài khoản? <a href="<?= BASE_URL ?>/auth/login" class="font-bold text-brand-600 hover:text-brand-700">Đăng nhập</a></p>
            </div>
        </div>
    </div>
</section>
