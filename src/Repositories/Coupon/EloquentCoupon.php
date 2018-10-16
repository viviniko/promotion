<?php

namespace Viviniko\Promotion\Repositories\Coupon;

use Illuminate\Support\Facades\Config;
use Viviniko\Promotion\Models\Coupon;
use Viviniko\Repository\EloquentRepository;

class EloquentCoupon extends EloquentRepository implements CouponRepository
{
    protected $fieldSearchable = ['code' => 'like', 'promotion_id'];

    public function __construct()
    {
        parent::__construct(Config::get('promotion.promotion_coupon'));
    }

    /**
     * @param $code
     * @return Coupon|null
     */
    public function findByCode($code)
    {
        return $this->findBy('code', $code);
    }
}