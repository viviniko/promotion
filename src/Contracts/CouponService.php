<?php

namespace Viviniko\Promotion\Contracts;

interface CouponService
{
    /**
     * Get entity by code.
     *
     * @param $code
     * @return mixed
     */
    public function findByCode($code);
}
