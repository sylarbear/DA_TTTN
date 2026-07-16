<?php
$overall = $data['overall'] ?? [];
$topicProgress = $data['topic_progress'] ?? [];
$nextTopic = $topicProgress[0] ?? null;
?>

<!-- Hero -->
<section class="bg-gradient-to-br from-brand-700 to-brand-800 text-white py-16">
    <div class="max-w-7xl mx-auto px-6">
        <div class="grid lg:grid-cols-[1fr_380px] gap-10 items-start">
            <div>
                <span class="inline-flex items-center gap-2 text-brand-200 font-extrabold text-xs uppercase tracking-wider mb-3"><?= Middleware::isPro() ? 'PRO' : 'FREE' ?> Plan</span>
                <h1 class="font-heading text-3xl lg:text-4xl font-bold mb-3">Chào <?= htmlspecialchars($_SESSION['full_name'] ?? $_SESSION['username'] ?? 'bạn') ?>,<br>hôm nay học gì?</h1>
                <p class="text-white/65 mb-7">Theo dõi tiến độ, chọn bài học tiếp theo và duy trì streak mỗi ngày.</p>
                <a href="<?= BASE_URL ?>/course" class="inline-flex items-center gap-2 px-6 py-3 bg-brand-500 hover:bg-brand-400 text-white font-bold rounded-lg text-sm transition shadow-lg shadow-black/10">
                    <i class="fas fa-book-open"></i> Tiếp tục học
                </a>
            </div>
            <div class="bg-white/10 border border-white/15 rounded-2xl p-6 backdrop-blur">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-sm text-white/80 font-medium"><i class="fas fa-bullseye mr-2"></i>Mục tiêu hôm nay</span>
                    <span class="text-xs font-bold bg-white/20 text-white px-3 py-1 rounded-full"><?= Middleware::isPro() ? 'PRO' : 'FREE' ?></span>
                </div>
                <div class="font-heading text-2xl font-bold mb-4">
                    <?= !empty($streak) ? number_format($streak['daily_xp_today']) . ' / ' . number_format($streak['daily_goal']) . ' XP' : 'Hoàn thành 1 bài học' ?>
                </div>
                <div class="h-2 bg-white/20 rounded-full overflow-hidden mb-4">
                    <div class="h-full bg-white rounded-full transition-all duration-500" style="width:<?= !empty($streak) ? min(100, (int)$streak['daily_progress']) : 35 ?>%"></div>
                </div>
                <a href="<?= BASE_URL ?>/course" class="text-sm font-bold text-white/80 hover:text-white transition flex items-center gap-1.5">Học khóa học <i class="fas fa-arrow-right text-xs"></i></a>
            </div>
        </div>
    </div>
</section>

