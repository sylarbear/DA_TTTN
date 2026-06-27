<!-- Search Results Page -->
<section class="page-header">
    <div class="container">
        <h1><i class="fas fa-search"></i> Tìm kiếm</h1>
        <p>Tìm chủ đề, từ vựng, bài test, bài học và ngữ pháp</p>
    </div>
</section>

<section class="dashboard-section">
    <div class="container">
        <!-- Search Bar -->
        <div class="section-card" style="margin-bottom:1.5rem;">
            <form action="<?= BASE_URL ?>/topic/search" method="GET" class="search-form" id="searchForm">
                <div style="display:flex; gap:0.75rem; align-items:center;">
                    <div style="flex:1; position:relative;">
                        <i class="fas fa-search" style="position:absolute; left:14px; top:50%; transform:translateY(-50%); color:var(--text-muted);"></i>
                        <input type="text" name="q" id="searchInput" value="<?= htmlspecialchars($keyword) ?>" 
                               placeholder="Nhập từ khóa tìm kiếm (VD: present, daily, food...)" 
                               class="form-input" style="padding-left:40px; font-size:1rem; height:48px;"
                               autocomplete="off" minlength="2">
                    </div>
                    <button type="submit" class="btn btn-primary" style="height:48px; padding:0 24px;">
                        <i class="fas fa-search"></i> Tìm kiếm
                    </button>
                </div>
            </form>
        </div>

        <?php if (!empty($keyword)): ?>
            <!-- Results Summary -->
            <div style="margin-bottom:1rem; color:var(--text-secondary);">
                <i class="fas fa-info-circle"></i> 
                Tìm thấy <strong><?= $totalResults ?></strong> kết quả cho "<strong><?= htmlspecialchars($keyword) ?></strong>"
            </div>

            <?php if ($totalResults === 0): ?>
                <div class="section-card" style="text-align:center; padding:3rem;">
                    <i class="fas fa-search fa-3x" style="color:var(--text-muted); margin-bottom:1rem;"></i>
                    <h3>Không tìm thấy kết quả</h3>
                    <p style="color:var(--text-muted);">Thử tìm với từ khóa khác hoặc ngắn hơn.</p>
                </div>
            <?php endif; ?>

            <!-- Topics Results -->
            <?php if (!empty($results['topics'])): ?>
            <div class="section-card" style="margin-bottom:1rem;">
                <h3><i class="fas fa-book-open" style="color:var(--primary);"></i> Chủ đề (<?= count($results['topics']) ?>)</h3>
                <div class="topics-grid" style="margin-top:1rem;">
                    <?php foreach ($results['topics'] as $t): ?>
                        <a href="<?= BASE_URL ?>/topic/show/<?= $t['id'] ?>" class="topic-card">
                            <div class="topic-card-header">
                                <span class="topic-level level-<?= $t['level'] ?>"><?= ucfirst($t['level']) ?></span>
                            </div>
                            <div class="topic-card-body">
                                <h3><?= htmlspecialchars($t['title']) ?></h3>
                                <p><?= htmlspecialchars(mb_substr($t['description'], 0, 80)) ?>...</p>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Vocabulary Results -->
            <?php if (!empty($results['vocabularies'])): ?>
            <div class="section-card" style="margin-bottom:1rem;">
                <h3><i class="fas fa-font" style="color:#667eea;"></i> Từ vựng (<?= count($results['vocabularies']) ?>)</h3>
                <div class="activity-list" style="margin-top:1rem;">
                    <?php foreach ($results['vocabularies'] as $v): ?>
                        <a href="<?= BASE_URL ?>/topic/show/<?= $v['topic_id'] ?>" class="activity-item" style="text-decoration:none; color:inherit;">
                            <div class="activity-icon" style="background:linear-gradient(135deg, #667eea, #764ba2);">
                                <i class="fas fa-font"></i>
                            </div>
                            <div class="activity-info">
                                <strong><?= htmlspecialchars($v['title']) ?></strong>
                                <small><?= htmlspecialchars($v['description'] ?? '') ?> · <?= htmlspecialchars($v['topic_name']) ?></small>
                            </div>
                            <i class="fas fa-chevron-right" style="color:var(--text-muted);"></i>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Test Results -->
            <?php if (!empty($results['tests'])): ?>
            <div class="section-card" style="margin-bottom:1rem;">
                <h3><i class="fas fa-clipboard-check" style="color:#f5576c;"></i> Bài test (<?= count($results['tests']) ?>)</h3>
                <div class="activity-list" style="margin-top:1rem;">
                    <?php foreach ($results['tests'] as $t): ?>
                        <a href="<?= BASE_URL ?>/test/take/<?= $t['id'] ?>" class="activity-item" style="text-decoration:none; color:inherit;">
                            <div class="activity-icon" style="background:linear-gradient(135deg, #f093fb, #f5576c);">
                                <i class="fas fa-clipboard-check"></i>
                            </div>
                            <div class="activity-info">
                                <strong><?= htmlspecialchars($t['title']) ?></strong>
                                <small><?= ucfirst(htmlspecialchars($t['description'])) ?> · <?= htmlspecialchars($t['topic_name']) ?></small>
                            </div>
                            <i class="fas fa-chevron-right" style="color:var(--text-muted);"></i>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Lesson Results -->
            <?php if (!empty($results['lessons'])): ?>
            <div class="section-card" style="margin-bottom:1rem;">
                <h3><i class="fas fa-book" style="color:#43e97b;"></i> Bài học (<?= count($results['lessons']) ?>)</h3>
                <div class="activity-list" style="margin-top:1rem;">
                    <?php foreach ($results['lessons'] as $l): ?>
                        <a href="<?= BASE_URL ?>/lesson/show/<?= $l['id'] ?>" class="activity-item" style="text-decoration:none; color:inherit;">
                            <div class="activity-icon" style="background:linear-gradient(135deg, #43e97b, #38f9d7);">
                                <i class="fas fa-book"></i>
                            </div>
                            <div class="activity-info">
                                <strong><?= htmlspecialchars($l['title']) ?></strong>
                                <small><?= htmlspecialchars($l['topic_name']) ?></small>
                            </div>
                            <i class="fas fa-chevron-right" style="color:var(--text-muted);"></i>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Grammar Results -->
            <?php if (!empty($results['grammar'])): ?>
            <div class="section-card" style="margin-bottom:1rem;">
                <h3><i class="fas fa-graduation-cap" style="color:#f59e0b;"></i> Ngữ pháp (<?= count($results['grammar']) ?>)</h3>
                <div class="activity-list" style="margin-top:1rem;">
                    <?php foreach ($results['grammar'] as $g): ?>
                        <a href="<?= BASE_URL ?>/grammar/show/<?= $g['id'] ?>" class="activity-item" style="text-decoration:none; color:inherit;">
                            <div class="activity-icon" style="background:linear-gradient(135deg, #f59e0b, #fbbf24);">
                                <i class="fas fa-graduation-cap"></i>
                            </div>
                            <div class="activity-info">
                                <strong><?= htmlspecialchars($g['title']) ?></strong>
                                <small><?= ucfirst(htmlspecialchars($g['level'] ?? '')) ?></small>
                            </div>
                            <i class="fas fa-chevron-right" style="color:var(--text-muted);"></i>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

        <?php else: ?>
            <div class="section-card" style="text-align:center; padding:3rem;">
                <i class="fas fa-search fa-3x" style="color:var(--primary); margin-bottom:1rem;"></i>
                <h3>Tìm kiếm nội dung</h3>
                <p style="color:var(--text-muted);">Nhập từ khóa để tìm chủ đề, từ vựng, bài test, bài học hoặc ngữ pháp.</p>
            </div>
        <?php endif; ?>
    </div>
</section>
