<?php

namespace Viviniko\Promotion\Repositories\UserCoupon;

use Illuminate\Support\Collection;

interface UserCouponRepository
{
    /**
     * Find usage by its id.
     *
     * @param $id
     * @return mixed
     */
    public function find($id);

    /**
     * Create new usage.
     *
     * @param array $data
     * @return mixed
     */
    public function create(array $data);

    /**
     * Get customer coupon usages.
     *
     * @param $couponId
     * @param $userId
     * @return Collection
     */
    public function findByCouponIdAndUserId($couponId, $userId);

    /**
     * Get customer coupons.
     *
     * @param $userId
     * @return Collection
     */
    public function findByUserId($userId);

    /**
     * @param $couponId
     * @return bool
     */
    public function existsCouponId($couponId);
}
