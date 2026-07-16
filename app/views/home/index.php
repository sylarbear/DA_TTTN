<?php
$isLoggedIn = Middleware::isLoggedIn();
$topicCount = count($topics);
$totalVocab = array_sum(array_map(fn($t) => (int)($t['vocab_count'] ?? 0), $topics));
$totalLessons = array_sum(array_map(fn($t) => (int)($t['lesson_count'] ?? 0), $topics));
?>

<!-- Hero -->
<section class="bg-gradient-to-br from-brand-600 via-brand-500 to-blue-400 text-white py-24 lg:py-28 relative overflow-hidden">
    <div class="absolute top-0 right-0 w-[800px] h-[800px] rounded-full bg-white/5 -translate-y-1/2 translate-x-1/4"></div>
    <div class="absolute bottom-0 left-0 w-[600px] h-[600px] rounded-full bg-white/5 translate-y-1/2 -translate-x-1/4"></div>
    <div class="max-w-7xl mx-auto px-6 relative z-10">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
            <div class="max-w-xl">
                <span class="inline-flex items-center gap-2 px-4 py-2 bg-white/15 border border-white/20 rounded-full text-sm text-white/90 mb-8">
                    <i class="fas fa-graduation-cap"></i> Nền tảng học tiếng Anh theo lộ trình
                </span>
                <h1 class="font-heading text-4xl lg:text-5xl xl:text-6xl font-extrabold leading-tight mb-8 text-white">
                    Học tiếng Anh bài bản.<br>Tự tin giao tiếp.
                </h1>
                <p class="text-lg text-white/80 mb-10 leading-relaxed max-w-lg">
                    EngPath đưa bạn đi theo lộ trình rõ ràng từ A1 đến C1 với 15 khóa học, hàng trăm bài học, quiz tương tác và bài thi cuối khóa.
                </p>
                <div class="flex flex-wrap gap-4 mb-12">
                    <?php if (!$isLoggedIn): ?>
                        <a href="<?= BASE_URL ?>/auth/register" class="inline-flex items-center gap-2 px-7 py-4 bg-white text-brand-700 hover:bg-white/90 font-bold rounded-lg text-sm transition shadow-xl shadow-black/10">
                            <i class="fas fa-rocket"></i> Bắt đầu học miễn phí
                        </a>
                        <a href="<?= BASE_URL ?>/course" class="inline-flex items-center gap-2 px-7 py-4 border-2 border-white/50 hover:border-white text-white font-bold rounded-lg text-sm transition">
                            Khám phá khóa học
                        </a>
                    <?php else: ?>
                        <a href="<?= BASE_URL ?>/course" class="inline-flex items-center gap-2 px-7 py-4 bg-white text-brand-700 hover:bg-white/90 font-bold rounded-lg text-sm transition shadow-xl shadow-black/10">
                            <i class="fas fa-play"></i> Tiếp tục học
                        </a>
                        <a href="<?= BASE_URL ?>/dashboard" class="inline-flex items-center gap-2 px-7 py-4 border-2 border-white/50 hover:border-white text-white font-bold rounded-lg text-sm transition">
                            Dashboard
                        </a>
                    <?php endif; ?>
                </div>
                <div class="flex flex-wrap gap-4">
                    <div class="px-6 py-4 bg-white/10 border border-white/15 rounded-xl">
                        <span class="block text-2xl font-extrabold font-heading">15</span>
                        <span class="text-sm text-white/70 font-medium">Khóa học</span>
                    </div>
                    <div class="px-6 py-4 bg-white/10 border border-white/15 rounded-xl">
                        <span class="block text-2xl font-extrabold font-heading">5</span>
                        <span class="text-sm text-white/70 font-medium">Cấp độ CEFR</span>
                    </div>
                    <div class="px-6 py-4 bg-white/10 border border-white/15 rounded-xl">
                        <span class="block text-2xl font-extrabold font-heading"><?= $totalLessons ?: 50 ?>+</span>
                        <span class="text-sm text-white/70 font-medium">Bài học</span>
                    </div>
                    <div class="px-6 py-4 bg-white/10 border border-white/15 rounded-xl">
                        <span class="block text-2xl font-extrabold font-heading"><?= $totalVocab ?: 200 ?>+</span>
                        <span class="text-sm text-white/70 font-medium">Từ vựng</span>
                    </div>
                </div>
            </div>
            <div class="hidden lg:flex justify-center">
                <svg viewBox="0 0 400 300" class="w-full max-w-[420px]" aria-label="Hành trình học tập từ A1 đến C1">
                    <defs><linearGradient id="bgGrad" x1="0" y1="0" x2="1" y2="1"><stop offset="0%" stop-color="rgba(255,255,255,0.15)"/><stop offset="100%" stop-color="rgba(255,255,255,0.05)"/></linearGradient></defs>
                    <circle cx="200" cy="140" r="120" fill="url(#bgGrad)" stroke="rgba(255,255,255,0.2)" stroke-width="2"/>
                    <circle cx="200" cy="140" r="90" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="1" stroke-dasharray="8 6"/>
                    <rect x="160" y="85" width="80" height="55" rx="4" fill="rgba(255,255,255,0.2)" stroke="#fff" stroke-width="2"/>
                    <line x1="170" y1="97" x2="230" y2="97" stroke="#fff" stroke-width="1.5" opacity="0.7"/>
                    <line x1="170" y1="107" x2="215" y2="107" stroke="#fbbf24" stroke-width="1.5" opacity="0.9"/>
                    <line x1="170" y1="117" x2="225" y2="117" stroke="#fff" stroke-width="1.5" opacity="0.5"/>
                    <line x1="170" y1="127" x2="200" y2="127" stroke="#fff" stroke-width="1.5" opacity="0.5"/>
                    <polygon points="175,75 200,65 225,75 200,80" fill="rgba(255,255,255,0.3)" stroke="#fff" stroke-width="1.5"/>
                    <line x1="200" y1="80" x2="200" y2="85" stroke="#fff" stroke-width="1.5"/>
                    <circle cx="130" cy="180" r="6" fill="#fbbf24"/>
                    <circle cx="155" cy="195" r="6" fill="#fff" opacity="0.6"/><circle cx="180" cy="205" r="6" fill="#fff" opacity="0.4"/>
                    <circle cx="220" cy="205" r="6" fill="#fff" opacity="0.4"/><circle cx="245" cy="195" r="6" fill="#fff" opacity="0.3"/>
                    <circle cx="270" cy="180" r="6" fill="#fff" opacity="0.2"/>
                    <polyline points="130,180 155,195 180,205 220,205 245,195 270,180" fill="none" stroke="#fff" stroke-width="1.5" opacity="0.3"/>
                    <polygon points="200,155 203,163 211,163 205,168 207,176 200,172 193,176 195,168 189,163 197,163" fill="#fbbf24" opacity="0.9"/>
                </svg>
            </div>
        </div>
    </div>
