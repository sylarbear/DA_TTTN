<?php
$adminActive = 'dashboard';
$adminTitle = 'Admin Dashboard';
$adminSubtitle = 'Theo dõi học viên, nội dung, đơn nâng cấp và hỗ trợ hệ thống.';
require APP_PATH . '/views/admin/_nav.php';
?>

<section class="dashboard-section">
    <div class="container">
        <!-- Stats Cards -->
        <div class="stats-grid" style="grid-template-columns:repeat(5, 1fr);">
            <div class="stat-card">
                <div class="stat-icon" style="background:linear-gradient(135deg, #5B6CFF, #8B5CF6);"><i class="fas fa-users"></i></div>
                <div class="stat-content">
                    <span class="stat-value"><?= $stats['total_users'] ?></span>
                    <span class="stat-title">Học viên</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:linear-gradient(135deg, #FFD700, #FFA500);"><i class="fas fa-crown"></i></div>
                <div class="stat-content">
                    <span class="stat-value"><?= $stats['pro_users'] ?></span>
                    <span class="stat-title">Pro Members</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:linear-gradient(135deg, #10B981, #34D399);"><i class="fas fa-book-open"></i></div>
                <div class="stat-content">
                    <span class="stat-value"><?= $stats['total_topics'] ?></span>
                    <span class="stat-title">Chủ đề</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:linear-gradient(135deg, #06B6D4, #22D3EE);"><i class="fas fa-clipboard-check"></i></div>
                <div class="stat-content">
                    <span class="stat-value"><?= $stats['total_attempts'] ?></span>
                    <span class="stat-title">Lượt làm bài</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:linear-gradient(135deg, #ef4444, #f97316);"><i class="fas fa-headset"></i></div>
                <div class="stat-content">
                    <span class="stat-value"><?= $stats['pending_tickets'] ?></span>
                    <span class="stat-title">Tickets chờ</span>
                </div>
            </div>
        </div>

        <!-- Charts Grid -->
        <div class="charts-grid" style="display:grid; grid-template-columns:repeat(2,1fr); gap:1.5rem; margin-bottom:2rem;">
            <!-- Chart 1: User Growth -->
            <div class="section-card">
                <h3><i class="fas fa-chart-line" style="color:#6366f1;"></i> Tăng trưởng User (7 ngày)</h3>
                <div style="position:relative; height:250px;">
                    <canvas id="userGrowthChart"></canvas>
                </div>
            </div>
            <!-- Chart 2: Score Distribution -->
            <div class="section-card">
                <h3><i class="fas fa-chart-bar" style="color:#f59e0b;"></i> Phân bố điểm Test</h3>
                <div style="position:relative; height:250px;">
                    <canvas id="scoreDistChart"></canvas>
                </div>
            </div>
            <!-- Chart 3: Orders by Month -->
            <div class="section-card">
                <h3><i class="fas fa-chart-area" style="color:#10b981;"></i> Đơn nâng cấp (6 tháng)</h3>
                <div style="position:relative; height:250px;">
                    <canvas id="ordersChart"></canvas>
                </div>
            </div>
            <!-- Chart 4: Free vs Pro -->
            <div class="section-card">
                <h3><i class="fas fa-chart-pie" style="color:#8b5cf6;"></i> Tỷ lệ Free / Pro</h3>
                <div style="position:relative; height:250px;">
                    <canvas id="membershipChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Tables Grid -->
        <div class="charts-grid">
            <!-- Recent Users -->
            <div class="section-card">
                <h3><i class="fas fa-user-plus"></i> Học viên mới đăng ký</h3>
                <div class="progress-table">
                    <table>
                        <thead><tr><th>Username</th><th>Họ tên</th><th>Email</th><th>Membership</th><th>Ngày ĐK</th></tr></thead>
                        <tbody>
                        <?php foreach ($stats['recent_users'] as $u): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($u['username']) ?></strong></td>
                                <td><?= htmlspecialchars($u['full_name'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($u['email']) ?></td>
                                <td><span class="answer-badge <?= $u['membership'] === 'pro' ? 'correct' : '' ?>"><?= strtoupper($u['membership']) ?></span></td>
                                <td><?= date('d/m/Y H:i', strtotime($u['created_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="section-card">
                <h3><i class="fas fa-chart-pie"></i> Thống kê nhanh</h3>
                <div style="display:flex; flex-direction:column; gap:1rem;">
                    <div class="progress-item">
                        <div class="progress-icon"><i class="fas fa-clipboard-list"></i></div>
                        <div class="progress-info"><span><?= $stats['total_tests'] ?></span><small>Bài test</small></div>
                    </div>
                    <div class="progress-item">
                        <div class="progress-icon"><i class="fas fa-question"></i></div>
                        <div class="progress-info"><span><?= $stats['total_questions'] ?></span><small>Câu hỏi</small></div>
                    </div>
                    <div class="progress-item">
                        <div class="progress-icon"><i class="fas fa-key"></i></div>
                        <div class="progress-info"><span><?= $stats['unused_codes'] ?></span><small>Mã chưa dùng</small></div>
                    </div>
                    <div class="progress-item">
                        <div class="progress-icon"><i class="fas fa-star"></i></div>
                        <div class="progress-info"><span><?= $stats['total_reviews'] ?></span><small>Đánh giá bài học</small></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Test Attempts -->
        <div class="section-card">
            <h3><i class="fas fa-history"></i> Lượt làm bài gần đây</h3>
            <div class="progress-table">
                <table>
                    <thead><tr><th>User</th><th>Bài test</th><th>Điểm</th><th>Thời gian</th></tr></thead>
                    <tbody>
                    <?php foreach ($stats['recent_attempts'] as $a): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($a['username']) ?></strong></td>
                            <td><?= htmlspecialchars($a['test_title']) ?></td>
                            <td><span class="answer-badge <?= $a['percentage'] >= 50 ? 'correct' : 'incorrect' ?>"><?= $a['percentage'] ?>%</span></td>
                            <td><?= date('d/m/Y H:i', strtotime($a['completed_at'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<script>
// Load chart data from API
fetch('<?= BASE_URL ?>/admin/chartData', { credentials: 'same-origin' })
    .then(r => r.json())
    .then(data => {
        // Chart 1: User Growth (Line)
        new Chart(document.getElementById('userGrowthChart'), {
            type: 'line',
            data: {
                labels: data.userGrowth.map(d => d.label),
                datasets: [{
                    label: 'User mới',
                    data: data.userGrowth.map(d => d.count),
                    borderColor: '#6366f1',
                    backgroundColor: 'rgba(99,102,241,0.1)',
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#6366f1',
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
            }
        });

        // Chart 2: Score Distribution (Bar)
        const sd = data.scoreDistribution;
        new Chart(document.getElementById('scoreDistChart'), {
            type: 'bar',
            data: {
                labels: ['0-20%', '21-40%', '41-60%', '61-80%', '81-100%'],
                datasets: [{
                    label: 'Số lượt',
                    data: [sd.r_0_20 || 0, sd.r_21_40 || 0, sd.r_41_60 || 0, sd.r_61_80 || 0, sd.r_81_100 || 0],
                    backgroundColor: ['#ef4444', '#f59e0b', '#eab308', '#22c55e', '#10b981'],
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
            }
        });

        // Chart 3: Orders by Month (Bar)
        new Chart(document.getElementById('ordersChart'), {
            type: 'bar',
            data: {
                labels: data.ordersByMonth.map(d => d.label),
                datasets: [{
                    label: 'Đơn nâng cấp',
                    data: data.ordersByMonth.map(d => d.count),
                    backgroundColor: 'rgba(16,185,129,0.7)',
                    borderColor: '#10b981',
                    borderWidth: 1,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
            }
        });

        // Chart 4: Free vs Pro (Doughnut)
        new Chart(document.getElementById('membershipChart'), {
            type: 'doughnut',
            data: {
                labels: ['Free', 'Pro'],
                datasets: [{
                    data: [data.membershipRatio.free, data.membershipRatio.pro],
                    backgroundColor: ['#94a3b8', '#f59e0b'],
                    borderWidth: 0,
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            label: function(ctx) {
                                const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                const pct = total > 0 ? Math.round(ctx.raw / total * 100) : 0;
                                return ctx.label + ': ' + ctx.raw + ' (' + pct + '%)';
                            }
                        }
                    }
                }
            }
        });
    })
    .catch(err => console.error('Chart load error:', err));
</script>

