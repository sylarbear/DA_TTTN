<section class="min-h-[calc(100vh-64px)] grid lg:grid-cols-[1fr_430px] bg-white">
    <!-- Left: Brand Story -->
    <div class="relative hidden lg:flex flex-col justify-center overflow-hidden px-16 bg-gradient-to-br from-brand-50 via-white to-blue-50">
        <div class="absolute -right-36 -bottom-40 w-[430px] h-[430px] rounded-full bg-amber-200/60"></div>
        <a href="<?= BASE_URL ?>" class="absolute top-8 left-16 flex items-center gap-3 text-slate-800 font-heading text-xl font-extrabold z-10">
            <span class="w-9 h-9 rounded-lg bg-brand-600 text-white inline-grid place-items-center"><i class="fas fa-route text-sm"></i></span>
            <?= APP_NAME ?>
        </a>
        <div class="relative z-10 max-w-xl">
            <span class="text-brand-600 font-extrabold text-xs uppercase tracking-wider">Welcome back</span>
            <h1 class="font-heading text-5xl lg:text-6xl font-extrabold text-slate-800 leading-tight mt-2 mb-6">Tiếp tục lộ trình tiếng Anh của bạn.</h1>
            <p class="text-slate-500 text-lg leading-relaxed mb-10">Đăng nhập để xem tiến độ, học tiếp chủ đề đang dang dở và luyện speaking với AI.</p>
            <div class="flex flex-col gap-3">
                <div class="flex items-center gap-3 text-slate-600"><i class="fas fa-chart-line w-5 text-brand-500"></i> Dashboard tiến độ</div>
                <div class="flex items-center gap-3 text-slate-600"><i class="fas fa-clone w-5 text-brand-500"></i> Flashcard theo chủ đề</div>
                <div class="flex items-center gap-3 text-slate-600"><i class="fas fa-microphone w-5 text-brand-500"></i> Speaking feedback</div>
            </div>
        </div>
    </div>

    <!-- Right: Login Form -->
    <div class="flex items-center justify-center px-6 py-12">
        <div class="w-full max-w-[400px]">
            <div class="text-center mb-10">
                <div class="lg:hidden text-brand-600 text-4xl mb-4"><i class="fas fa-route"></i></div>
                <h2 class="font-heading text-2xl font-bold text-slate-800 mb-1">Đăng nhập</h2>
                <p class="text-slate-500 text-sm">Chào mừng bạn trở lại EngPath</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="flex items-center gap-2 p-4 bg-red-50 border border-red-200 rounded-lg text-red-600 text-sm mb-6">
                    <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?= BASE_URL ?>/auth/login" class="flex flex-col gap-5">
                <div class="flex flex-col gap-1.5">
                    <label for="username" class="text-sm font-semibold text-slate-600">Tên đăng nhập hoặc Email</label>
                    <input type="text" id="username" name="username"
                           value="<?= htmlspecialchars($old['username'] ?? '') ?>"
                           placeholder="Nhập username hoặc email" required autofocus
                           class="px-4 py-3 bg-slate-50 border border-slate-200 rounded-lg focus:border-brand-500 focus:bg-white focus:ring-2 focus:ring-brand-500/10 outline-none transition text-slate-800 text-sm">
                </div>

                <div class="flex flex-col gap-1.5">
                    <label for="password" class="text-sm font-semibold text-slate-600">Mật khẩu</label>
                    <div class="relative">
                        <input type="password" id="password" name="password" placeholder="Nhập mật khẩu" required
                               class="w-full px-4 py-3 pr-12 bg-slate-50 border border-slate-200 rounded-lg focus:border-brand-500 focus:bg-white focus:ring-2 focus:ring-brand-500/10 outline-none transition text-slate-800 text-sm">
                        <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600" onclick="togglePassword('password')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="w-full py-3 bg-slate-800 hover:bg-slate-900 text-white font-bold rounded-lg text-sm transition" id="loginBtn">
                    <i class="fas fa-sign-in-alt mr-2"></i> Đăng nhập
                </button>
            </form>

            <div class="flex items-center gap-4 my-7">
                <span class="flex-1 h-px bg-gray-200"></span>
                <span class="text-sm text-slate-400 font-medium">hoặc</span>
                <span class="flex-1 h-px bg-gray-200"></span>
            </div>

            <a href="<?= BASE_URL ?>/auth/google" class="w-full flex items-center justify-center gap-3 py-3 bg-white border border-slate-200 rounded-lg hover:border-blue-400 hover:bg-blue-50/50 hover:-translate-y-0.5 transition text-slate-700 text-sm font-medium">
                <svg width="20" height="20" viewBox="0 0 48 48"><path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/><path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/><path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/><path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/></svg>
                Đăng nhập bằng Google
            </a>

            <div class="text-center mt-8 pt-6 border-t border-gray-100">
                <p class="text-sm text-slate-500">Chưa có tài khoản? <a href="<?= BASE_URL ?>/auth/register" class="font-bold text-brand-600 hover:text-brand-700">Đăng ký miễn phí</a></p>
            </div>

            <div class="text-center mt-5 p-3 bg-brand-50/50 rounded-lg text-xs text-slate-400">
                <p class="font-semibold text-slate-500 mb-1">Tài khoản demo:</p>
                Admin: admin / admin123 &nbsp;|&nbsp; Student: student1 / student123
            </div>
        </div>
    </div>
</section>
