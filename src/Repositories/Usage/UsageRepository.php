<?php

namespace Viviniko\Promotion\Repositories\Usage;

use Illuminate\Support\Collection;

interface UsageRepository
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
     * @param $couponId
     * @param $userId
     * @return int
     */
    public function getUsageNumber($couponId, $userId);
}
