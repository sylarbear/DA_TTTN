<!-- Test Result Page -->
<section class="page-header">
    <div class="container">
        <nav class="breadcrumb">
            <a href="<?= BASE_URL ?>/test">Bài test</a>
            <span>/</span>
            <span>Kết quả</span>
        </nav>
        <h1><?= htmlspecialchars($result['test_title']) ?></h1>
    </div>
</section>

<section class="result-section">
    <div class="container">
        <!-- Score Overview -->
        <?php $percentage = $result['total_points'] > 0 ? round(($result['score'] / $result['total_points']) * 100) : 0; ?>
        <?php $passed = $percentage >= $result['pass_score']; ?>
        
        <div class="result-overview <?= $passed ? 'passed' : 'failed' ?>">
            <div class="result-circle">
                <div class="circle-progress" style="--progress: <?= $percentage ?>%;">
                    <span class="circle-value"><?= $percentage ?>%</span>
                </div>
            </div>
            <div class="result-info">
                <h2><?= $passed ? '🎉 Chúc mừng! Bạn đã đạt!' : '😔 Chưa đạt yêu cầu' ?></h2>
                <div class="result-stats">
                    <div class="result-stat">
                        <span class="stat-value"><?= $result['score'] ?>/<?= $result['total_points'] ?></span>
                        <span class="stat-label">Điểm</span>
                    </div>
                    <div class="result-stat">
                        <span class="stat-value"><?= gmdate("i:s", $result['time_spent'] ?? 0) ?></span>
                        <span class="stat-label">Thời gian</span>
                    </div>
                    <div class="result-stat">
                        <span class="stat-value"><?= $result['pass_score'] ?>%</span>
                        <span class="stat-label">Điểm cần đạt</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Answers -->
        <h2 class="section-title"><i class="fas fa-list-check"></i> Chi tiết bài làm</h2>
        <div class="answers-list">
            <?php foreach ($details as $i => $d): ?>
                <div class="answer-card <?= $d['is_correct'] ? 'correct' : 'incorrect' ?>" id="answer-<?= $i + 1 ?>">
                    <div class="answer-header">
                        <span class="answer-number">Câu <?= $i + 1 ?></span>
                        <?php if ($d['is_correct']): ?>
                            <span class="answer-badge correct"><i class="fas fa-check"></i> Đúng</span>
                        <?php else: ?>
                            <span class="answer-badge incorrect"><i class="fas fa-times"></i> Sai</span>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (!empty($d['passage'])): ?>
                        <div class="answer-passage">
                            <small><?= htmlspecialchars(mb_substr($d['passage'], 0, 150)) ?>...</small>
                        </div>
                    <?php endif; ?>

                    <p class="answer-question"><?= htmlspecialchars($d['question_text']) ?></p>
                    
                    <div class="answer-compare">
                        <div class="your-answer">
                            <strong>Bạn chọn:</strong> 
                            <span class="<?= $d['is_correct'] ? 'text-success' : 'text-danger' ?>">
                                <?= htmlspecialchars($d['user_answer'] ?: '(Không trả lời)') ?>
                            </span>
                        </div>
                        <?php if (!$d['is_correct']): ?>
                            <div class="correct-answer">
                                <strong>Đáp án đúng:</strong> 
                                <span class="text-success"><?= htmlspecialchars($d['correct_answer']) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="result-actions">
            <a href="<?= BASE_URL ?>/test/take/<?= $result['test_id'] ?>" class="btn btn-primary">
                <i class="fas fa-redo"></i> Làm lại
            </a>
            <a href="<?= BASE_URL ?>/test" class="btn btn-outline">
                <i class="fas fa-list"></i> Danh sách bài test
            </a>
        </div>
    </div>
</section>
