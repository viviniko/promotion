<?php

namespace Viviniko\Promotion\Services\Impl;

use Viviniko\Promotion\Models\Coupon;
use Viviniko\Promotion\Repositories\Coupon\CouponRepository;
use Viviniko\Promotion\Services\CouponService;
use Viviniko\Repository\SearchPageRequest;

class CouponServiceImpl implements CouponService
{
    protected $coupons;

    public function __construct(CouponRepository $coupons)
    {
        $this->coupons = $coupons;
    }

    /**
     * {@inheritdoc}
     */
    public function paginate($perPage, $wheres = [], $orders = [])
    {
        return $this->coupons->search(
            SearchPageRequest::create($perPage, $wheres, $orders)
                ->rules(['code' => 'like'])
                ->request(request(), 'search')
        );
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