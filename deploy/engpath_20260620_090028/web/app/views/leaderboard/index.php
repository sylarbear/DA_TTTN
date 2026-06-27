<!-- Leaderboard Page -->
<section class="page-header">
    <div class="container">
        <h1><i class="fas fa-trophy"></i> Bảng xếp hạng</h1>
        <p>Top học viên xuất sắc nhất English Learning</p>
    </div>
</section>

<section class="dashboard-section">
    <div class="container">
        <?php if (count($leaders) >= 3): ?>
        <!-- Podium -->
        <div class="leaderboard-podium">
            <!-- 2nd Place -->
            <div class="podium-item silver" style="margin-top:2rem;">
                <div class="podium-rank">🥈</div>
                <div class="podium-avatar"><?= mb_substr($leaders[1]['full_name'] ?? $leaders[1]['username'], 0, 1) ?></div>
                <div class="podium-name"><?= htmlspecialchars($leaders[1]['full_name'] ?? $leaders[1]['username']) ?></div>
                <div class="podium-score"><?= number_format($leaders[1]['total_score']) ?> pts</div>
                <small style="color:var(--text-muted);"><?= $leaders[1]['total_tests'] ?> bài · TB <?= $leaders[1]['avg_score'] ?>%</small>
            </div>
            <!-- 1st Place -->
            <div class="podium-item gold">
                <div class="podium-rank">🥇</div>
                <div class="podium-avatar" style="width:70px;height:70px;font-size:1.5rem;"><?= mb_substr($leaders[0]['full_name'] ?? $leaders[0]['username'], 0, 1) ?></div>
                <div class="podium-name"><?= htmlspecialchars($leaders[0]['full_name'] ?? $leaders[0]['username']) ?></div>
                <div class="podium-score" style="font-size:1.5rem;"><?= number_format($leaders[0]['total_score']) ?> pts</div>
                <small style="color:var(--text-muted);"><?= $leaders[0]['total_tests'] ?> bài · TB <?= $leaders[0]['avg_score'] ?>%</small>
            </div>
            <!-- 3rd Place -->
            <div class="podium-item bronze" style="margin-top:3rem;">
                <div class="podium-rank">🥉</div>
                <div class="podium-avatar"><?= mb_substr($leaders[2]['full_name'] ?? $leaders[2]['username'], 0, 1) ?></div>
                <div class="podium-name"><?= htmlspecialchars($leaders[2]['full_name'] ?? $leaders[2]['username']) ?></div>
                <div class="podium-score"><?= number_format($leaders[2]['total_score']) ?> pts</div>
                <small style="color:var(--text-muted);"><?= $leaders[2]['total_tests'] ?> bài · TB <?= $leaders[2]['avg_score'] ?>%</small>
            </div>
        </div>
        <?php endif; ?>

        <!-- Full Ranking -->
        <div class="leaderboard-table">
            <?php foreach ($leaders as $i => $l): ?>
                <div class="lb-row <?= ($user && $l['id'] == $user['id']) ? 'current-user' : '' ?>">
                    <div class="lb-rank"><?= $i + 1 ?></div>
                    <div class="lb-avatar"><?= mb_substr($l['full_name'] ?? $l['username'], 0, 1) ?></div>
                    <div class="lb-name">
                        <?= htmlspecialchars($l['full_name'] ?? $l['username']) ?>
                        <?php if ($l['membership'] === 'pro'): ?><span class="nav-pro-badge">PRO</span><?php endif; ?>
                        <?php if ($user && $l['id'] == $user['id']): ?><span style="color:var(--primary); font-size:0.8rem;">(Bạn)</span><?php endif; ?>
                    </div>
                    <div class="lb-stats">
                        <span><i class="fas fa-star" style="color:var(--accent-orange);"></i> <?= number_format($l['total_score']) ?> pts</span>
                        <span><i class="fas fa-clipboard-check" style="color:var(--primary);"></i> <?= $l['total_tests'] ?> bài</span>
                        <span><i class="fas fa-chart-line" style="color:var(--success);"></i> TB <?= $l['avg_score'] ?>%</span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if ($myRank === null): ?>
            <div class="section-card" style="text-align:center; margin-top:1.5rem; padding:2rem;">
                <p style="color:var(--text-secondary);">📝 Bạn chưa có trên bảng xếp hạng. Hoàn thành bài test để xuất hiện tại đây!</p>
                <a href="<?= BASE_URL ?>/test" class="btn btn-primary" style="margin-top:1rem;"><i class="fas fa-play"></i> Làm bài test ngay</a>
            </div>
        <?php endif; ?>
    </div>
</section>
