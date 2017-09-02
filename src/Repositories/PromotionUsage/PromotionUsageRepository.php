<?php

namespace Viviniko\Promotion\Repositories\PromotionUsage;

use Illuminate\Support\Collection;

interface PromotionUsageRepository
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
     * @param $customerId
     * @return Collection
     */
    public function findByCouponIdAndCustomerId($couponId, $customerId);

    /**
     * @param $couponId
     * @param $clientId
     * @return int
     */
    public function getUsageNumber($couponId, $clientId);
}
