<?php

namespace Viviniko\Promotion\Repositories\Usage;

use Illuminate\Support\Facades\Config;
use Viviniko\Repository\EloquentRepository;

class EloquentUsage extends EloquentRepository implements UsageRepository
{
    public function __construct()
    {
        parent::__construct(Config::get('promotion.promotion_usage'));
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
    public function getUsageNumber($couponId, $userId) {
        return $this->count(['coupon_id' => $couponId, 'user_id' => $userId]);
    }
}