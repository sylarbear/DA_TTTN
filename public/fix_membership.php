<?php
/**
 * Fix membership_plans data: correct UTF-8 Vietnamese text + new prices
 */
require_once __DIR__ . '/../app/config/database.php';
$db = getDB();

// Force UTF-8 connection
$db->exec("SET NAMES utf8mb4");

echo "<h3>Fixing membership_plans...</h3><pre>";

// Update plan 1: Pro 1 Tháng → 50,000
$db->exec("UPDATE membership_plans SET 
    name = 'Pro 1 Tháng',
    price = 50000,
    description = 'Trải nghiệm đầy đủ tính năng Pro trong 1 tháng',
    features = 'Mở khóa tất cả khóa học|Luyện nói với AI chấm điểm|Bài test Listening & Reading|Hỗ trợ ưu tiên'
    WHERE duration_months = 1");
echo "Plan 1 (1 tháng): 50,000 VND ✅\n";

// Update plan 2: Pro 3 Tháng → 120,000
$db->exec("UPDATE membership_plans SET 
    name = 'Pro 3 Tháng',
    price = 120000,
    description = 'Tiết kiệm 17% so với gói 1 tháng',
    features = 'Mở khóa tất cả khóa học|Luyện nói với AI chấm điểm|Bài test Listening & Reading|Hỗ trợ ưu tiên|Báo cáo chi tiết'
    WHERE duration_months = 3");
echo "Plan 2 (3 tháng): 120,000 VND ✅\n";

// Update plan 3: Pro 12 Tháng → 400,000
$db->exec("UPDATE membership_plans SET 
    name = 'Pro 12 Tháng',
    price = 400000,
    description = 'Tiết kiệm 33% - Đầu tư dài hạn',
    features = 'Mở khóa tất cả khóa học|Luyện nói với AI chấm điểm|Bài test Listening & Reading|Hỗ trợ ưu tiên|Báo cáo chi tiết|Ưu tiên tính năng mới'
    WHERE duration_months = 12");
echo "Plan 3 (12 tháng): 400,000 VND ✅\n";

echo "\n✅ Done! Membership plans updated.";
echo "</pre>";
