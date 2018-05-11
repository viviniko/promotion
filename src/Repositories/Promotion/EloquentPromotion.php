<?php

namespace Viviniko\Promotion\Repositories\Promotion;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Viviniko\Repository\SimpleRepository;
use Illuminate\Support\Facades\Config;

class EloquentPromotion extends SimpleRepository implements PromotionRepository
{
    protected $modelConfigKey = 'promotion.promotion';

    protected $fieldSearchable = ['title', 'coupon_code', 'start_time' => 'between', 'end_time' => 'between', 'discount_action', 'is_active'];

    /**
     * {@inheritdoc}
     */
    public function search($keywords)
    {
        if (isset($keywords['coupon_code'])) {
            $coupon_code = $keywords['coupon_code'];
            unset($keywords['coupon_code']);
            $promotionTable = Config::get('promotion.promotions_table');
            $couponTable = Config::get('promotion.promotion_coupons_table');
            $builder = parent::search($keywords);
            $builder = $builder->join($couponTable, "{$couponTable}.promotion_id", '=', "{$promotionTable}.id")
                ->whereRaw("({$couponTable}.type='1' AND {$couponTable}.code = '{$coupon_code}')");
            return $builder;
        }

        return parent::search($keywords);
    }
}