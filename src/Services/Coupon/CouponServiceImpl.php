<?php

namespace Viviniko\Promotion\Services\Coupon;

use Viviniko\Promotion\Contracts\CouponService;
use Viviniko\Promotion\Models\Coupon;
use Viviniko\Promotion\Repositories\Coupon\CouponRepository;

class CouponServiceImpl implements CouponService
{
    protected $coupons;

    public function __construct(CouponRepository $coupons)
    {
        $this->coupons = $coupons;
    }

    /**
     * @param $code
     * @return Coupon|null
     */
    public function findByCode($code)
    {
        return $this->coupons->findByCode($code);
    }

    public function __call($method, $parameters)
    {
        if (method_exists($this->coupons, $method)) {
            return $this->coupons->$method(...$parameters);
        }

        throw new \BadMethodCallException("Method [{$method}] does not exist.");
    }
}