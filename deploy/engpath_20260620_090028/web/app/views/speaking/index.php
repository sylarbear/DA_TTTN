<section class="speaking-hero">
    <div class="container speaking-hero-grid">
        <div>
            <span class="section-kicker">Speaking AI</span>
            <h1>Luyện nói tiếng Anh và nhận phản hồi ngay.</h1>
            <p>Chọn một chủ đề, ghi âm câu trả lời và để EngPath đánh giá phát âm, độ trôi chảy và độ chính xác.</p>
            <div class="speaking-hero-actions">
                <?php if (Middleware::isLoggedIn()): ?>
                    <a href="<?= BASE_URL ?>/speaking/freetext" class="btn btn-primary btn-lg"><i class="fas fa-volume-up"></i> Luyện phát âm tự do</a>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>/auth/login" class="btn btn-primary btn-lg"><i class="fas fa-lock"></i> Đăng nhập để luyện</a>
                <?php endif; ?>
                <a href="#prompt-list" class="btn btn-outline btn-lg"><i class="fas fa-list-check"></i> Xem đề luyện</a>
            </div>
        </div>

        <div class="speaking-score-card">
            <div class="feedback-header">
                <i class="fas fa-headphones-simple"></i>
                <div>
                    <strong>AI speaking room</strong>
                    <span>Practice preview</span>
                </div>
            </div>
            <div class="voice-orb"><i class="fas fa-microphone-lines"></i></div>
            <div class="feedback-meter">
                <span><b>88</b><small>Pronunciation</small></span>
                <span><b>81</b><small>Fluency</small></span>
                <span><b>92</b><small>Accuracy</small></span>
            </div>
        </div>
    </div>
</section>

<section class="speaking-section" id="prompt-list">
    <div class="container">
        <div class="section-header">
            <span class="section-kicker">Practice sets</span>
            <h2>Chọn đề luyện theo chủ đề</h2>
            <p>Mỗi đề được thiết kế để người học trả lời ngắn, luyện phát âm và tăng phản xạ nói.</p>
        </div>

        <div class="speaking-help-grid">
            <div class="info-card">
                <h3><i class="fas fa-circle-info"></i> Cách luyện</h3>
                <ol>
                    <li>Chọn một đề phù hợp với trình độ.</li>
                    <li>Đọc câu hỏi và chuẩn bị ý trả lời.</li>
                    <li>Nhấn ghi âm rồi nói bằng tiếng Anh.</li>
                    <li>Xem điểm và gợi ý cải thiện.</li>
                </ol>
            </div>
            <div class="info-card accent">
                <h3><i class="fas fa-lightbulb"></i> Mẹo nhỏ</h3>
                <p>Dùng Chrome hoặc Edge, cho phép truy cập microphone và nói ở nơi ít tiếng ồn để kết quả ổn định hơn.</p>
            </div>
        </div>

        <?php if (empty($groupedPrompts)): ?>
            <div class="empty-state">
                <i class="fas fa-microphone-slash"></i>
                <p>Chưa có bài speaking nào.</p>
            </div>
        <?php else: ?>
            <?php foreach ($groupedPrompts as $topicName => $prompts): ?>
                <div class="speaking-group">
                    <div class="group-heading">
                        <h2><?= htmlspecialchars($topicName) ?></h2>
                        <span><?= count($prompts) ?> đề luyện</span>
                    </div>
                    <div class="prompts-grid">
                        <?php foreach ($prompts as $prompt): ?>
                            <div class="prompt-card" id="prompt-<?= $prompt['id'] ?>">
                                <div class="prompt-header">
                                    <span class="difficulty diff-<?= htmlspecialchars($prompt['difficulty']) ?>">
                                        <?= ucfirst(htmlspecialchars($prompt['difficulty'])) ?>
                                    </span>
                                    <i class="fas fa-microphone-lines"></i>
                                </div>
                                <div class="prompt-body">
                                    <p><?= htmlspecialchars(mb_substr($prompt['prompt_text'], 0, 130)) ?><?= mb_strlen($prompt['prompt_text']) > 130 ? '...' : '' ?></p>
                                </div>
                                <div class="prompt-footer">
                                    <?php if (Middleware::isLoggedIn()): ?>
                                        <a href="<?= BASE_URL ?>/speaking/practice/<?= $prompt['id'] ?>" class="btn btn-primary">
                                            <i class="fas fa-microphone"></i> Luyện tập
                                        </a>
                                    <?php else: ?>
                                        <a href="<?= BASE_URL ?>/auth/login" class="btn btn-outline">
                                            <i class="fas fa-lock"></i> Đăng nhập
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>
