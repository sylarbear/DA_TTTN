<!-- Lesson Detail Page -->
<section class="page-header">
    <div class="container">
        <nav class="breadcrumb">
            <a href="<?= BASE_URL ?>/topic">Chủ đề</a>
            <span>/</span>
            <a href="<?= BASE_URL ?>/topic/show/<?= $topic['id'] ?>"><?= htmlspecialchars($topic['name']) ?></a>
            <span>/</span>
            <span><?= htmlspecialchars($lesson['title']) ?></span>
        </nav>
        <h1><?= htmlspecialchars($lesson['title']) ?></h1>
        <p><?= htmlspecialchars($lesson['description']) ?></p>
    </div>
</section>

<section class="lesson-content-section">
    <div class="container">
        <div class="lesson-body">
            <?php foreach ($lesson['contents'] as $content): ?>
                <div class="content-block content-<?= $content['content_type'] ?>">
                    <?php if ($content['content_type'] === 'text'): ?>
                        <div class="text-content">
                            <?= $content['content'] ?>
                        </div>
                    <?php elseif ($content['content_type'] === 'image'): ?>
                        <div class="image-content">
                            <img src="<?= htmlspecialchars($content['content']) ?>" alt="Lesson image" loading="lazy">
                        </div>
                    <?php elseif ($content['content_type'] === 'audio'): ?>
                        <div class="audio-content">
                            <audio controls>
                                <source src="<?= htmlspecialchars($content['content']) ?>" type="audio/mpeg">
                                Trình duyệt không hỗ trợ audio.
                            </audio>
                        </div>
                    <?php elseif ($content['content_type'] === 'video'): ?>
                        <div class="video-content">
                            <video controls width="100%">
                                <source src="<?= htmlspecialchars($content['content']) ?>" type="video/mp4">
                                Trình duyệt không hỗ trợ video.
                            </video>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Review Section -->
        <div class="lesson-review-section" style="margin-top:2rem; padding-top:2rem; border-top:2px solid var(--border-color);">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem; flex-wrap:wrap; gap:1rem;">
                <h3 style="margin:0;"><i class="fas fa-star" style="color:#f59e0b;"></i> Đánh giá bài học</h3>
                <div style="display:flex; align-items:center; gap:0.5rem;">
                    <?php $avg = round($reviewStats['avg_rating'] ?? 0, 1); $total = (int)($reviewStats['total_reviews'] ?? 0); ?>
                    <span style="font-size:1.5rem; font-weight:700; color:#f59e0b;" id="avgRatingDisplay"><?= $avg ?: '—' ?></span>
                    <div style="display:flex; gap:2px;" id="avgStarsDisplay">
                        <?php for ($s = 1; $s <= 5; $s++): ?>
                            <i class="fas fa-star" style="color:<?= $s <= round($avg) ? '#f59e0b' : '#e2e8f0' ?>; font-size:0.9rem;"></i>
                        <?php endfor; ?>
                    </div>
                    <span style="color:var(--text-muted); font-size:0.85rem;" id="totalReviewsDisplay">(<?= $total ?> đánh giá)</span>
                </div>
            </div>

            <?php if (Middleware::isLoggedIn()): ?>
            <!-- Write/Edit Review -->
            <div class="section-card" style="padding:1.5rem; margin-bottom:1.5rem;">
                <h4 style="margin:0 0 1rem;"><?= $userReview ? '✏️ Sửa đánh giá' : '✍️ Viết đánh giá' ?></h4>
                <form id="reviewForm">
                    <!-- Star selector -->
                    <div style="margin-bottom:1rem;">
                        <label style="font-weight:600; margin-bottom:0.5rem; display:block;">Đánh giá sao</label>
                        <div class="star-selector" style="display:flex; gap:0.25rem; font-size:1.8rem; cursor:pointer;">
                            <?php for ($s = 1; $s <= 5; $s++): ?>
                                <i class="fas fa-star star-select" data-value="<?= $s ?>" 
                                   style="color:<?= ($userReview && $s <= $userReview['rating']) ? '#f59e0b' : '#e2e8f0' ?>; transition:color 0.15s;"
                                   onmouseenter="hoverStars(<?= $s ?>)" onclick="selectStar(<?= $s ?>)"></i>
                            <?php endfor; ?>
                        </div>
                        <input type="hidden" id="selectedRating" value="<?= $userReview['rating'] ?? 0 ?>">
                    </div>
                    <!-- Comment -->
                    <div style="margin-bottom:1rem;">
                        <label style="font-weight:600; margin-bottom:0.5rem; display:block;">Nhận xét (tùy chọn)</label>
                        <textarea id="reviewComment" class="form-input" rows="3" placeholder="Chia sẻ cảm nhận của bạn về bài học..." style="padding:0.75rem; resize:vertical;"><?= htmlspecialchars($userReview['comment'] ?? '') ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary" id="submitReviewBtn" style="padding:0.6rem 1.5rem;">
                        <i class="fas fa-paper-plane"></i> <?= $userReview ? 'Cập nhật' : 'Gửi đánh giá' ?>
                    </button>
                </form>
            </div>
            <?php endif; ?>

            <!-- Reviews List -->
            <?php if (!empty($reviews)): ?>
            <div class="reviews-list">
                <?php foreach ($reviews as $rev): ?>
                <div style="padding:1rem 0; border-bottom:1px solid var(--border-color);">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.5rem;">
                        <div style="display:flex; align-items:center; gap:0.5rem;">
                            <strong><?= htmlspecialchars($rev['full_name'] ?? $rev['username']) ?></strong>
                            <div style="display:flex; gap:1px;">
                                <?php for ($s = 1; $s <= 5; $s++): ?>
                                    <i class="fas fa-star" style="color:<?= $s <= $rev['rating'] ? '#f59e0b' : '#e2e8f0' ?>; font-size:0.75rem;"></i>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <small style="color:var(--text-muted);"><?= date('d/m/Y', strtotime($rev['created_at'])) ?></small>
                    </div>
                    <?php if (!empty($rev['comment'])): ?>
                        <p style="margin:0; color:var(--text-secondary); font-size:0.9rem;"><?= htmlspecialchars($rev['comment']) ?></p>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
            <?php elseif (!Middleware::isLoggedIn()): ?>
                <p style="text-align:center; color:var(--text-muted); padding:2rem;">Chưa có đánh giá nào. <a href="<?= BASE_URL ?>/auth/login">Đăng nhập</a> để đánh giá!</p>
            <?php endif; ?>
        </div>

        <!-- Navigation giữa các bài -->
        <div class="lesson-navigation">
            <?php if ($prevLesson): ?>
                <a href="<?= BASE_URL ?>/lesson/show/<?= $prevLesson['id'] ?>" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i> <?= htmlspecialchars($prevLesson['title']) ?>
                </a>
            <?php else: ?>
                <span></span>
            <?php endif; ?>

            <?php if ($nextLesson): ?>
                <a href="<?= BASE_URL ?>/lesson/show/<?= $nextLesson['id'] ?>" class="btn btn-primary">
                    <?= htmlspecialchars($nextLesson['title']) ?> <i class="fas fa-arrow-right"></i>
                </a>
            <?php else: ?>
                <a href="<?= BASE_URL ?>/topic/show/<?= $topic['id'] ?>" class="btn btn-primary">
                    <i class="fas fa-check"></i> Hoàn thành
                </a>
            <?php endif; ?>
        </div>
    </div>
