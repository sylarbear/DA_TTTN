<?php

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \App\Services\MembershipService
 */
class MembershipServiceTest extends TestCase
{
    /**
     * @covers ::calculateExpiryDate
     */
    public function testCalculateExpiryDate_whenNoExistingMembership_addsMonthsFromNow()
    {
        $expiry = App\Services\MembershipService::calculateExpiryDate(1, null);

        $expected = (new DateTime())->modify('+1 month');
        // Chấp nhận sai số 1 phút do thời gian chạy test
        $this->assertEqualsWithDelta(
            $expected->getTimestamp(),
            (new DateTime($expiry))->getTimestamp(),
            60,
            'Hạn mới phải là 1 tháng từ hiện tại khi chưa có membership'
        );
    }

    /**
     * @covers ::calculateExpiryDate
     */
    public function testCalculateExpiryDate_whenActivePro_accumulatesFromCurrentExpiry()
    {
        $futureDate = (new DateTime('+20 days'))->format('Y-m-d H:i:s');

        $expiry = App\Services\MembershipService::calculateExpiryDate(1, $futureDate);

        $expected = (new DateTime($futureDate))->modify('+1 month');
        $this->assertEqualsWithDelta(
            $expected->getTimestamp(),
            (new DateTime($expiry))->getTimestamp(),
            60,
            'Khi đang Pro, hạn mới phải cộng dồn từ hạn cũ'
        );
    }

    /**
     * @covers ::calculateExpiryDate
     */
    public function testCalculateExpiryDate_whenExpiredPro_startsFromNow()
    {
        $pastDate = (new DateTime('-10 days'))->format('Y-m-d H:i:s');

        $expiry = App\Services\MembershipService::calculateExpiryDate(3, $pastDate);

        $expected = (new DateTime())->modify('+3 months');
        $this->assertEqualsWithDelta(
            $expected->getTimestamp(),
            (new DateTime($expiry))->getTimestamp(),
            60,
            'Khi Pro hết hạn, phải tính từ hiện tại'
        );
    }
}
