<?php
$isLoggedIn = Middleware::isLoggedIn();
$topicCount = count($topics);
$totalVocab = array_sum(array_map(fn($t) => (int)($t['vocab_count'] ?? 0), $topics));
$totalLessons = array_sum(array_map(fn($t) => (int)($t['lesson_count'] ?? 0), $topics));
?>

<div class="landing-page">

<!-- Hero -->
<section class="hero">
    <div class="container hero-grid">
        <div class="hero-content">
            <span class="hero-badge"><i class="fas fa-graduation-cap"></i> Nền tảng học tiếng Anh theo lộ trình</span>
            <h1>Học tiếng Anh bài bản.<br>Tự tin giao tiếp.</h1>
            <p class="hero-description">EngPath đưa bạn đi theo lộ trình rõ ràng từ A1 đến C1 với 15 khóa học, hàng trăm bài học, quiz tương tác và bài thi cuối khóa.</p>
            <div class="hero-actions">
                <?php if (!$isLoggedIn): ?>
                    <a href="<?= BASE_URL ?>/auth/register" class="btn btn-cta btn-lg"><i class="fas fa-rocket"></i> Bắt đầu học miễn phí</a>
                    <a href="<?= BASE_URL ?>/course" class="btn btn-outline btn-lg">Khám phá khóa học</a>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>/course" class="btn btn-cta btn-lg"><i class="fas fa-play"></i> Tiếp tục học</a>
                    <a href="<?= BASE_URL ?>/dashboard" class="btn btn-outline btn-lg">Dashboard</a>
                <?php endif; ?>
            </div>
            <div class="hero-stats">
                <div class="stat-item"><span class="stat-number">15</span><span class="stat-label">Khóa học</span></div>
                <div class="stat-item"><span class="stat-number">5</span><span class="stat-label">Cấp độ CEFR</span></div>
                <div class="stat-item"><span class="stat-number"><?= $totalLessons ?: 50 ?>+</span><span class="stat-label">Bài học</span></div>
                <div class="stat-item"><span class="stat-number"><?= $totalVocab ?: 200 ?>+</span><span class="stat-label">Từ vựng</span></div>
            </div>
        </div>
        <div class="hero-showcase">
            <svg viewBox="0 0 400 300" xmlns="http://www.w3.org/2000/svg" style="width:100%; max-width:420px;" aria-label="Hành trình học tập từ A1 đến C1">
                <defs>
                    <linearGradient id="bgGrad" x1="0" y1="0" x2="1" y2="1">
                        <stop offset="0%" stop-color="rgba(255,255,255,0.15)"/>
                        <stop offset="100%" stop-color="rgba(255,255,255,0.05)"/>
                    </linearGradient>
                </defs>
                <circle cx="200" cy="140" r="120" fill="url(#bgGrad)" stroke="rgba(255,255,255,0.2)" stroke-width="2"/>
                <circle cx="200" cy="140" r="90" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="1" stroke-dasharray="8 6"/>
                <rect x="160" y="85" width="80" height="55" rx="4" fill="rgba(255,255,255,0.2)" stroke="#fff" stroke-width="2"/>
                <line x1="170" y1="97" x2="230" y2="97" stroke="#fff" stroke-width="1.5" opacity="0.7"/>
                <line x1="170" y1="107" x2="215" y2="107" stroke="var(--cta)" stroke-width="1.5" opacity="0.9"/>
                <line x1="170" y1="117" x2="225" y2="117" stroke="#fff" stroke-width="1.5" opacity="0.5"/>
                <line x1="170" y1="127" x2="200" y2="127" stroke="#fff" stroke-width="1.5" opacity="0.5"/>
                <polygon points="175,75 200,65 225,75 200,80" fill="rgba(255,255,255,0.3)" stroke="#fff" stroke-width="1.5"/>
                <line x1="200" y1="80" x2="200" y2="85" stroke="#fff" stroke-width="1.5"/>
                <circle cx="130" cy="180" r="6" fill="var(--cta)"/>
                <circle cx="155" cy="195" r="6" fill="#fff" opacity="0.6"/>
                <circle cx="180" cy="205" r="6" fill="#fff" opacity="0.4"/>
                <circle cx="220" cy="205" r="6" fill="#fff" opacity="0.4"/>
                <circle cx="245" cy="195" r="6" fill="#fff" opacity="0.3"/>
                <circle cx="270" cy="180" r="6" fill="#fff" opacity="0.2"/>
                <polyline points="130,180 155,195 180,205 220,205 245,195 270,180" fill="none" stroke="#fff" stroke-width="1.5" opacity="0.3"/>
                <polygon points="200,155 203,163 211,163 205,168 207,176 200,172 193,176 195,168 189,163 197,163" fill="var(--cta)" opacity="0.9"/>
            </svg>
        </div>
    </div>
</section>

