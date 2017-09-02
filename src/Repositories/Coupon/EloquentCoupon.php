<?php

namespace Viviniko\Promotion\Repositories\Coupon;

use Viviniko\Promotion\Models\Coupon;
use Viviniko\Repository\SimpleRepository;

class EloquentCoupon extends SimpleRepository implements CouponRepository
{
    protected $modelConfigKey = 'promotion.promotion_coupon';

    protected $fieldSearchable = ['code' => 'like', 'promotion_id'];

    /**
     * @param $code
     * @return Coupon|null
     */
    public function findByCode($code)
    {
        return $this->createModel()->where('code', $code)->first();
    }
}