<?php
$isLoggedIn = Middleware::isLoggedIn();
$topicCount = count($topics);
$totalVocab = array_sum(array_map(function ($topic) { return (int) ($topic['vocab_count'] ?? 0); }, $topics));
$totalLessons = array_sum(array_map(function ($topic) { return (int) ($topic['lesson_count'] ?? 0); }, $topics));
$totalTests = array_sum(array_map(function ($topic) { return (int) ($topic['test_count'] ?? 0); }, $topics));
$featuredTopic = $topics[0] ?? null;

$topicIcons = [
    'travel' => 'fa-plane-departure',
    'food' => 'fa-utensils',
    'education' => 'fa-graduation-cap',
    'technology' => 'fa-microchip',
    'health' => 'fa-heart-pulse',
    'home' => 'fa-house-chimney',
    'default' => 'fa-layer-group'
];

$topicAccent = [
    'beginner' => 'topic-accent-green',
    'intermediate' => 'topic-accent-blue',
    'advanced' => 'topic-accent-red'
];
?>

<section class="hero hero-product">
    <div class="container hero-grid">
        <div class="hero-content">
            <div class="hero-badge"><i class="fas fa-bolt"></i> Lộ trình học tiếng Anh theo nhiệm vụ mỗi ngày</div>
            <h1>Học từ vựng, làm test và luyện nói trong một không gian gọn gàng hơn.</h1>
            <p class="hero-description">Giao diện mới đưa người học vào thẳng việc cần làm: chọn chủ đề, ôn flashcard, kiểm tra nhanh và luyện speaking có phản hồi.</p>

            <div class="hero-actions">
                <?php if (!$isLoggedIn): ?>
                    <a href="<?= BASE_URL ?>/auth/register" class="btn btn-primary btn-lg">
                        <i class="fas fa-play"></i> Bắt đầu học ngay
                    </a>
                    <a href="<?= BASE_URL ?>/topic" class="btn btn-outline btn-lg">
                        <i class="fas fa-layer-group"></i> Xem chủ đề
                    </a>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>/topic" class="btn btn-primary btn-lg">
                        <i class="fas fa-book-open"></i> Tiếp tục học
                    </a>
                    <a href="<?= BASE_URL ?>/dashboard" class="btn btn-outline btn-lg">
                        <i class="fas fa-chart-line"></i> Xem tiến độ
                    </a>
                <?php endif; ?>
            </div>

            <div class="hero-stat-strip">
                <div>
                    <strong><?= $topicCount ?></strong>
                    <span>Chủ đề</span>
                </div>
                <div>
                    <strong><?= $totalVocab ?: '100+' ?></strong>
                    <span>Từ vựng</span>
                </div>
                <div>
                    <strong><?= $totalTests ?: '10+' ?></strong>
                    <span>Bài test</span>
                </div>
            </div>
        </div>

        <div class="learning-console" aria-label="Bảng học tập mẫu">
            <div class="console-header">
                <div>
                    <span>Today mission</span>
                    <strong><?= $featuredTopic ? htmlspecialchars($featuredTopic['name']) : 'Daily English' ?></strong>
                </div>
                <div class="console-score">
                    <small>Focus</small>
                    <b>82%</b>
                </div>
            </div>

            <div class="mission-list">
                <a href="<?= BASE_URL ?>/topic" class="mission-item active">
                    <i class="fas fa-clone"></i>
                    <span>
                        <strong>Ôn flashcard</strong>
                        <small>12 từ mới trong chủ đề hôm nay</small>
                    </span>
                    <b>15m</b>
                </a>
                <a href="<?= BASE_URL ?>/test" class="mission-item">
                    <i class="fas fa-clipboard-check"></i>
                    <span>
                        <strong>Làm mini test</strong>
                        <small>Kiểm tra nhanh mức ghi nhớ</small>
                    </span>
                    <b>8m</b>
                </a>
                <a href="<?= BASE_URL ?>/speaking" class="mission-item">
                    <i class="fas fa-microphone-lines"></i>
                    <span>
                        <strong>Luyện speaking</strong>
                        <small>Nhận điểm phát âm và độ trôi chảy</small>
                    </span>
                    <b>10m</b>
                </a>
            </div>

            <div class="voice-insight">
                <div class="voice-wave">
                    <span></span><span></span><span></span><span></span><span></span><span></span>
                </div>
                <div class="insight-grid">
                    <div><span>Pronunciation</span><strong>86</strong></div>
                    <div><span>Fluency</span><strong>78</strong></div>
                    <div><span>Accuracy</span><strong>91</strong></div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="quick-start">
    <div class="container quick-start-grid">
        <a href="<?= BASE_URL ?>/topic" class="quick-card">
            <i class="fas fa-book-open"></i>
            <span>
                <strong>Học theo chủ đề</strong>
                <small><?= $topicCount ?> topic có từ vựng, bài học và bài test</small>
            </span>
        </a>
        <a href="<?= BASE_URL ?>/speaking" class="quick-card">
            <i class="fas fa-microphone"></i>
            <span>
                <strong>Luyện nói</strong>
                <small>Ghi âm, chấm điểm và cải thiện phát âm</small>
            </span>
        </a>
        <a href="<?= BASE_URL ?>/test" class="quick-card">
            <i class="fas fa-clipboard-list"></i>
            <span>
                <strong>Kiểm tra nhanh</strong>
                <small>Quiz, listening và reading để đo tiến độ</small>
            </span>
        </a>
        <a href="<?= BASE_URL ?>/dashboard" class="quick-card">
            <i class="fas fa-chart-line"></i>
            <span>
                <strong>Theo dõi tiến độ</strong>
                <small>Xem điểm, streak và chủ đề đã học</small>
            </span>
        </a>
    </div>
