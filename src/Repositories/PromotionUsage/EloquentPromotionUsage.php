<?php

namespace Viviniko\Promotion\Repositories\PromotionUsage;

use Viviniko\Repository\SimpleRepository;

class EloquentPromotionUsage extends SimpleRepository implements PromotionUsageRepository
{
    protected $modelConfigKey = 'promotion.promotion_usage';

    /**
     * Get customer coupon usages.
     *
     * @param $couponId
     * @param $customerId
     * @return mixed
     */
    public function findByCouponIdAndCustomerId($couponId, $customerId)
    {
        return $this->findBy(['coupon_id' => $couponId, 'customer_id' => $customerId]);
    }

    /**
     * {@inheritdoc}
     */
    public function getUsageNumber($couponId, $clientId) {
        return $this->createModel()->newQuery()
            ->where(['coupon_id' => $couponId, 'client_id' => $clientId])
            ->whereNotNUll('used_at')
            ->count();
    }
}