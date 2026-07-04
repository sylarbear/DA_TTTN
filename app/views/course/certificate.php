<!-- Certificate Page — Chứng chỉ hoàn thành khóa học -->
<?php
$courseTitle = htmlspecialchars($course['title']);
$userName    = htmlspecialchars($user['full_name'] ?? $user['username']);
$cefr        = $course['cefr_level'];
$completedAt = $progress['completed_at'] ?? date('Y-m-d');
$certId      = strtoupper(substr(md5($user['id'] . $course['id'] . 'engpath'), 0, 10));
?>
<section class="certificate-page">
    <div class="container">
        <div class="certificate-card">
            <div class="cert-border-outer">
                <div class="cert-border-inner">
                    <div class="cert-header">
                        <div class="cert-logo">
                            <i class="fas fa-route"></i>
                            <span><?= APP_NAME ?></span>
                        </div>
                        <div class="cert-badge">
                            <span class="course-level-badge cefr-<?= strtolower($cefr) ?>" style="font-size: 14px; padding: 6px 16px;"><?= $cefr ?></span>
                        </div>
                    </div>

                    <div class="cert-body">
                        <h1>Chứng chỉ hoàn thành</h1>
                        <p class="cert-subtitle">This certificate is awarded to</p>

                        <div class="cert-recipient"><?= $userName ?></div>

                        <p class="cert-for">for successfully completing the course</p>

                        <div class="cert-course-title"><?= $courseTitle ?></div>

                        <div class="cert-meta">
                            <div class="cert-meta-item">
                                <span>Ngày hoàn thành</span>
                                <strong><?= date('d/m/Y', strtotime($completedAt)) ?></strong>
                            </div>
                            <div class="cert-meta-item">
                                <span>Chứng chỉ số</span>
                                <strong><?= $certId ?></strong>
                            </div>
                            <div class="cert-meta-item">
                                <span>Trình độ</span>
                                <strong>CEFR <?= $cefr ?></strong>
                            </div>
                        </div>
                    </div>

                    <div class="cert-footer">
                        <div class="cert-signature">
                            <div class="cert-sig-line"></div>
                            <span>EngPath Team</span>
                        </div>
                        <div class="cert-seal">
                            <i class="fas fa-award"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="cert-actions">
            <button class="btn btn-primary" onclick="window.print()">
                <i class="fas fa-download"></i> Tải chứng chỉ (PDF)
            </button>
            <a href="<?= BASE_URL ?>/course" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Quay lại khóa học
            </a>
        </div>

        <?php
        // Suggest next course
        $courseModel = new Course();
        $nextStmt = getDB()->prepare(
            'SELECT c.id, c.title FROM courses c
             INNER JOIN course_progress cp ON cp.course_id = c.id
             WHERE cp.user_id = :uid AND cp.status = "unlocked"
             ORDER BY FIELD(c.cefr_level, "A1","A2","B1","B2","C1"), c.sort_order LIMIT 1'
        );
        $nextStmt->execute(['uid' => $user['id']]);
        $nextCourse = $nextStmt->fetch();
        ?>
        <?php if ($nextCourse): ?>
        <div class="cert-next-course">
            <i class="fas fa-rocket"></i>
            <span>Tiếp tục học: <a href="<?= BASE_URL ?>/course/show/<?= $nextCourse['id'] ?>"><?= htmlspecialchars($nextCourse['title']) ?></a></span>
        </div>
        <?php endif; ?>
    </div>
</section>

<style>
/* Certificate-specific styles (print-friendly) */
.certificate-page { padding: 60px 0 80px; background: #f8fafc; min-height: calc(100vh - 200px); }
.certificate-card { max-width: 800px; margin: 0 auto; }
.cert-border-outer {
    background: linear-gradient(135deg, #4f46e5, #7c3aed, #6366f1);
    padding: 3px; border-radius: 8px;
}
.cert-border-inner { background: #fff; border-radius: 6px; padding: 48px 56px; }

.cert-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; }
.cert-logo { display: flex; align-items: center; gap: 8px; font-size: 20px; font-weight: 800; color: #4f46e5; }
.cert-logo i { font-size: 24px; }

.cert-body { text-align: center; }
.cert-body h1 { font-size: 32px; font-weight: 800; color: #1e293b; margin-bottom: 12px; letter-spacing: -0.5px; }
.cert-subtitle { font-size: 16px; color: #64748b; margin-bottom: 20px; font-style: italic; }
.cert-recipient {
    font-size: 36px; font-weight: 800; color: #4f46e5;
    padding: 12px 0; border-top: 2px solid #e2e8f0;
    border-bottom: 2px solid #e2e8f0;
    margin: 16px auto; max-width: 500px;
    font-family: 'Be Vietnam Pro', serif;
}
.cert-for { font-size: 14px; color: #64748b; margin-top: 16px; }
.cert-course-title { font-size: 22px; font-weight: 700; color: #1e293b; margin: 8px 0 32px; }

.cert-meta { display: flex; justify-content: center; gap: 40px; margin-bottom: 40px; flex-wrap: wrap; }
.cert-meta-item { text-align: center; }
.cert-meta-item span { display: block; font-size: 12px; color: #94a3b8; margin-bottom: 4px; }
.cert-meta-item strong { font-size: 16px; color: #1e293b; }

.cert-footer { display: flex; justify-content: space-between; align-items: flex-end; padding-top: 24px; border-top: 1px solid #f1f5f9; }
.cert-signature { text-align: center; }
.cert-sig-line { width: 180px; height: 1px; background: #cbd5e1; margin-bottom: 8px; }
.cert-signature span { font-size: 13px; color: #64748b; }
.cert-seal { font-size: 64px; color: #f59e0b; opacity: 0.8; }

.cert-actions { text-align: center; margin-top: 32px; display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }
.cert-next-course {
    text-align: center; margin-top: 24px; padding: 16px; background: #eef2ff;
    border-radius: 12px; display: flex; align-items: center; justify-content: center; gap: 8px;
    font-size: 14px; color: #4f46e5;
}
.cert-next-course a { font-weight: 700; color: #4f46e5; }

/* Print styles */
@media print {
    body { background: #fff; }
    .navbar, .footer, .cert-actions, .cert-next-course, .certificate-page { background: #fff; }
    .certificate-page { padding: 20px 0; }
    .cert-border-outer { padding: 2px; box-shadow: none; }
    .cert-actions { display: none; }
    .cert-next-course { display: none; }
}

@media (max-width: 640px) {
    .cert-border-inner { padding: 24px 20px; }
    .cert-body h1 { font-size: 22px; }
    .cert-recipient { font-size: 24px; }
    .cert-course-title { font-size: 18px; }
    .cert-meta { gap: 16px; }
    .cert-sig-line { width: 120px; }
    .cert-seal { font-size: 48px; }
}
</style>