<section class="py-12 bg-slate-50/50">
    <div class="max-w-7xl mx-auto px-6">
        <!-- Streak Cards -->
        <?php if (!empty($streak)): ?>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4 mb-10">
            <div class="bg-white rounded-xl p-5 border border-gray-100 flex items-center gap-4 hover:-translate-y-0.5 hover:shadow-md transition-all duration-200">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-amber-500 to-red-500 text-white flex items-center justify-center text-lg flex-shrink-0"><i class="fas fa-fire"></i></div>
                <div><div class="font-heading text-xl font-extrabold text-slate-800"><?= (int)$streak['current_streak'] ?></div><div class="text-xs text-slate-400 mt-0.5">ngày liên tiếp</div></div>
            </div>
            <div class="bg-white rounded-xl p-5 border border-gray-100 flex items-center gap-4 hover:-translate-y-0.5 hover:shadow-md transition-all duration-200">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-brand-500 to-brand-700 text-white flex items-center justify-center text-lg flex-shrink-0"><i class="fas fa-star"></i></div>
                <div><div class="font-heading text-xl font-extrabold text-slate-800">Lv.<?= (int)$streak['level'] ?></div><div class="text-xs text-slate-400 mt-0.5"><?= number_format($streak['total_xp']) ?> XP</div></div>
            </div>
            <div class="bg-white rounded-xl p-5 border border-gray-100 flex items-center gap-4 hover:-translate-y-0.5 hover:shadow-md transition-all duration-200">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 text-white flex items-center justify-center text-lg flex-shrink-0"><i class="fas fa-bullseye"></i></div>
                <div><div class="font-heading text-xl font-extrabold text-slate-800"><?= (int)$streak['daily_progress'] ?>%</div><div class="text-xs text-slate-400 mt-0.5">mục tiêu hôm nay</div></div>
            </div>
            <div class="bg-white rounded-xl p-5 border border-gray-100 flex items-center gap-4 hover:-translate-y-0.5 hover:shadow-md transition-all duration-200">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-blue-500 to-blue-700 text-white flex items-center justify-center text-lg flex-shrink-0"><i class="fas fa-trophy"></i></div>
                <div><div class="font-heading text-xl font-extrabold text-slate-800"><?= (int)$streak['longest_streak'] ?></div><div class="text-xs text-slate-400 mt-0.5">streak cao nhất</div></div>
            </div>
            <?php if (!empty($placement)): ?>
            <div class="bg-white rounded-xl p-5 border border-gray-100 flex items-center gap-4 hover:-translate-y-0.5 hover:shadow-md transition-all duration-200">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-pink-500 to-brand-500 text-white flex items-center justify-center text-lg flex-shrink-0"><i class="fas fa-certificate"></i></div>
                <div><div class="font-heading text-xl font-extrabold text-slate-800"><?= htmlspecialchars($placement['final_cefr']) ?></div><div class="text-xs text-slate-400 mt-0.5">trình độ CEFR</div></div>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Placement Banner -->
        <?php if (empty($placement) && empty($user['placement_level'])): ?>
        <div class="flex items-center gap-5 bg-amber-50 border border-amber-200 rounded-xl p-5 mb-10 flex-wrap">
            <div class="w-12 h-12 rounded-xl bg-amber-500 text-white flex items-center justify-center text-xl flex-shrink-0"><i class="fas fa-clipboard-check"></i></div>
            <div class="flex-1 min-w-[200px]">
                <strong class="block font-heading text-slate-800">Xác định trình độ của bạn</strong>
                <p class="text-sm text-slate-500">Làm bài kiểm tra đầu vào 5 phút để nhận lộ trình học phù hợp.</p>
            </div>
            <a href="<?= BASE_URL ?>/placement/intro" class="px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-lg text-sm transition flex-shrink-0">Kiểm tra ngay</a>
        </div>
        <?php endif; ?>

        <!-- Main Grid -->
        <div class="grid lg:grid-cols-2 gap-6 mb-10">
            <div class="bg-white rounded-xl border border-gray-100 p-7">
                <h2 class="font-heading text-lg font-bold text-slate-800 mb-5 flex items-center gap-2.5"><i class="fas fa-graduation-cap text-brand-500"></i> Bài học tiếp theo</h2>
                <?php if ($nextTopic): ?>
                    <p class="font-bold text-lg text-slate-800 mb-1"><?= htmlspecialchars($nextTopic['topic_name']) ?></p>
                    <p class="text-sm text-slate-500 mb-5">Tiếp tục hoàn thành từ vựng và bài học trong chủ đề này.</p>
                    <?php
                        $totalItems = max(1, (int)$nextTopic['total_vocab'] + (int)$nextTopic['total_lessons'] + (int)$nextTopic['total_tests']);
                        $doneItems = (int)$nextTopic['vocab_learned'] + (int)$nextTopic['lessons_completed'] + (int)$nextTopic['tests_passed'];
                        $pct = min(100, round($doneItems / $totalItems * 100));
                    ?>
                    <div class="h-2 bg-gray-100 rounded-full overflow-hidden mb-2">
                        <div class="h-full bg-gradient-to-r from-brand-500 to-brand-400 rounded-full transition-all duration-500" style="width:<?= $pct ?>%"></div>
                    </div>
                    <small class="text-slate-400"><?= $pct ?>% hoàn thành</small>
                    <div class="mt-5">
                        <a href="<?= BASE_URL ?>/course" class="inline-flex items-center gap-1.5 px-5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-bold rounded-lg text-sm transition">Học tiếp <i class="fas fa-arrow-right text-xs"></i></a>
                    </div>
                <?php else: ?>
                    <p class="text-slate-500">Chọn một chủ đề để bắt đầu theo dõi tiến độ.</p>
                    <a href="<?= BASE_URL ?>/course" class="inline-flex items-center gap-1.5 px-5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-bold rounded-lg text-sm transition mt-4">Chọn khóa học</a>
                <?php endif; ?>
            </div>

            <div class="bg-white rounded-xl border border-gray-100 p-7">
                <h2 class="font-heading text-lg font-bold text-slate-800 mb-5 flex items-center gap-2.5"><i class="fas fa-chart-simple text-brand-500"></i> Tổng quan</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div class="text-center p-5 bg-slate-50 rounded-xl">
                        <div class="text-3xl font-extrabold text-brand-600 font-heading"><?= (int)($overall['total_vocab_learned'] ?? 0) ?></div>
                        <div class="text-xs text-slate-400 mt-1">Từ vựng đã học</div>
                    </div>
                    <div class="text-center p-5 bg-slate-50 rounded-xl">
                        <div class="text-3xl font-extrabold text-emerald-500 font-heading"><?= (int)($overall['total_lessons_completed'] ?? 0) ?></div>
                        <div class="text-xs text-slate-400 mt-1">Bài học hoàn thành</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="grid lg:grid-cols-2 gap-6 mb-10">
            <div class="bg-white rounded-xl border border-gray-100 p-7">
                <h3 class="font-heading font-bold text-slate-600 text-sm mb-5"><i class="fas fa-chart-bar text-brand-500 mr-2"></i>Điểm theo chủ đề</h3>
                <canvas id="topicScoreChart"></canvas>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 p-7">
                <h3 class="font-heading font-bold text-slate-600 text-sm mb-5"><i class="fas fa-chart-pie text-brand-500 mr-2"></i>Phân bố kỹ năng</h3>
                <canvas id="skillRadarChart"></canvas>
            </div>
        </div>

        <!-- Progress Table -->
        <div class="bg-white rounded-xl border border-gray-100 p-7">
            <h2 class="font-heading text-lg font-bold text-slate-800 mb-5 flex items-center gap-2.5"><i class="fas fa-tasks text-brand-500"></i> Tiến độ theo chủ đề</h2>
            <?php if (empty($topicProgress)): ?>
                <div class="text-center py-12">
                    <i class="fas fa-chart-line text-5xl text-slate-200 mb-4 block"></i>
                    <h3 class="font-heading text-lg font-bold text-slate-600 mb-1">Chưa có dữ liệu</h3>
                    <p class="text-slate-400 mb-6">Hãy bắt đầu học để theo dõi tiến độ của bạn.</p>
                    <a href="<?= BASE_URL ?>/course" class="inline-flex items-center gap-2 px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-lg text-sm transition">Bắt đầu học</a>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b-2 border-gray-100 text-left text-xs text-slate-400 uppercase tracking-wider">
                                <th class="pb-3 pr-4">Chủ đề</th><th class="pb-3 pr-4">Level</th><th class="pb-3 pr-4">Từ vựng</th><th class="pb-3 pr-4">Bài học</th><th class="pb-3 pr-4">Điểm</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($topicProgress as $tp): ?>
                                <tr class="border-b border-gray-50 hover:bg-slate-50/50 transition-colors">
                                    <td class="py-3 pr-4 font-semibold text-slate-700"><?= htmlspecialchars($tp['topic_name']) ?></td>
                                    <td class="py-3 pr-4"><span class="px-2.5 py-1 rounded-full text-xs font-bold <?= match($tp['level']){'beginner'=>'bg-emerald-50 text-emerald-600','intermediate'=>'bg-sky-50 text-sky-600','advanced'=>'bg-red-50 text-red-600',default=>'bg-slate-100 text-slate-500'} ?>"><?= ucfirst(htmlspecialchars($tp['level'])) ?></span></td>
                                    <td class="py-3 pr-4"><?= min($tp['vocab_learned'], $tp['total_vocab']) ?>/<?= $tp['total_vocab'] ?></td>
                                    <td class="py-3 pr-4"><?= min($tp['lessons_completed'], $tp['total_lessons']) ?>/<?= $tp['total_lessons'] ?></td>
                                    <td class="py-3 font-bold text-slate-800"><?= (int)$tp['total_score'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script src="<?= BASE_URL ?>/js/dashboard.js?v=<?= APP_VERSION ?>"></script>
<script>
var chartData = <?= json_encode([
    'topics' => array_column($topicProgress, 'topic_name'),
    'scores' => array_column($topicProgress, 'total_score'),
    'vocab' => array_column($topicProgress, 'vocab_learned'),
    'overall' => $overall,
]) ?>;
if (typeof initDashboardCharts === 'function') {
    initDashboardCharts(chartData);
}
</script>
