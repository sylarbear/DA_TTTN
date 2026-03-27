<!-- Speaking Index Page -->
<section class="page-header">
    <div class="container">
        <h1><i class="fas fa-microphone"></i> Luyện nói</h1>
        <p>Thực hành kỹ năng Speaking với AI chấm điểm</p>
    </div>
</section>

<section class="speaking-section">
    <div class="container">
        <!-- Hướng dẫn -->
        <div class="info-card">
            <h3><i class="fas fa-info-circle"></i> Cách sử dụng</h3>
            <ol>
                <li>Chọn một đề bài phù hợp với trình độ</li>
                <li>Đọc kỹ câu hỏi và chuẩn bị câu trả lời</li>
                <li>Nhấn nút <strong>Ghi âm</strong> và nói bằng tiếng Anh</li>
                <li>Hệ thống sẽ chuyển giọng nói thành văn bản và chấm điểm</li>
            </ol>
            <p class="info-note"><i class="fas fa-exclamation-triangle"></i> Yêu cầu sử dụng Chrome hoặc Edge. Cho phép truy cập microphone.</p>
        </div>

        <!-- Free Text Mode Promo -->
        <?php if (Middleware::isLoggedIn()): ?>
        <div class="freetext-promo">
            <h3><i class="fas fa-keyboard"></i> Luyện phát âm tự do</h3>
            <p>Nhập bất kỳ đoạn văn tiếng Anh nào để nghe phát âm chuẩn với 5 giọng đọc khác nhau</p>
            <a href="<?= BASE_URL ?>/speaking/freetext" class="btn btn-primary btn-lg">
                <i class="fas fa-volume-up"></i> Bắt đầu ngay
            </a>
        </div>
        <?php endif; ?>

        <?php if (empty($groupedPrompts)): ?>
            <div class="empty-state">
                <i class="fas fa-microphone-slash"></i>
                <p>Chưa có bài speaking nào.</p>
            </div>
        <?php else: ?>
            <?php foreach ($groupedPrompts as $topicName => $prompts): ?>
                <div class="speaking-group">
                    <h2 class="group-title"><?= htmlspecialchars($topicName) ?></h2>
                    <div class="prompts-grid">
                        <?php foreach ($prompts as $prompt): ?>
                            <div class="prompt-card" id="prompt-<?= $prompt['id'] ?>">
                                <div class="prompt-header">
                                    <span class="difficulty diff-<?= $prompt['difficulty'] ?>">
                                        <?= ucfirst($prompt['difficulty']) ?>
                                    </span>
                                </div>
                                <div class="prompt-body">
                                    <p><?= htmlspecialchars(mb_substr($prompt['prompt_text'], 0, 120)) ?>...</p>
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
