<!-- Dashboard Page -->
<section class="page-header">
    <div class="container">
        <h1><i class="fas fa-chart-line"></i> Dashboard</h1>
        <p>Theo dõi tiến độ học tập của bạn</p>
    </div>
</section>

<section class="dashboard-section">
    <div class="container">
        <!-- Streak + XP + Daily Goal -->
        <?php if (!empty($streak)): ?>
        <div class="streak-bar">
            <div class="streak-item">
                <div class="streak-icon">🔥</div>
                <div>
                    <strong class="streak-count"><?= $streak['current_streak'] ?></strong>
                    <small>ngày liên tiếp</small>
                </div>
            </div>
            <div class="streak-item">
                <div class="streak-icon">⭐</div>
                <div>
                    <strong>Lv.<?= $streak['level'] ?></strong>
                    <div class="xp-mini-bar"><div class="xp-mini-fill" style="width:<?= $streak['level_progress'] ?>%"></div></div>
                    <small><?= number_format($streak['total_xp']) ?> XP</small>
                </div>
            </div>
            <div class="streak-item">
                <div class="streak-icon">🎯</div>
                <div>
                    <strong><?= $streak['daily_xp_today'] ?>/<?= $streak['daily_goal'] ?> XP</strong>
                    <div class="xp-mini-bar"><div class="xp-mini-fill" style="width:<?= $streak['daily_progress'] ?>%; background:<?= $streak['daily_progress'] >= 100 ? 'var(--success)' : 'var(--primary)' ?>;"></div></div>
                    <small>Mục tiêu hôm nay <?= $streak['daily_progress'] >= 100 ? '✅' : '' ?></small>
                </div>
            </div>
            <div class="streak-item">
                <div class="streak-icon">🏆</div>
                <div>
                    <strong><?= $streak['longest_streak'] ?></strong>
                    <small>Streak cao nhất</small>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Overall Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #667eea, #764ba2);"><i class="fas fa-font"></i></div>
                <div class="stat-content">
                    <span class="stat-value"><?= $data['overall']['total_vocab_learned'] ?></span>
                    <span class="stat-title">Từ vựng đã học</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #f093fb, #f5576c);"><i class="fas fa-book"></i></div>
                <div class="stat-content">
                    <span class="stat-value"><?= $data['overall']['total_lessons_completed'] ?></span>
                    <span class="stat-title">Bài học hoàn thành</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #4facfe, #00f2fe);"><i class="fas fa-trophy"></i></div>
                <div class="stat-content">
                    <span class="stat-value"><?= $data['overall']['total_tests_passed'] ?></span>
                    <span class="stat-title">Bài test đã đạt</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #43e97b, #38f9d7);"><i class="fas fa-microphone"></i></div>
                <div class="stat-content">
                    <span class="stat-value"><?= $data['overall']['total_speaking_practiced'] ?></span>
                    <span class="stat-title">Lượt luyện nói</span>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="charts-grid">
            <div class="chart-card">
                <h3><i class="fas fa-chart-bar"></i> Điểm theo chủ đề</h3>
                <canvas id="topicScoreChart"></canvas>
            </div>
            <div class="chart-card">
                <h3><i class="fas fa-spider"></i> Phân bố kỹ năng</h3>
                <canvas id="skillRadarChart"></canvas>
            </div>
        </div>

        <!-- Badges Preview -->
        <div class="section-card" style="margin-bottom:1.5rem;">
            <h3><i class="fas fa-medal"></i> Huy hiệu của bạn · <a href="<?= BASE_URL ?>/profile" style="font-size:0.85rem;">Xem tất cả →</a></h3>
            <div class="badge-grid" style="margin-top:1rem; grid-template-columns:repeat(auto-fill, minmax(100px, 1fr));">
                <?php
                $badgeList = [
                    ['icon'=>'🌱','name'=>'Người mới','earned' => ($data['overall']['total_tests_passed'] ?? 0) >= 1],
                    ['icon'=>'📚','name'=>'Bookworm','earned' => count($data['topic_progress'] ?? []) >= 3],
                    ['icon'=>'⭐','name'=>'Star','earned' => ($data['overall']['total_tests_passed'] ?? 0) >= 10],
                    ['icon'=>'🗣️','name'=>'Speaker','earned' => ($data['overall']['total_speaking_practiced'] ?? 0) >= 10],
                    ['icon'=>'💎','name'=>'Pro','earned' => Middleware::isPro()],
                ];
                foreach ($badgeList as $b): ?>
                    <div class="badge-card <?= $b['earned'] ? 'earned' : 'locked' ?>" style="padding:0.8rem;">
                        <div class="badge-icon" style="font-size:1.8rem;"><?= $b['icon'] ?></div>
                        <div class="badge-name"><?= $b['name'] ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Topic Progress -->
        <div class="section-card">
            <h3><i class="fas fa-tasks"></i> Tiến độ theo chủ đề</h3>
            <?php if (empty($data['topic_progress'])): ?>
                <div class="empty-state">
                    <i class="fas fa-chart-line"></i>
                    <p>Chưa có dữ liệu. Hãy bắt đầu học để theo dõi tiến độ!</p>
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
                                <th>Tổng điểm</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data['topic_progress'] as $tp): ?>
                                <tr>
                                    <td>
                                        <a href="<?= BASE_URL ?>/topic/show/<?= $tp['topic_id'] ?>">
                                            <?= htmlspecialchars($tp['topic_name']) ?>
                                        </a>
                                    </td>
                                    <td><span class="topic-level level-<?= $tp['level'] ?>"><?= ucfirst($tp['level']) ?></span></td>
                                    <td>
                                        <div class="mini-progress">
                                            <div class="mini-bar" style="width: <?= $tp['total_vocab'] > 0 ? ($tp['vocab_learned'] / $tp['total_vocab'] * 100) : 0 ?>%"></div>
                                        </div>
                                        <small><?= $tp['vocab_learned'] ?>/<?= $tp['total_vocab'] ?></small>
                                    </td>
                                    <td><?= $tp['lessons_completed'] ?>/<?= $tp['total_lessons'] ?></td>
                                    <td><?= $tp['tests_passed'] ?>/<?= $tp['total_tests'] ?></td>
                                    <td><?= $tp['speaking_practiced'] ?>/<?= $tp['total_speaking'] ?></td>
                                    <td><strong><?= $tp['total_score'] ?></strong></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- Recent Activities - 2 columns -->
        <div class="activities-grid">
            <!-- Recent Tests -->
            <div class="section-card">
                <h3><i class="fas fa-clipboard-check"></i> Bài test gần đây</h3>
                <?php if (empty($data['recent_tests'])): ?>
                    <p class="empty-text">Chưa có bài test nào.</p>
                <?php else: ?>
                    <div class="activity-list">
                        <?php foreach (array_slice($data['recent_tests'], 0, 5) as $t): ?>
                            <div class="activity-item">
                                <div class="activity-icon type-<?= $t['test_type'] ?>">
                                    <i class="fas fa-<?= $t['test_type'] === 'quiz' ? 'question-circle' : ($t['test_type'] === 'listening' ? 'headphones' : 'book-reader') ?>"></i>
                                </div>
                                <div class="activity-info">
                                    <strong><?= htmlspecialchars($t['test_title']) ?></strong>
                                    <small><?= $t['topic_name'] ?> • <?= date('d/m/Y', strtotime($t['completed_at'])) ?></small>
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

            <!-- Recent Speaking -->
            <div class="section-card">
                <h3><i class="fas fa-microphone"></i> Speaking gần đây</h3>
                <?php if (empty($data['recent_speaking'])): ?>
                    <p class="empty-text">Chưa có lượt luyện nói nào.</p>
                <?php else: ?>
                    <div class="activity-list">
                        <?php foreach (array_slice($data['recent_speaking'], 0, 5) as $s): ?>
                            <div class="activity-item">
                                <div class="activity-icon" style="background: linear-gradient(135deg, #43e97b, #38f9d7);">
                                    <i class="fas fa-microphone"></i>
                                </div>
                                <div class="activity-info">
                                    <strong><?= htmlspecialchars(mb_substr($s['prompt_text'], 0, 50)) ?>...</strong>
                                    <small><?= $s['topic_name'] ?> • <?= date('d/m/Y', strtotime($s['created_at'])) ?></small>
                                </div>
                                <div class="activity-score">
                                    <span class="<?= $s['overall_score'] >= 60 ? 'text-success' : 'text-danger' ?>"><?= $s['overall_score'] ?></span>
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
// Dữ liệu cho charts
const chartData = <?= json_encode([
    'topics' => array_column($data['topic_progress'], 'topic_name'),
    'scores' => array_column($data['topic_progress'], 'total_score'),
    'vocab'  => array_column($data['topic_progress'], 'vocab_learned'),
    'overall' => $data['overall']
]) ?>;

if (typeof initDashboardCharts === 'function') {
    initDashboardCharts(chartData);
}
</script>
