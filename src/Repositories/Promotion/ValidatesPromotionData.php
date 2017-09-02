<?php

namespace Viviniko\Promotion\Repositories\Promotion;

use Viviniko\Promotion\Enums\CouponType;
use Viviniko\Promotion\Models\Promotion;
use Viviniko\Support\ValidatesData;

trait ValidatesPromotionData
{
    use ValidatesData;

    public function validateCreateData($data)
    {
        $this->validate($data, $this->rules());
    }

    public function validateUpdateData($promotionId, $data)
    {
        $this->validate($data, $this->rules($promotionId));
    }

    public function rules($promotionId = null)
    {
        $data = [
            'title' => 'required',
            'is_active' => 'required',
            'coupon_type' => 'required',
            'coupon_code' => 'nullable|regex:/^[a-z0-9_-]+$/i|unique:' . config('promotion.promotion_coupons_table') . ',code',
            'uses_per_coupon' => 'nullable|integer',
            'uses_per_user' => 'nullable|integer',
            'discount_amount' => 'required',
        ];
        if ($promotionId) {
            $promotion = Promotion::find($promotionId);
            $coupon = $promotion->coupons->filter(function ($coupon) {
                return $coupon->type == CouponType::MASTER;
            })->first();
            if ($coupon) {
                $data['coupon_code'] = 'nullable|regex:/^[a-z0-9_-]+$/i|unique:' . config('promotion.promotion_coupons_table') . ',code,' . $coupon->id;
            }
        }
        return $data;
    }
}