</section>

<!-- How It Works -->
<section class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-16">
            <span class="inline-flex items-center gap-2 text-brand-500 font-extrabold text-xs uppercase tracking-wider mb-4">Cách hoạt động</span>
            <h2 class="font-heading text-3xl lg:text-4xl font-bold mb-4 text-slate-700">3 bước để bắt đầu học</h2>
            <p class="text-slate-500 text-base max-w-xl mx-auto leading-relaxed">Hành trình học tiếng Anh của bạn chỉ với 3 bước đơn giản</p>
        </div>
        <div class="grid md:grid-cols-3 gap-10 max-w-4xl mx-auto">
            <div class="relative text-center pt-10 pb-10 px-8 bg-white border border-gray-100 rounded-2xl hover:-translate-y-1 hover:shadow-lg transition-all duration-200">
                <div class="absolute -top-6 left-1/2 -translate-x-1/2 w-12 h-12 rounded-full bg-brand-500 text-white font-extrabold text-base flex items-center justify-center shadow-lg shadow-brand-500/25">1</div>
                <div class="w-16 h-16 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center text-2xl mx-auto mb-6"><i class="fas fa-clipboard-check"></i></div>
                <h3 class="font-heading text-xl font-bold mb-3 text-slate-700">Kiểm tra đầu vào</h3>
                <p class="text-slate-500 text-sm leading-relaxed">Làm bài test 5 phút để xác định trình độ CEFR hiện tại của bạn.</p>
            </div>
            <div class="relative text-center pt-10 pb-10 px-8 bg-white border border-gray-100 rounded-2xl hover:-translate-y-1 hover:shadow-lg transition-all duration-200">
                <div class="absolute -top-6 left-1/2 -translate-x-1/2 w-12 h-12 rounded-full bg-brand-500 text-white font-extrabold text-base flex items-center justify-center shadow-lg shadow-brand-500/25">2</div>
                <div class="w-16 h-16 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center text-2xl mx-auto mb-6"><i class="fas fa-book-open"></i></div>
                <h3 class="font-heading text-xl font-bold mb-3 text-slate-700">Học theo lộ trình</h3>
                <p class="text-slate-500 text-sm leading-relaxed">Theo học các khóa học được cá nhân hóa theo đúng trình độ của bạn.</p>
            </div>
            <div class="relative text-center pt-10 pb-10 px-8 bg-white border border-gray-100 rounded-2xl hover:-translate-y-1 hover:shadow-lg transition-all duration-200">
                <div class="absolute -top-6 left-1/2 -translate-x-1/2 w-12 h-12 rounded-full bg-brand-500 text-white font-extrabold text-base flex items-center justify-center shadow-lg shadow-brand-500/25">3</div>
                <div class="w-16 h-16 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center text-2xl mx-auto mb-6"><i class="fas fa-trophy"></i></div>
                <h3 class="font-heading text-xl font-bold mb-3 text-slate-700">Nhận chứng chỉ</h3>
                <p class="text-slate-500 text-sm leading-relaxed">Hoàn thành bài thi cuối khóa và nhận chứng chỉ cho từng cấp độ.</p>
            </div>
        </div>
    </div>