<!-- How It Works -->
<section class="how-it-works">
    <div class="container">
        <div class="section-header">
            <span class="section-kicker">Cách hoạt động</span>
            <h2>3 bước để bắt đầu học</h2>
            <p>Hành trình học tiếng Anh của bạn chỉ với 3 bước đơn giản</p>
        </div>
        <div class="steps-grid">
            <div class="step-card">
                <div class="step-number">1</div>
                <div class="step-icon"><i class="fas fa-clipboard-check"></i></div>
                <h3>Kiểm tra đầu vào</h3>
                <p>Làm bài test 5 phút để xác định trình độ CEFR hiện tại của bạn.</p>
            </div>
            <div class="step-card">
                <div class="step-number">2</div>
                <div class="step-icon"><i class="fas fa-book-open"></i></div>
                <h3>Học theo lộ trình</h3>
                <p>Theo học các khóa học được cá nhân hóa theo đúng trình độ của bạn.</p>
            </div>
            <div class="step-card">
                <div class="step-number">3</div>
                <div class="step-icon"><i class="fas fa-trophy"></i></div>
                <h3>Nhận chứng chỉ</h3>
                <p>Hoàn thành bài thi cuối khóa và nhận chứng chỉ cho từng cấp độ.</p>
            </div>
        </div>
    </div>
</section>

<!-- CEFR Levels -->
<section class="section-dots section-py">
    <div class="container">
        <div class="section-header">
            <span class="section-kicker">Lộ trình học</span>
            <h2>5 cấp độ — từ cơ bản đến thành thạo</h2>
            <p>Mỗi cấp độ có 3 khóa học, được thiết kế theo khung tham chiếu Châu Âu (CEFR)</p>
        </div>
        <div class="cefr-timeline">
            <?php
            $cefrLabels = [
                'A1' => ['name' => 'Sơ cấp', 'desc' => 'Làm quen tiếng Anh', 'color' => 'var(--color-cefr-a1)', 'bg' => 'var(--color-cefr-a1-bg)'],
                'A2' => ['name' => 'Cơ bản', 'desc' => 'Giao tiếp đơn giản', 'color' => 'var(--color-cefr-a2)', 'bg' => 'var(--color-cefr-a2-bg)'],
                'B1' => ['name' => 'Trung cấp', 'desc' => 'Tự tin hội thoại', 'color' => 'var(--color-cefr-b1)', 'bg' => 'var(--color-cefr-b1-bg)'],
                'B2' => ['name' => 'Trên trung cấp', 'desc' => 'Giao tiếp lưu loát', 'color' => 'var(--color-cefr-b2)', 'bg' => 'var(--color-cefr-b2-bg)'],
                'C1' => ['name' => 'Nâng cao', 'desc' => 'Thành thạo học thuật', 'color' => 'var(--color-cefr-c1)', 'bg' => 'var(--color-cefr-c1-bg)'],
            ];
            foreach ($cefrLabels as $level => $info): ?>
            <div class="cefr-card" style="border-top-color:<?= $info['color'] ?>">
                <div class="cefr-level" style="color:<?= $info['color'] ?>"><?= $level ?></div>
                <div class="cefr-name"><?= $info['name'] ?></div>
                <div class="cefr-desc"><?= $info['desc'] ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Features -->
<section class="section-wave section-py bg-white">
    <div class="container">
        <div class="section-header">
            <span class="section-kicker">Tại sao chọn EngPath?</span>
            <h2>Học thông minh hơn, không vất vả hơn</h2>
        </div>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon feature-blue"><i class="fas fa-road"></i></div>
                <h3>Lộ trình rõ ràng</h3>
                <p>15 khóa học từ A1 đến C1. Mỗi khóa có chương, bài học, quiz và bài thi cuối khóa — bạn luôn biết mình đang ở đâu.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon feature-teal"><i class="fas fa-clipboard-check"></i></div>
                <h3>Quiz & Kiểm tra</h3>
                <p>Quiz tương tác sau mỗi chương, bài thi cuối khóa đánh giá toàn diện. Nhận chứng chỉ khi hoàn thành.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon feature-amber"><i class="fas fa-trophy"></i></div>
                <h3>Theo dõi tiến độ</h3>
                <p>Dashboard trực quan, streak hàng ngày, XP & level — giúp bạn duy trì động lực học tập mỗi ngày.</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cta-section">
    <div class="container">
        <h2 class="cta-heading">Sẵn sàng bắt đầu?</h2>
        <p class="cta-description">Tham gia cùng hàng nghìn học viên đang học tiếng Anh theo lộ trình trên EngPath.</p>
        <?php if (!$isLoggedIn): ?>
            <a href="<?= BASE_URL ?>/auth/register" class="btn btn-cta btn-lg"><i class="fas fa-rocket"></i> Bắt đầu học miễn phí</a>
        <?php else: ?>
            <a href="<?= BASE_URL ?>/course" class="btn btn-cta btn-lg"><i class="fas fa-play"></i> Tiếp tục học ngay</a>
        <?php endif; ?>
    </div>
</section>

</div><!-- /.landing-page -->
