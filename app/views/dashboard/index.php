<?php
$overall = $data['overall'] ?? [];
$topicProgress = $data['topic_progress'] ?? [];
$recentTests = $data['recent_tests'] ?? [];
$recentSpeaking = $data['recent_speaking'] ?? [];
$nextTopic = $topicProgress[0] ?? null;
?>

<section class="dashboard-hero">
    <div class="container dashboard-hero-grid">
        <div>
            <span class="section-kicker">Your learning space</span>
            <h1>Chào <?= htmlspecialchars($_SESSION['full_name'] ?? $_SESSION['username'] ?? 'bạn') ?>, hôm nay học gì?</h1>
            <p>Dashboard giúp bạn nhìn nhanh tiến độ, chọn bài học tiếp theo và quay lại các hoạt động quan trọng nhất.</p>
            <div class="dashboard-actions">
                <a href="<?= BASE_URL ?>/topic" class="btn btn-primary"><i class="fas fa-book-open"></i> Tiếp tục học</a>
                <a href="<?= BASE_URL ?>/speaking" class="btn btn-outline"><i class="fas fa-microphone"></i> Luyện speaking</a>
            </div>
        </div>

        <div class="daily-plan-card">
            <div class="study-card-top">
                <div>
                    <small>Mục tiêu hôm nay</small>
                    <strong><?= !empty($streak) ? number_format($streak['daily_xp_today']) . '/' . number_format($streak['daily_goal']) . ' XP' : 'Hoàn thành 1 bài học' ?></strong>
                </div>
                <span class="pill-soft"><?= Middleware::isPro() ? 'PRO' : 'FREE' ?></span>
            </div>
            <div class="xp-mini-bar large">
                <div class="xp-mini-fill" style="width:<?= !empty($streak) ? min(100, (int) $streak['daily_progress']) : 35 ?>%"></div>
            </div>
            <div class="daily-plan-list">
                <a href="<?= BASE_URL ?>/topic"><i class="fas fa-clone"></i> Ôn flashcard 10 phút</a>
                <a href="<?= BASE_URL ?>/test"><i class="fas fa-clipboard-check"></i> Làm một bài kiểm tra</a>
                <a href="<?= BASE_URL ?>/speaking"><i class="fas fa-microphone-lines"></i> Luyện nói một chủ đề</a>
            </div>
        </div>
    </div>
</section>