</section>

<script>
let selectedRating = <?= $userReview['rating'] ?? 0 ?>;

function hoverStars(n) {
    document.querySelectorAll('.star-select').forEach(star => {
        star.style.color = parseInt(star.dataset.value) <= n ? '#f59e0b' : '#e2e8f0';
    });
}

function selectStar(n) {
    selectedRating = n;
    document.getElementById('selectedRating').value = n;
    hoverStars(n);
}

// Reset stars on mouseout
document.querySelector('.star-selector')?.addEventListener('mouseleave', function() {
    hoverStars(selectedRating);
});

// Submit review
document.getElementById('reviewForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (selectedRating < 1) {
        alert('Vui lòng chọn số sao!');
        return;
    }
    
    const btn = document.getElementById('submitReviewBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang gửi...';
    
    fetch('<?= BASE_URL ?>/lesson/review', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'same-origin',
        body: JSON.stringify({
            lesson_id: <?= $lesson['id'] ?>,
            rating: selectedRating,
            comment: document.getElementById('reviewComment').value.trim()
        })
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            // Update display
            document.getElementById('avgRatingDisplay').textContent = res.avg_rating;
            document.getElementById('totalReviewsDisplay').textContent = '(' + res.total_reviews + ' đánh giá)';
            
            btn.innerHTML = '<i class="fas fa-check"></i> Đã gửi!';
            btn.style.background = 'var(--success)';
            setTimeout(() => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-paper-plane"></i> Cập nhật';
                btn.style.background = '';
            }, 2000);
        } else {
            alert(res.error || 'Có lỗi xảy ra.');
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-paper-plane"></i> Gửi đánh giá';
        }
    })
    .catch(err => {
        alert('Lỗi kết nối: ' + err.message);
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-paper-plane"></i> Gửi đánh giá';
    });
});
</script>

