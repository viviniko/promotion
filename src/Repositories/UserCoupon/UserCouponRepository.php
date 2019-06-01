<?php

namespace Viviniko\Promotion\Repositories\UserCoupon;

use Illuminate\Support\Collection;
use Viviniko\Repository\CrudRepository;

interface UserCouponRepository extends CrudRepository
{
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
    public function findAllByUserId($userId);

    /**
     * @param $couponId
     * @return bool
     */
    public function existsCouponId($couponId);
}