<section class="dashboard-section">
    <div class="container">
        <?php if (!empty($streak)): ?>
        <div class="streak-bar learning-stats-row">
            <div class="streak-item">
                <div class="streak-icon"><i class="fas fa-fire"></i></div>
                <div>
                    <strong class="streak-count"><?= (int) $streak['current_streak'] ?></strong>
                    <small>ngày liên tiếp</small>
                </div>
            </div>
            <div class="streak-item">
                <div class="streak-icon"><i class="fas fa-star"></i></div>
                <div>
                    <strong>Level <?= (int) $streak['level'] ?></strong>
                    <div class="xp-mini-bar"><div class="xp-mini-fill" style="width:<?= (int) $streak['level_progress'] ?>%"></div></div>
                    <small><?= number_format($streak['total_xp']) ?> XP</small>
                </div>
            </div>
            <div class="streak-item">
                <div class="streak-icon"><i class="fas fa-bullseye"></i></div>
                <div>
                    <strong><?= (int) $streak['daily_progress'] ?>%</strong>
                    <small>mục tiêu hôm nay</small>
                </div>
            </div>
            <div class="streak-item">
                <div class="streak-icon"><i class="fas fa-trophy"></i></div>
                <div>
                    <strong><?= (int) $streak['longest_streak'] ?></strong>
                    <small>streak cao nhất</small>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-font"></i></div>
                <div class="stat-content">
                    <span class="stat-value"><?= (int) ($overall['total_vocab_learned'] ?? 0) ?></span>
                    <span class="stat-title">Từ vựng đã học</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-book"></i></div>
                <div class="stat-content">
                    <span class="stat-value"><?= (int) ($overall['total_lessons_completed'] ?? 0) ?></span>
                    <span class="stat-title">Bài học hoàn thành</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-clipboard-check"></i></div>
                <div class="stat-content">
                    <span class="stat-value"><?= (int) ($overall['total_tests_passed'] ?? 0) ?></span>
                    <span class="stat-title">Bài test đã đạt</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-microphone"></i></div>
                <div class="stat-content">
                    <span class="stat-value"><?= (int) ($overall['total_speaking_practiced'] ?? 0) ?></span>
                    <span class="stat-title">Lượt luyện nói</span>
                </div>
            </div>
        </div>

        <div class="dashboard-learning-grid">
            <div class="section-card next-lesson-card">
                <span class="section-kicker">Bài học tiếp theo</span>
                <?php if ($nextTopic): ?>
                    <h3><?= htmlspecialchars($nextTopic['topic_name']) ?></h3>
                    <p>Tiếp tục hoàn thành từ vựng, bài học và speaking trong chủ đề này.</p>
                    <div class="mini-progress">
                        <?php
                            $totalItems = max(1, (int) $nextTopic['total_vocab'] + (int) $nextTopic['total_lessons'] + (int) $nextTopic['total_tests'] + (int) $nextTopic['total_speaking']);
                            $doneItems = (int) $nextTopic['vocab_learned'] + (int) $nextTopic['lessons_completed'] + (int) $nextTopic['tests_passed'] + (int) $nextTopic['speaking_practiced'];
                            $pct = min(100, round($doneItems / $totalItems * 100));
                        ?>
                        <div class="mini-bar" style="width:<?= $pct ?>%"></div>
                    </div>
                    <small><?= $pct ?>% hoàn thành</small>
                    <a href="<?= BASE_URL ?>/topic/show/<?= $nextTopic['topic_id'] ?>" class="btn btn-primary btn-sm">Học tiếp <i class="fas fa-arrow-right"></i></a>
                <?php else: ?>
                    <h3>Bắt đầu khóa học đầu tiên</h3>
                    <p>Chọn một chủ đề để EngPath có dữ liệu theo dõi tiến độ cho bạn.</p>
                    <a href="<?= BASE_URL ?>/topic" class="btn btn-primary btn-sm">Chọn khóa học</a>
                <?php endif; ?>
            </div>

            <div class="section-card quick-practice-card">
                <span class="section-kicker">Luyện nhanh</span>
                <div class="practice-links">
                    <a href="<?= BASE_URL ?>/topic"><i class="fas fa-clone"></i><span>Flashcard</span></a>
                    <a href="<?= BASE_URL ?>/test"><i class="fas fa-clipboard-check"></i><span>Quiz</span></a>
                    <a href="<?= BASE_URL ?>/speaking"><i class="fas fa-microphone"></i><span>Speaking</span></a>
                    <a href="<?= BASE_URL ?>/grammar"><i class="fas fa-graduation-cap"></i><span>Grammar</span></a>
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

        <div class="section-card">
            <h3><i class="fas fa-tasks"></i> Tiến độ theo chủ đề</h3>
            <?php if (empty($topicProgress)): ?>
                <div class="empty-state">
                    <i class="fas fa-chart-line"></i>
                    <p>Chưa có dữ liệu. Hãy bắt đầu học để theo dõi tiến độ.</p>
                    <a href="<?= BASE_URL ?>/topic" class="btn btn-primary">Bắt đầu học</a>
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
                                <th>Test</th>
                                <th>Speaking</th>
                                <th>Điểm</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($topicProgress as $tp): ?>
                                <tr>
                                    <td><a href="<?= BASE_URL ?>/topic/show/<?= $tp['topic_id'] ?>"><?= htmlspecialchars($tp['topic_name']) ?></a></td>
                                    <td><span class="topic-level level-<?= htmlspecialchars($tp['level']) ?>"><?= ucfirst(htmlspecialchars($tp['level'])) ?></span></td>
                                    <td><?= min($tp['vocab_learned'], $tp['total_vocab']) ?>/<?= $tp['total_vocab'] ?></td>
                                    <td><?= min($tp['lessons_completed'], $tp['total_lessons']) ?>/<?= $tp['total_lessons'] ?></td>
                                    <td><?= min($tp['tests_passed'], $tp['total_tests']) ?>/<?= $tp['total_tests'] ?></td>
                                    <td><?= min($tp['speaking_practiced'], $tp['total_speaking']) ?>/<?= $tp['total_speaking'] ?></td>
                                    <td><strong><?= (int) $tp['total_score'] ?></strong></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <div class="activities-grid">
            <div class="section-card">
                <h3><i class="fas fa-clipboard-check"></i> Bài test gần đây</h3>
                <?php if (empty($recentTests)): ?>
                    <p class="empty-text">Chưa có bài test nào.</p>
                <?php else: ?>
                    <div class="activity-list">
                        <?php foreach (array_slice($recentTests, 0, 5) as $t): ?>
                            <div class="activity-item">
                                <div class="activity-icon type-<?= htmlspecialchars($t['test_type']) ?>">
                                    <i class="fas fa-<?= $t['test_type'] === 'quiz' ? 'question-circle' : ($t['test_type'] === 'listening' ? 'headphones' : 'book-reader') ?>"></i>
                                </div>
                                <div class="activity-info">
                                    <strong><?= htmlspecialchars($t['test_title']) ?></strong>
                                    <small><?= htmlspecialchars($t['topic_name']) ?> - <?= date('d/m/Y', strtotime($t['completed_at'])) ?></small>
                                </div>
                                <div class="activity-score">
                                    <?php $pct = $t['total_points'] > 0 ? round($t['score'] / $t['total_points'] * 100) : 0; ?>
                                    <span class="<?= $pct >= 60 ? 'text-success' : 'text-danger' ?>"><?= $pct ?>%</span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="section-card">
                <h3><i class="fas fa-microphone"></i> Speaking gần đây</h3>
                <?php if (empty($recentSpeaking)): ?>
                    <p class="empty-text">Chưa có lượt luyện nói nào.</p>
                <?php else: ?>
                    <div class="activity-list">
                        <?php foreach (array_slice($recentSpeaking, 0, 5) as $s): ?>
                            <div class="activity-item">
                                <div class="activity-icon"><i class="fas fa-microphone"></i></div>
                                <div class="activity-info">
                                    <strong><?= htmlspecialchars(mb_substr($s['prompt_text'], 0, 56)) ?>...</strong>
                                    <small><?= htmlspecialchars($s['topic_name']) ?> - <?= date('d/m/Y', strtotime($s['created_at'])) ?></small>
                                </div>
                                <div class="activity-score">
                                    <span class="<?= $s['overall_score'] >= 60 ? 'text-success' : 'text-danger' ?>"><?= (int) $s['overall_score'] ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<script src="<?= BASE_URL ?>/js/dashboard.js"></script>
<script>
const chartData = <?= json_encode([
    'topics' => array_column($topicProgress, 'topic_name'),
    'scores' => array_column($topicProgress, 'total_score'),
    'vocab'  => array_column($topicProgress, 'vocab_learned'),
    'overall' => $overall
]) ?>;

if (typeof initDashboardCharts === 'function') {
    initDashboardCharts(chartData);
}
</script>
