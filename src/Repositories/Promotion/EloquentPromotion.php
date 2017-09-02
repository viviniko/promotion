<?php

namespace Viviniko\Promotion\Repositories\Promotion;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Viviniko\Repository\SimpleRepository;
use Illuminate\Support\Facades\Config;

class EloquentPromotion extends SimpleRepository implements PromotionRepository
{
    use ValidatesPromotionData;

    protected $modelConfigKey = 'promotion.promotion';

    protected $fieldSearchable = ['title', 'coupon_code', 'start_time' => 'between', 'end_time' => 'between', 'discount_action', 'is_active'];

    /**
     * Search.
     *
     * @param $keywords
     *
     * @return Builder
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

    public function getCustomerPromotionCoupons($customerId, $invalid = false)
    {
        $promotionTable = Config::get('promotion.promotions_table');
        $couponTable = Config::get('promotion.promotion_coupons_table');
        $usageTable = Config::get('promotion.promotion_usages_table');

        return $this->createModel()->newQuery()
            ->select(["{$couponTable}.code", "{$promotionTable}.discount_action", "{$promotionTable}.discount_amount", "{$usageTable}.start_time", "{$usageTable}.expire_time"])
            ->join($couponTable, "{$promotionTable}.id", '=', "{$couponTable}.promotion_id")
            ->join($usageTable, "{$usageTable}.coupon_id", '=', "{$couponTable}.id")
            ->where("{$usageTable}.customer_id", $customerId)
            ->where(function ($query) use ($usageTable, $invalid) {
                $now = new Carbon();
                if ($invalid) {
                    $query->whereNotNull("{$usageTable}.used_at")->orWhere("{$usageTable}.expire_time", '<=', $now);
                } else {
                    $query->whereNull("{$usageTable}.used_at")->Where("{$usageTable}.expire_time", '>', $now);
                }
            })
            ->get();
    }
}