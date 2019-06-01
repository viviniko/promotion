<?php

namespace Viviniko\Promotion\Repositories\Coupon;

use Viviniko\Repository\CrudRepository;

interface CouponRepository extends CrudRepository
{
    /**
     * Find data by coupon code.
     *
     * @param $code
     * @return mixed
     */
    public function findByCode($code);
}