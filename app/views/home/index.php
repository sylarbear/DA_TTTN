<?php
$isLoggedIn = Middleware::isLoggedIn();
$topicCount = count($topics);
$totalVocab = array_sum(array_map(function ($topic) {
    return (int)($topic['vocab_count'] ?? 0);
}, $topics));
$totalLessons = array_sum(array_map(function ($topic) {
    return (int)($topic['lesson_count'] ?? 0);
}, $topics));
$totalTests = array_sum(array_map(function ($topic) {
    return (int)($topic['test_count'] ?? 0);
}, $topics));
$featuredTopic = $topics[0] ?? null;

$topicIcons = [
    'travel' => 'fa-plane-departure',
    'food' => 'fa-utensils',
    'education' => 'fa-graduation-cap',
    'technology' => 'fa-microchip',
    'health' => 'fa-heart-pulse',
    'business' => 'fa-briefcase',
    'default' => 'fa-layer-group',
];

$topicAccent = [
    'beginner' => 'topic-accent-green',
    'intermediate' => 'topic-accent-blue',
    'advanced' => 'topic-accent-purple',
];
?>

<section class="busuu-hero">
    <div class="busuu-shell">
        <div class="busuu-hero-copy">
            <span class="busuu-label">English learning platform</span>
            <h1>New English, new confidence, new you.</h1>
            <p>EngPath đưa từ vựng, bài học, quiz và luyện nói AI vào một lộ trình học rõ ràng để người học biết hôm nay cần làm gì và học tiếp ở đâu.</p>

            <div class="busuu-hero-actions">
                <?php if (!$isLoggedIn): ?>
                    <a href="<?= BASE_URL ?>/auth/register" class="busuu-primary-btn">Learn for free</a>
                    <a href="<?= BASE_URL ?>/topic" class="busuu-secondary-btn">Explore courses</a>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>/dashboard" class="busuu-primary-btn">Continue learning</a>
                    <a href="<?= BASE_URL ?>/speaking" class="busuu-secondary-btn">Practice speaking</a>
                <?php endif; ?>
            </div>
        </div>

        <div class="busuu-visual" aria-label="EngPath learning preview">
            <div class="phone-mockup">
                <div class="phone-top">
                    <span></span>
                    <strong>Today</strong>
                    <i class="fas fa-bell"></i>
                </div>
                <div class="lesson-hero-card">
                    <small>Next lesson</small>
                    <h3><?= $featuredTopic ? htmlspecialchars($featuredTopic['name']) : 'Everyday English' ?></h3>
                    <div class="lesson-progress"><span style="width:76%"></span></div>
                </div>
                <div class="app-task active">
                    <i class="fas fa-volume-up"></i>
                    <div><strong>Vocabulary review</strong><small>12 new words</small></div>
                </div>
                <div class="app-task">
                    <i class="fas fa-microphone-lines"></i>
                    <div><strong>Speaking AI</strong><small>Instant feedback</small></div>
                </div>
                <div class="score-row">
                    <div><b>86</b><span>Pronunciation</span></div>
                    <div><b>91</b><span>Accuracy</span></div>
                </div>
            </div>

            <div class="floating-result-card">
                <i class="fas fa-check"></i>
                <div>
                    <strong>Great progress</strong>
                    <span><?= $totalVocab ?: 100 ?>+ words available</span>
                </div>
            </div>
        </div>
    </div>

    <div class="busuu-goal-panel">
        <span>I want to improve</span>
        <a href="<?= BASE_URL ?>/topic"><i class="fas fa-comments"></i> Communication</a>
        <a href="<?= BASE_URL ?>/topic"><i class="fas fa-font"></i> Vocabulary</a>
        <a href="<?= BASE_URL ?>/grammar"><i class="fas fa-graduation-cap"></i> Grammar</a>
        <a href="<?= BASE_URL ?>/speaking"><i class="fas fa-microphone"></i> Speaking</a>
    </div>
</section>

<section class="busuu-proof">
    <div class="container proof-grid">
        <div><strong><?= $topicCount ?: 6 ?>+</strong><span>learning topics</span></div>
        <div><strong><?= $totalLessons ?: 20 ?>+</strong><span>compact lessons</span></div>
        <div><strong><?= $totalVocab ?: 100 ?>+</strong><span>vocabulary items</span></div>
        <div><strong><?= $totalTests ?: 10 ?>+</strong><span>tests and quizzes</span></div>
    </div>
</section>

