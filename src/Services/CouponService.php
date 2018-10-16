<?php

namespace Viviniko\Promotion\Services;

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
