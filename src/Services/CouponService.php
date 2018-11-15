<?php

namespace Viviniko\Promotion\Services;

interface CouponService
{
    public function paginate($perPage, $wheres = [], $orders = []);

    /**
     * Get entity by code.
     *
     * @param $code
     * @return mixed
     */
    public function findByCode($code);
}
