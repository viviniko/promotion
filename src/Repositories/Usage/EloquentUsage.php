<?php

namespace Viviniko\Promotion\Repositories\Usage;

use Viviniko\Repository\SimpleRepository;

class EloquentUsage extends SimpleRepository implements UsageRepository
{
    protected $modelConfigKey = 'promotion.promotion_usage';

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
    public function getUsageNumber($couponId, $userId) {
        return $this->createModel()->newQuery()
            ->where(['coupon_id' => $couponId, 'user_id' => $userId])
            ->count();
    }
}