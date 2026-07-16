<!-- Course Catalog -->
<section class="bg-gradient-to-br from-brand-50 to-blue-50 py-16 border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-6">
        <h1 class="font-heading text-3xl lg:text-4xl font-bold text-slate-800 mb-2">Khóa học của tôi</h1>
        <p class="text-slate-500 text-base">Lộ trình học tập được cá nhân hóa dựa trên trình độ của bạn.</p>
    </div>
</section>

<section class="py-12 bg-slate-50/50">
    <div class="max-w-7xl mx-auto px-6">
        <?php if (empty($active) && empty($locked) && empty($mastered)): ?>
            <div class="text-center py-20">
                <i class="fas fa-clipboard-check text-6xl text-slate-200 mb-6 block"></i>
                <h3 class="font-heading text-xl font-bold text-slate-600 mb-2">Bạn chưa xác định trình độ</h3>
                <p class="text-slate-400 mb-8">Hãy làm bài kiểm tra đầu vào để nhận lộ trình học phù hợp.</p>
                <a href="<?= BASE_URL ?>/placement/intro" class="inline-flex items-center gap-2 px-6 py-3 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-lg text-sm transition">Làm bài kiểm tra</a>
            </div>
        <?php endif; ?>

        <!-- Active Courses -->
        <?php if (!empty($active)): ?>
        <div class="mb-14">
            <h2 class="font-heading text-2xl font-bold text-slate-800 mb-7 flex items-center gap-3">
                <span class="w-2 h-8 rounded bg-brand-500"></span> Đang học
            </h2>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <?php foreach ($active as $c):
                    $card = [
                        'url'    => BASE_URL . '/course/show/' . $c['id'],
                        'cefr'   => $c['cefr_level'],
                        'title'  => htmlspecialchars($c['title']),
                        'desc'   => htmlspecialchars($c['description'] ?? ''),
                        'pct'    => $c['completion_percent'] ?? 0,
                        'opacity'=> '',
                        'badge'  => '',
                        'footer_left'  => !empty($c['last_lesson']) ? '<span class="text-xs text-slate-400 flex items-center gap-1.5 truncate max-w-[180px]"><i class="fas fa-history text-amber-500"></i> ' . htmlspecialchars($c['last_lesson']['title']) . '</span>' : '',
                        'footer_right' => '<span class="text-xs font-bold text-brand-600 flex items-center gap-1.5 ml-auto"><i class="fas fa-play text-[10px]"></i> Tiếp tục học</span>',
                    ];
                    require __DIR__ . '/_course-card.php';
                endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Mastered Courses -->
        <?php if (!empty($mastered)): ?>
        <div class="mb-14">
            <h2 class="font-heading text-2xl font-bold text-slate-800 mb-7 flex items-center gap-3">
                <span class="w-2 h-8 rounded bg-emerald-500"></span> Đã hoàn thành — Ôn tập
            </h2>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <?php foreach ($mastered as $c):
                    $card = [
                        'url'    => BASE_URL . '/course/show/' . $c['id'],
                        'cefr'   => $c['cefr_level'],
                        'title'  => htmlspecialchars($c['title']),
                        'desc'   => htmlspecialchars($c['description'] ?? ''),
                        'pct'    => 0,
                        'opacity'=> 'opacity-85 hover:opacity-100',
                        'badge'  => '<span class="absolute top-3 left-3 text-white text-lg"><i class="fas fa-check-circle"></i></span>',
                        'footer_left'  => '<span class="text-xs font-bold text-emerald-600 flex items-center gap-1.5"><i class="fas fa-redo"></i> Ôn tập</span>',
                        'footer_right' => '<a href="' . BASE_URL . '/course/certificate/' . $c['id'] . '" class="text-xs font-bold text-brand-600 hover:text-brand-700 flex items-center gap-1.5" onclick="event.stopPropagation()"><i class="fas fa-certificate"></i> Chứng chỉ</a>',
                    ];
                    require __DIR__ . '/_course-card.php';
                endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Locked Courses -->
        <?php if (!empty($locked)): ?>
        <div>
            <h2 class="font-heading text-2xl font-bold text-slate-800 mb-7 flex items-center gap-3">
                <span class="w-2 h-8 rounded bg-slate-300"></span> Sẽ mở sau
            </h2>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <?php foreach ($locked as $c):
                    $card = [
                        'url'     => '',
                        'cefr'    => $c['cefr_level'],
                        'title'   => htmlspecialchars($c['title']),
                        'desc'    => htmlspecialchars($c['description'] ?? ''),
                        'pct'     => 0,
                        'opacity' => 'opacity-50 pointer-events-none',
                        'badge'   => '',
                        'footer_full' => '<span class="text-xs text-slate-400 flex items-center gap-1.5"><i class="fas fa-lock"></i> Hoàn thành khóa trước để mở</span>',
                        'locked'  => true,
                    ];
                    require __DIR__ . '/_course-card.php';
                endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<link rel="stylesheet" href="<?= BASE_URL ?>/css/course.css?v=<?= APP_VERSION ?>">
