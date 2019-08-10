<?php

namespace Viviniko\Promotion\Services;

use Viviniko\Promotion\Exceptions\PromotionException;

interface PromotionService
{
    /**
     * Paginate the given query into a simple paginator.
     *
     * @param null $perPage
     * @param array $wheres
     * @param array $orders
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate($perPage, $wheres = [], $orders = []);

    /**
     * Get coupon discount amount.
     *
     * @param  $items
     * @param  string  $code
     * @return float
     * @throws PromotionException
     */
    public function calDiscountAmountByCoupon($items, $code);

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
