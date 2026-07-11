<?php
$overall = $data['overall'] ?? [];
$topicProgress = $data['topic_progress'] ?? [];
$recentTests = $data['recent_tests'] ?? [];
$nextTopic = $topicProgress[0] ?? null;
?>

<div class="dashboard-page">

<section class="dashboard-hero">
    <div class="container dashboard-hero-grid">
        <div class="dashboard-welcome">
            <span class="section-kicker"><?= Middleware::isPro() ? 'PRO' : 'FREE' ?> Plan</span>
            <h1>Chào <?= htmlspecialchars($_SESSION['full_name'] ?? $_SESSION['username'] ?? 'bạn') ?>,<br>hôm nay học gì?</h1>
            <p>Theo dõi tiến độ, chọn bài học tiếp theo và duy trì streak mỗi ngày.</p>
            <div class="hero-actions">
                <a href="<?= BASE_URL ?>/course" class="btn btn-cta btn-lg"><i class="fas fa-book-open"></i> Tiếp tục học</a>
            </div>
        </div>

        <div class="daily-plan-card">
            <div class="plan-header">
                <span class="plan-title"><i class="fas fa-bullseye"></i> Mục tiêu hôm nay</span>
                <span class="pill-soft"><?= Middleware::isPro() ? 'PRO' : 'FREE' ?></span>
            </div>
            <div class="plan-goal">
                <?= !empty($streak) ? number_format($streak['daily_xp_today']) . ' / ' . number_format($streak['daily_goal']) . ' XP' : 'Hoàn thành 1 bài học' ?>
            </div>
            <div class="plan-progress-bar">
                <div class="plan-progress-fill" style="width:<?= !empty($streak) ? min(100, (int)$streak['daily_progress']) : 35 ?>%"></div>
            </div>
            <a href="<?= BASE_URL ?>/course" class="plan-link">Học khóa học <i class="fas fa-arrow-right"></i></a>
        </div>
    </div>
</section>

