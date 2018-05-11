<?php

namespace Viviniko\Promotion\Repositories\UserCoupon;

use Illuminate\Support\Collection;
use Viviniko\Repository\SimpleRepository;

class EloquentUserCoupon extends SimpleRepository implements UserCouponRepository
{
    protected $modelConfigKey = 'promotion.promotion_user_coupon';

    /**
     * {@inheritdoc}
     */
    public function findByCouponIdAndUserId($couponId, $userId)
    {
        return $this->createModel()->where(['coupon_id' => $couponId, 'user_id' => $userId])->first();
    }

    /**
     * {@inheritdoc}
     */
    public function findByUserId($userId)
    {
        return $this->findBy(['user_id' => $userId]);
    }
}