</section>

<!-- CEFR Levels -->
<section class="py-24 bg-slate-50/50">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-16">
            <span class="inline-flex items-center gap-2 text-brand-500 font-extrabold text-xs uppercase tracking-wider mb-4">Lộ trình học</span>
            <h2 class="font-heading text-3xl lg:text-4xl font-bold mb-4 text-slate-700">5 cấp độ — từ cơ bản đến thành thạo</h2>
            <p class="text-slate-500 text-base max-w-xl mx-auto leading-relaxed">Mỗi cấp độ có 3 khóa học, được thiết kế theo khung tham chiếu Châu Âu (CEFR)</p>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-5 gap-5 text-center max-w-4xl mx-auto">
            <?php
            $cefrLabels = [
                'A1' => ['name' => 'Sơ cấp', 'desc' => 'Làm quen tiếng Anh', 'color' => 'border-emerald-500', 'text' => 'text-emerald-500', 'bg' => 'bg-emerald-50'],
                'A2' => ['name' => 'Cơ bản', 'desc' => 'Giao tiếp đơn giản', 'color' => 'border-sky-500', 'text' => 'text-sky-500', 'bg' => 'bg-sky-50'],
                'B1' => ['name' => 'Trung cấp', 'desc' => 'Tự tin hội thoại', 'color' => 'border-amber-500', 'text' => 'text-amber-500', 'bg' => 'bg-amber-50'],
                'B2' => ['name' => 'Trên trung cấp', 'desc' => 'Giao tiếp lưu loát', 'color' => 'border-brand-500', 'text' => 'text-brand-500', 'bg' => 'bg-brand-50'],
                'C1' => ['name' => 'Nâng cao', 'desc' => 'Thành thạo học thuật', 'color' => 'border-rose-500', 'text' => 'text-rose-500', 'bg' => 'bg-rose-50'],
            ];
            foreach ($cefrLabels as $level => $info): ?>
            <a href="<?= BASE_URL ?>/course" class="block bg-white rounded-2xl p-7 border-t-4 <?= $info['color'] ?> shadow-sm hover:-translate-y-1 hover:shadow-md transition-all duration-200">
                <div class="font-heading text-4xl font-extrabold <?= $info['text'] ?> mb-2"><?= $level ?></div>
                <div class="font-bold text-slate-700 mb-1"><?= $info['name'] ?></div>
                <div class="text-sm text-slate-500"><?= $info['desc'] ?></div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Features -->