<section class="dashboard-section">
    <div class="container">
        <?php if (!empty($streak)): ?>
        <div class="streak-row">
            <div class="streak-card">
                <div class="streak-card-icon" style="background:linear-gradient(135deg, #F59E0B, #EF4444);">
                    <i class="fas fa-fire"></i>
                </div>
                <div>
                    <div class="streak-card-value"><?= (int)$streak['current_streak'] ?></div>
                    <div class="streak-card-label">ngày liên tiếp</div>
                </div>
            </div>
            <div class="streak-card">
                <div class="streak-card-icon" style="background:linear-gradient(135deg, #8B5CF6, #6366F1);">
                    <i class="fas fa-star"></i>
                </div>
                <div>
                    <div class="streak-card-value">Lv.<?= (int)$streak['level'] ?></div>
                    <div class="streak-card-label"><?= number_format($streak['total_xp']) ?> XP</div>
                </div>
            </div>
            <div class="streak-card">
                <div class="streak-card-icon" style="background:linear-gradient(135deg, #10B981, #059669);">
                    <i class="fas fa-bullseye"></i>
                </div>
                <div>
                    <div class="streak-card-value"><?= (int)$streak['daily_progress'] ?>%</div>
                    <div class="streak-card-label">mục tiêu hôm nay</div>
                </div>
            </div>
            <div class="streak-card">
                <div class="streak-card-icon" style="background:linear-gradient(135deg, #3B82F6, #2563EB);">
                    <i class="fas fa-trophy"></i>
                </div>
                <div>
                    <div class="streak-card-value"><?= (int)$streak['longest_streak'] ?></div>
                    <div class="streak-card-label">streak cao nhất</div>
                </div>
            </div>
            <?php if (!empty($placement)): ?>
            <div class="streak-card">
                <div class="streak-card-icon" style="background:linear-gradient(135deg, #EC4899, #8B5CF6);">
                    <i class="fas fa-certificate"></i>
                </div>
                <div>
                    <div class="streak-card-value"><?= htmlspecialchars($placement['final_cefr']) ?></div>
                    <div class="streak-card-label">trình độ CEFR</div>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php if (empty($placement) && empty($user['placement_level'])): ?>
        <div class="placement-banner">
            <div class="placement-banner-icon"><i class="fas fa-clipboard-check"></i></div>
            <div class="placement-banner-text">
                <strong>Xác định trình độ của bạn</strong>
                <p>Làm bài kiểm tra đầu vào 5 phút để nhận lộ trình học phù hợp.</p>
            </div>
            <a href="<?= BASE_URL ?>/placement/intro" class="btn btn-primary btn-sm" style="flex-shrink:0;">Kiểm tra ngay</a>
        </div>
        <?php endif; ?>

        <div class="dashboard-grid">
            <div class="dashboard-section-card">
                <h3><i class="fas fa-graduation-cap"></i> Bài học tiếp theo</h3>
                <?php if ($nextTopic): ?>
                    <p style="font-weight:700; font-size:1.1rem; margin-bottom:4px;"><?= htmlspecialchars($nextTopic['topic_name']) ?></p>
                    <p style="color:var(--color-text-secondary); font-size:0.9rem; margin-bottom:16px;">Tiếp tục hoàn thành từ vựng và bài học trong chủ đề này.</p>
                    <?php
                        $totalItems = max(1, (int)$nextTopic['total_vocab'] + (int)$nextTopic['total_lessons'] + (int)$nextTopic['total_tests']);
                        $doneItems = (int)$nextTopic['vocab_learned'] + (int)$nextTopic['lessons_completed'] + (int)$nextTopic['tests_passed'];
                        $pct = min(100, round($doneItems / $totalItems * 100));
                    ?>
                    <div class="plan-progress-bar" style="margin-bottom:8px;">
                        <div class="plan-progress-fill" style="width:<?= $pct ?>%"></div>
                    </div>
                    <small style="color:var(--color-text-tertiary);"><?= $pct ?>% hoàn thành</small>
                    <div style="margin-top:16px;">
                        <a href="<?= BASE_URL ?>/course" class="btn btn-primary btn-sm">Học tiếp <i class="fas fa-arrow-right"></i></a>
                    </div>
                <?php else: ?>
                    <p style="color:var(--color-text-secondary);">Chọn một chủ đề để bắt đầu theo dõi tiến độ.</p>
                    <a href="<?= BASE_URL ?>/course" class="btn btn-primary btn-sm" style="margin-top:12px;">Chọn khóa học</a>
                <?php endif; ?>
            </div>

            <div class="dashboard-section-card">
                <h3><i class="fas fa-chart-simple"></i> Tổng quan</h3>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:var(--space-4);">
                    <div style="text-align:center; padding:var(--space-4); background:var(--color-bg); border-radius:var(--radius-lg);">
                        <div style="font-size:2rem; font-weight:800; color:var(--color-primary);"><?= (int)($overall['total_vocab_learned'] ?? 0) ?></div>
                        <div style="font-size:0.8rem; color:var(--color-text-tertiary);">Từ vựng đã học</div>
                    </div>
                    <div style="text-align:center; padding:var(--space-4); background:var(--color-bg); border-radius:var(--radius-lg);">
                        <div style="font-size:2rem; font-weight:800; color:var(--color-emerald-500);"><?= (int)($overall['total_lessons_completed'] ?? 0) ?></div>
                        <div style="font-size:0.8rem; color:var(--color-text-tertiary);">Bài học hoàn thành</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="charts-grid">
            <div class="chart-card">
                <h3><i class="fas fa-chart-bar"></i> Điểm theo chủ đề</h3>
                <canvas id="topicScoreChart"></canvas>
            </div>
            <div class="chart-card">
                <h3><i class="fas fa-chart-pie"></i> Phân bố kỹ năng</h3>
                <canvas id="skillRadarChart"></canvas>
            </div>
        </div>

        <div class="dashboard-section-card" style="margin-bottom:var(--space-8);">
            <h3><i class="fas fa-tasks"></i> Tiến độ theo chủ đề</h3>
            <?php if (empty($topicProgress)): ?>
                <div class="empty-state">
                    <i class="fas fa-chart-line"></i>
                    <h3>Chưa có dữ liệu</h3>
                    <p>Hãy bắt đầu học để theo dõi tiến độ của bạn.</p>
                    <a href="<?= BASE_URL ?>/course" class="btn btn-primary">Bắt đầu học</a>
                </div>
            <?php else: ?>
                <div class="progress-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Chủ đề</th>
                                <th>Level</th>
                                <th>Từ vựng</th>
                                <th>Bài học</th>
                                <th>Điểm</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($topicProgress as $tp): ?>
                                <tr>
                                    <td style="font-weight:600;"><?= htmlspecialchars($tp['topic_name']) ?></td>
                                    <td><span class="topic-level level-<?= htmlspecialchars($tp['level']) ?>"><?= ucfirst(htmlspecialchars($tp['level'])) ?></span></td>
                                    <td><?= min($tp['vocab_learned'], $tp['total_vocab']) ?>/<?= $tp['total_vocab'] ?></td>
                                    <td><?= min($tp['lessons_completed'], $tp['total_lessons']) ?>/<?= $tp['total_lessons'] ?></td>
                                    <td><strong><?= (int)$tp['total_score'] ?></strong></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

</div><!-- /.dashboard-page -->

<script src="<?= BASE_URL ?>/js/dashboard.js?v=<?= APP_VERSION ?>"></script>
<script>
const chartData = <?= json_encode([
    'topics' => array_column($topicProgress, 'topic_name'),
    'scores' => array_column($topicProgress, 'total_score'),
    'vocab' => array_column($topicProgress, 'vocab_learned'),
    'overall' => $overall,
]) ?>;

if (typeof initDashboardCharts === 'function') {
    initDashboardCharts(chartData);
}
</script>
