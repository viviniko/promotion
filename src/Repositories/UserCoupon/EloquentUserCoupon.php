<?php

namespace Viviniko\Promotion\Repositories\UserCoupon;

use Illuminate\Support\Facades\Config;
use Viviniko\Repository\EloquentRepository;

class EloquentUserCoupon extends EloquentRepository implements UserCouponRepository
{
    public function __construct()
    {
        parent::__construct(Config::get('promotion.promotion_user_coupon'));
    }

    /**
     * {@inheritdoc}
     */
    public function findByCouponIdAndUserId($couponId, $userId)
    {
        return $this->findBy(['coupon_id' => $couponId, 'user_id' => $userId]);
    }

    /**
     * {@inheritdoc}
     */
    public function findAllByUserId($userId)
    {
        return $this->findAllBy(['user_id' => $userId]);
    }

    /**
     * {@inheritdoc}
     */
    public function existsCouponId($couponId)
    {
        return $this->exists('coupon_id', $couponId);
    }
}