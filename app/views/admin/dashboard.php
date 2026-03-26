<!-- Admin Dashboard -->
<section class="page-header">
    <div class="container">
        <h1><i class="fas fa-shield-alt"></i> Admin Dashboard</h1>
        <p>Quản lý hệ thống English Learning</p>
    </div>
</section>

<!-- Admin Nav -->
<section style="padding:1rem 0; border-bottom:1px solid var(--border-color); background:white;">
    <div class="container">
        <div class="admin-nav">
            <a href="<?= BASE_URL ?>/admin" class="admin-nav-item active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="<?= BASE_URL ?>/admin/users" class="admin-nav-item"><i class="fas fa-users"></i> Users</a>
            <a href="<?= BASE_URL ?>/admin/topics" class="admin-nav-item"><i class="fas fa-book"></i> Chủ đề</a>
            <a href="<?= BASE_URL ?>/admin/questions" class="admin-nav-item"><i class="fas fa-question-circle"></i> Câu hỏi</a>
            <a href="<?= BASE_URL ?>/admin/codes" class="admin-nav-item"><i class="fas fa-key"></i> Mã kích hoạt</a>
            <a href="<?= BASE_URL ?>/admin/settings" class="admin-nav-item"><i class="fas fa-cog"></i> Cài đặt</a>
        </div>
    </div>
</section>

<section class="dashboard-section">
    <div class="container">
        <!-- Stats Cards -->
        <div class="stats-grid" style="grid-template-columns:repeat(4, 1fr);">
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
        </div>

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
                        <div class="progress-icon"><i class="fas fa-check-circle"></i></div>
                        <div class="progress-info"><span><?= $stats['used_codes'] ?></span><small>Mã đã dùng</small></div>
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
                            <td><span class="answer-badge <?= $a['score'] >= 50 ? 'correct' : 'incorrect' ?>"><?= $a['score'] ?>%</span></td>
                            <td><?= date('d/m/Y H:i', strtotime($a['completed_at'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