</section>

<section class="learning-flow">
    <div class="container">
        <div class="section-header section-header-left">
            <span class="section-kicker">Học như một vòng luyện tập</span>
            <h2>Mỗi buổi học có mục tiêu rõ, thao tác ít và phản hồi nhanh.</h2>
            <p>Thay vì để người học tự đoán bước tiếp theo, trang chủ mới gom các hành động chính thành một lộ trình dễ theo.</p>
        </div>

        <div class="flow-track">
            <div class="flow-step">
                <i class="fas fa-layer-group"></i>
                <strong>Chọn chủ đề</strong>
                <span>Bắt đầu từ nhóm từ vựng gần với tình huống thực tế.</span>
            </div>
            <div class="flow-step">
                <i class="fas fa-clone"></i>
                <strong>Ôn flashcard</strong>
                <span>Ghi nhớ nghĩa, phát âm và ví dụ qua từng thẻ học.</span>
            </div>
            <div class="flow-step">
                <i class="fas fa-clipboard-check"></i>
                <strong>Làm bài test</strong>
                <span>Kiểm tra lại bằng quiz, reading hoặc listening.</span>
            </div>
            <div class="flow-step">
                <i class="fas fa-microphone-lines"></i>
                <strong>Luyện nói</strong>
                <span>Ghi âm câu trả lời và xem gợi ý cải thiện.</span>
            </div>
        </div>
    </div>
</section>

<section class="speaking-showcase">
    <div class="container speaking-grid">
        <div class="speaking-copy">
            <span class="section-kicker">Điểm nhấn mới</span>
            <h2>Phần speaking cần nổi bật hơn vì đây là tính năng khác biệt của đồ án.</h2>
            <p>Người học nhìn thấy ngay các chỉ số như phát âm, độ trôi chảy và độ chính xác. Cách trình bày này giúp website trông giống một sản phẩm học ngoại ngữ hoàn chỉnh hơn.</p>
            <a href="<?= BASE_URL ?>/speaking" class="btn btn-primary">
                <i class="fas fa-microphone"></i> Vào luyện nói
            </a>
        </div>

        <div class="feedback-panel">
            <div class="feedback-header">
                <i class="fas fa-headphones-simple"></i>
                <div>
                    <strong>Speaking feedback</strong>
                    <span>Travel conversation</span>
                </div>
            </div>
            <div class="feedback-meter">
                <span style="--value:86%"><b>86</b><small>Pronunciation</small></span>
                <span style="--value:78%"><b>78</b><small>Fluency</small></span>
                <span style="--value:91%"><b>91</b><small>Accuracy</small></span>
            </div>
            <div class="feedback-note">
                <i class="fas fa-lightbulb"></i>
                <p>Gợi ý: kéo dài âm cuối rõ hơn trong các từ như “planned”, “visited”, “asked”.</p>
            </div>
        </div>
    </div>
</section>

<section class="topics-preview">
    <div class="container">
        <div class="section-header">
            <span class="section-kicker">Khám phá nội dung</span>
            <h2>Chủ đề học nổi bật</h2>
            <p>Mỗi chủ đề được trình bày như một nhiệm vụ học ngắn, có level, số từ vựng, bài học và bài test rõ ràng.</p>
        </div>

        <div class="topics-grid">
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
                    $description = trim((string) ($topic['description'] ?? ''));
                ?>
                <a href="<?= BASE_URL ?>/topic/show/<?= $topic['id'] ?>" class="topic-card <?= $accentClass ?>">
                    <div class="topic-card-header">
                        <span class="topic-icon"><i class="fas <?= $icon ?>"></i></span>
                        <span class="topic-level level-<?= htmlspecialchars($level) ?>"><?= ucfirst(htmlspecialchars($level)) ?></span>
                    </div>
                    <div class="topic-card-body">
                        <h3><?= htmlspecialchars($topic['name']) ?></h3>
                        <p><?= htmlspecialchars(mb_substr($description, 0, 96)) ?><?= mb_strlen($description) > 96 ? '...' : '' ?></p>
                    </div>
                    <div class="topic-card-footer">
                        <span><i class="fas fa-font"></i> <?= (int) $topic['vocab_count'] ?> từ</span>
                        <span><i class="fas fa-book"></i> <?= (int) $topic['lesson_count'] ?> bài</span>
                        <span><i class="fas fa-clipboard-check"></i> <?= (int) $topic['test_count'] ?> test</span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>

        <div class="topics-action">
            <a href="<?= BASE_URL ?>/topic" class="btn btn-primary">Xem tất cả chủ đề <i class="fas fa-arrow-right"></i></a>
        </div>
    </div>
</section>
