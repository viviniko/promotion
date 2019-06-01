<?php

namespace Viviniko\Promotion\Repositories\Usage;

use Illuminate\Support\Collection;
use Viviniko\Repository\CrudRepository;

interface UsageRepository extends CrudRepository
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
     * @param $couponId
     * @param $userId
     * @return int
     */
    public function getUsageNumber($couponId, $userId);
}
