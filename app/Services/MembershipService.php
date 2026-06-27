<?php

namespace App\Services;

use DateTime;

/**
 * MembershipService
 * Tập trung logic membership: tính hạn, hạ cấp, kiểm tra hủy đơn
 * Trước đây logic này bị lặp 6 lần rải rác trong controllers
 */
class MembershipService
{
    /**
     * Tính ngày hết hạn membership khi nâng cấp
     * Nếu đang còn Pro, cộng dồn thời gian; nếu hết hạn, tính từ hiện tại.
     *
     * @param  int         $durationMonths Số tháng của gói
     * @param  string|null $currentExpiry  Ngày hết hạn hiện tại (Y-m-d H:i:s)
     * @return string Ngày hết hạn mới (Y-m-d H:i:s)
     */
    public static function calculateExpiryDate($durationMonths, $currentExpiry = null)
    {
        $now = new DateTime();

        if ($currentExpiry && strtotime($currentExpiry) > time()) {
            $baseDate = new DateTime($currentExpiry);
        } else {
            $baseDate = $now;
        }

        return $baseDate->modify('+' . (int) $durationMonths . ' months')->format('Y-m-d H:i:s');
    }

    /**
     * Auto-downgrade tài khoản Pro đã hết hạn
     * Gọi khi user đăng nhập để đảm bảo membership luôn đúng
     *
     * @param int    $userId
     * @param string $membership
     * @param string|null $expiredAt
     */
    public static function downgradeIfExpired($userId, $membership, $expiredAt)
    {
        if ($membership !== 'pro' || empty($expiredAt)) {
            return;
        }

        if (strtotime($expiredAt) < time()) {
            $db = getDB();
            $db->prepare("UPDATE users SET membership = 'free' WHERE id = :id")
               ->execute(['id' => $userId]);
        }
    }

    /**
     * Kiểm tra điều kiện hủy đơn membership
     * CHÍNH SÁCH:
     * - Trong 24h đầu: hoàn 100%
     * - Gói >= 3 tháng, trong 7 ngày: hoàn 50%
     * - Quá hạn: không thể hủy
     *
     * @param  array $order     Order data (phải có status, activated_at)
     * @param  int   $durationMonths Số tháng của gói
     * @return array ['can_cancel' => bool, 'reason' => string, 'refund_percent' => int, 'policy_note' => string]
     */
    public static function checkCancelEligibility($order, $durationMonths)
    {
        $result = [
            'can_cancel' => false,
            'reason' => '',
            'refund_percent' => 0,
            'policy_note' => '',
        ];

        // 1. Đã cancelled
        if ($order['status'] === 'cancelled') {
            $result['reason'] = 'Đơn này đã được hủy trước đó.';

            return $result;
        }

        // 2. Đã completed → cần ticket đặc biệt
        if ($order['status'] === 'completed') {
            $result['reason'] = 'Đơn đã được kích hoạt. Bạn không thể hủy trực tiếp, nhưng có thể gửi ticket yêu cầu hoàn tiền.';
            $result['policy_note'] = 'refund_request';

            return $result;
        }

        // 3. Đơn pending → kiểm tra thời gian
        $createdAt = strtotime($order['activated_at']);
        $hoursSinceOrder = (time() - $createdAt) / 3600;
        $daysSinceOrder = $hoursSinceOrder / 24;

        // 4. Check đã có ticket hủy chưa
        $db = getDB();
        $existing = $db->prepare("SELECT id FROM support_tickets WHERE related_order_id = :oid AND type = 'cancel_order' AND status NOT IN ('closed')");
        $existing->execute(['oid' => $order['id']]);
        if ($existing->fetch()) {
            $result['reason'] = 'Bạn đã gửi yêu cầu hủy cho đơn này. Vui lòng chờ Admin xử lý.';

            return $result;
        }

        $durationMonths = (int) $durationMonths;

        // 5. Tính phần trăm hoàn tiền
        if ($hoursSinceOrder <= 24) {
            $result['can_cancel'] = true;
            $result['refund_percent'] = 100;
            $result['reason'] = 'Đơn trong vòng 24 giờ — đủ điều kiện hủy.';
            $result['policy_note'] = 'Hoàn 100% giá trị đơn hàng.';
        } elseif ($daysSinceOrder <= 7 && $durationMonths >= 3) {
            $result['can_cancel'] = true;
            $result['refund_percent'] = 50;
            $result['reason'] = 'Đơn gói ' . $durationMonths . ' tháng, trong 7 ngày — đủ điều kiện hủy.';
            $result['policy_note'] = 'Hoàn 50% giá trị đơn hàng (do đã quá 24 giờ).';
        } else {
            $hoursText = $hoursSinceOrder < 48 ? round($hoursSinceOrder) . ' giờ' : round($daysSinceOrder) . ' ngày';
            $result['reason'] = 'Đã quá thời hạn hủy đơn (' . $hoursText . ' kể từ khi đặt).';
            if ($durationMonths >= 3) {
                $result['policy_note'] = 'Gói ' . $durationMonths . ' tháng chỉ được hủy trong 7 ngày đầu. Gói 1 tháng chỉ được hủy trong 24 giờ.';
            } else {
                $result['policy_note'] = 'Gói 1 tháng chỉ được hủy trong 24 giờ đầu.';
            }
        }

        return $result;
    }
}
