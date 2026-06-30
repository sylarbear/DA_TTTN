<!-- Placement Intro Page — Duolingo-style self-assessment -->
<section class="placement-intro">
    <div class="placement-intro-card">
        <div class="placement-intro-icon">
            <i class="fas fa-clipboard-check"></i>
        </div>
        <h1>Bạn biết tiếng Anh ở mức nào?</h1>
        <p class="placement-intro-sub">Chọn mô tả phù hợp nhất với bạn. Nếu không chắc, hãy chọn "Biết một ít" để làm bài kiểm tra.</p>

        <div class="placement-options">
            <form action="<?= BASE_URL ?>/placement/beginner" method="POST" class="placement-option-form">
                <button type="submit" class="placement-option-card">
                    <div class="placement-option-icon beginner">
                        <i class="fas fa-seedling"></i>
                    </div>
                    <div class="placement-option-text">
                        <strong>Mới bắt đầu</strong>
                        <span>Mình chưa biết gì hoặc mới biết vài từ cơ bản</span>
                    </div>
                    <i class="fas fa-arrow-right"></i>
                </button>
            </form>

            <a href="<?= BASE_URL ?>/placement/start?level=some" class="placement-option-card">
                <div class="placement-option-icon some">
                    <i class="fas fa-book-open"></i>
                </div>
                <div class="placement-option-text">
                    <strong>Biết một ít</strong>
                    <span>Mình có thể giao tiếp đơn giản, biết ngữ pháp cơ bản</span>
                </div>
                <i class="fas fa-arrow-right"></i>
            </a>

            <a href="<?= BASE_URL ?>/placement/start?level=advanced" class="placement-option-card">
                <div class="placement-option-icon advanced">
                    <i class="fas fa-star"></i>
                </div>
                <div class="placement-option-text">
                    <strong>Khá tốt / Thành thạo</strong>
                    <span>Mình có thể đọc hiểu, giao tiếp tự tin</span>
                </div>
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        <p class="placement-intro-note">Bài kiểm tra chỉ mất khoảng 5-10 phút. Bạn có thể làm lại bất cứ lúc nào.</p>
    </div>
</section>

<link rel="stylesheet" href="<?= BASE_URL ?>/css/placement.css">