<section class="busuu-feature-section">
    <div class="container">
        <div class="busuu-section-heading">
            <span>Why learn with EngPath?</span>
            <h2>Thiết kế mới tập trung vào hành trình học, không chỉ là danh sách chức năng.</h2>
        </div>

        <div class="busuu-feature-row">
            <div class="feature-media card-stack-media">
                <div class="stack-card main">
                    <small>Study plan</small>
                    <strong>Travel English</strong>
                    <span>Flashcard → Lesson → Quiz → Speaking</span>
                </div>
                <div class="stack-card second"><i class="fas fa-clone"></i> Flashcards</div>
                <div class="stack-card third"><i class="fas fa-chart-line"></i> Progress 76%</div>
            </div>
            <div class="feature-copy">
                <span class="busuu-label">Courses created for progress</span>
                <h3>Học theo từng chặng nhỏ, dễ hiểu và dễ quay lại.</h3>
                <p>Thay vì để người dùng tự tìm chức năng, EngPath đưa người học đi theo một vòng học rõ ràng: chọn chủ đề, học từ vựng, đọc bài, làm test và luyện speaking.</p>
                <a href="<?= BASE_URL ?>/topic" class="busuu-text-link">View course catalog <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>

        <div class="busuu-feature-row reverse">
            <div class="feature-media speaking-media">
                <div class="speaking-avatar"><i class="fas fa-microphone-lines"></i></div>
                <div class="speaking-bars"><span></span><span></span><span></span><span></span><span></span></div>
                <div class="feedback-chips">
                    <span>Pronunciation 86</span>
                    <span>Fluency 78</span>
                    <span>Accuracy 91</span>
                </div>
            </div>
            <div class="feature-copy">
                <span class="busuu-label">Immersive speaking practice</span>
                <h3>Luyện nói AI là điểm khác biệt cần được nhìn thấy ngay.</h3>
                <p>Trang speaking được đặt nổi bật vì đây là phần giúp đồ án trông giống một nền tảng học ngoại ngữ thật, không phải chỉ là website quản lý bài học.</p>
                <a href="<?= BASE_URL ?>/speaking" class="busuu-text-link">Start speaking practice <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
    </div>
</section>

<section class="busuu-course-section">
    <div class="container">
        <div class="busuu-section-heading compact">
            <span>Start learning</span>
            <h2>Choose a course for your goal</h2>
        </div>

        <div class="busuu-course-grid">
            <?php foreach (array_slice($topics, 0, 6) as $topic): ?>
                <?php
                    $topicName = strtolower($topic['name'] ?? '');
                $slug = strtolower($topic['slug'] ?? '');
                $icon = $topicIcons['default'];
                foreach ($topicIcons as $keyword => $iconClass) {
                    if ($keyword !== 'default' && (strpos($topicName, $keyword) !== false || strpos($slug, $keyword) !== false)) {
                        $icon = $iconClass;
                        break;
                    }
                }
                $level = $topic['level'] ?? 'beginner';
                $accentClass = $topicAccent[$level] ?? 'topic-accent-blue';
                $description = trim((string)($topic['description'] ?? ''));
                ?>
                <a href="<?= BASE_URL ?>/topic/show/<?= $topic['id'] ?>" class="busuu-course-card <?= $accentClass ?>">
                    <div class="course-icon"><i class="fas <?= $icon ?>"></i></div>
                    <span><?= ucfirst(htmlspecialchars($level)) ?></span>
                    <h3><?= htmlspecialchars($topic['name']) ?></h3>
                    <p><?= htmlspecialchars(mb_substr($description, 0, 92)) ?><?= mb_strlen($description) > 92 ? '...' : '' ?></p>
                    <div class="course-stats">
                        <b><?= (int)$topic['vocab_count'] ?> từ</b>
                        <b><?= (int)$topic['lesson_count'] ?> bài</b>
                        <b><?= (int)$topic['test_count'] ?> test</b>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>

        <div class="topics-action">
            <a href="<?= BASE_URL ?>/topic" class="busuu-primary-btn">View all courses</a>
        </div>
    </div>
</section>

<section class="busuu-final-cta">
    <div class="container">
        <div class="final-cta-box">
            <span>Ready to learn?</span>
            <h2>Build your English habit with EngPath.</h2>
            <p>Bắt đầu bằng một chủ đề nhỏ hôm nay, sau đó theo dõi tiến độ trong dashboard và luyện nói với AI.</p>
            <?php if (!$isLoggedIn): ?>
                <a href="<?= BASE_URL ?>/auth/register" class="busuu-primary-btn">Get started</a>
            <?php else: ?>
                <a href="<?= BASE_URL ?>/dashboard" class="busuu-primary-btn">Go to dashboard</a>
            <?php endif; ?>
        </div>
    </div>
</section>
