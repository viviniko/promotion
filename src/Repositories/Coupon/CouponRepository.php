<?php

namespace Viviniko\Promotion\Repositories\Coupon;

interface CouponRepository
{
    /**
     * Find data by coupon code.
     *
     * @param $code
     * @return mixed
     */
    public function findByCode($code);
}