<?php

namespace Viviniko\Promotion\Repositories\Promotion;

interface PromotionRepository
{
    /**
     * Get customer coupons.
     *
     * @param $customerId
     * @param bool $invalid
     * @return mixed
     */
    public function getCustomerPromotionCoupons($customerId, $invalid = false);
}