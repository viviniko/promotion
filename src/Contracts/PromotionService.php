<?php

namespace Viviniko\Promotion\Contracts;

use Viviniko\Cart\Services\Collection;
use Viviniko\Promotion\Exceptions\PromotionException;

interface PromotionService
{
    /**
     * Format promotion conditions.
     *
     * @param $data
     * @return mixed
     */
    public function formatConditions($data);

    /**
     * Get coupon discount amount.
     *
     * @param  Collection  $items
     * @param  string  $code
     * @return float
     * @throws PromotionException
     */
    public function getCouponDiscountAmount(Collection $items, $code);

    /**
     * Get customer coupons.
     *
     * @param $customerId
     * @return mixed
     */
    public function getUserCoupons($customerId);

    /**
     * Generate coupon.
     *
     * @param $userId
     * @param array $mergeData
     * @param string $event
     * @return mixed
     */
    public function generateCouponByUserEvent($userId, $event, array $mergeData = []);

    /**
     * Generate coupons.
     *
     * @param array $rules
     * @return mixed
     */
    public function generatePromotionCoupons(array $rules = []);
}