<section class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-16">
            <span class="inline-flex items-center gap-2 text-brand-500 font-extrabold text-xs uppercase tracking-wider mb-4">Tại sao chọn EngPath?</span>
            <h2 class="font-heading text-3xl lg:text-4xl font-bold text-slate-700">Học thông minh hơn, không vất vả hơn</h2>
        </div>
        <div class="grid md:grid-cols-3 gap-10 max-w-5xl mx-auto">
            <div class="p-10 bg-white border border-gray-100 rounded-2xl hover:-translate-y-1 hover:shadow-lg hover:border-brand-200 transition-all duration-200">
                <div class="w-14 h-14 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center text-2xl mb-6"><i class="fas fa-road"></i></div>
                <h3 class="font-heading text-xl font-bold mb-3 text-slate-700">Lộ trình rõ ràng</h3>
                <p class="text-slate-500 leading-relaxed">15 khóa học từ A1 đến C1. Mỗi khóa có chương, bài học, quiz và bài thi cuối khóa — bạn luôn biết mình đang ở đâu.</p>
            </div>
            <div class="p-10 bg-white border border-gray-100 rounded-2xl hover:-translate-y-1 hover:shadow-lg hover:border-brand-200 transition-all duration-200">
                <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl mb-6"><i class="fas fa-microphone"></i></div>
                <h3 class="font-heading text-xl font-bold mb-3 text-slate-700">Luyện nói với AI</h3>
                <p class="text-slate-500 leading-relaxed">Công nghệ nhận diện giọng nói AI giúp bạn luyện phát âm chuẩn, nhận phản hồi tức thì về độ chính xác.</p>
            </div>
            <div class="p-10 bg-white border border-gray-100 rounded-2xl hover:-translate-y-1 hover:shadow-lg hover:border-brand-200 transition-all duration-200">
                <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center text-2xl mb-6"><i class="fas fa-chart-line"></i></div>
                <h3 class="font-heading text-xl font-bold mb-3 text-slate-700">Theo dõi tiến độ</h3>
                <p class="text-slate-500 leading-relaxed">Dashboard trực quan, streak hàng ngày, XP & level — giúp bạn duy trì động lực học tập mỗi ngày.</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="py-24 bg-gradient-to-br from-brand-500 to-blue-500 text-white text-center">
    <div class="max-w-2xl mx-auto px-6">
        <h2 class="font-heading text-3xl lg:text-4xl font-bold mb-5">Sẵn sàng bắt đầu?</h2>
        <p class="text-white/75 text-lg mb-10 leading-relaxed">Tham gia cùng hàng nghìn học viên đang học tiếng Anh theo lộ trình trên EngPath.</p>
        <?php if (!$isLoggedIn): ?>
            <a href="<?= BASE_URL ?>/auth/register" class="inline-flex items-center gap-2 px-8 py-4 bg-white text-brand-600 hover:bg-white/95 font-bold rounded-lg text-base transition shadow-xl">
                <i class="fas fa-rocket"></i> Bắt đầu học miễn phí
            </a>
        <?php else: ?>
            <a href="<?= BASE_URL ?>/course" class="inline-flex items-center gap-2 px-8 py-4 bg-white text-brand-600 hover:bg-white/95 font-bold rounded-lg text-base transition shadow-xl">
                <i class="fas fa-play"></i> Tiếp tục học ngay
            </a>
        <?php endif; ?>
    </div>
</section>
