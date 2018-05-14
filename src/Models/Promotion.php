<?php

namespace Viviniko\Promotion\Models;

use Viviniko\Promotion\Enums\CouponType;
use Viviniko\Promotion\Enums\PromotionDiscountAction;
use Viviniko\Support\Database\Eloquent\Model;

class Promotion extends Model
{
    protected $tableConfigKey = 'promotion.promotions_table';

    protected $fillable = ['title', 'description', 'is_active', 'event', 'discount_conditions', 'discount_action',
        'discount_amount', 'start_time', 'end_time', 'type', 'auto_gen_coupon', 'uses_per_user', 'uses_per_coupon'];

    protected $appends = ['coupon_code'];

    protected $casts = [
        'discount_conditions' => 'array',
        'uses_per_user' => 'integer',
        'uses_per_coupon' => 'integer',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'type' => 'integer',
        'is_active' => 'boolean',
    ];

    public function setDiscountConditionsAttribute($value)
    {
        $this->attributes['discount_conditions'] = json_encode($value);
    }

    public function coupons()
    {
        return $this->hasMany(Coupon::class);
    }

    public function getCouponCodeAttribute()
    {
        $coupon = $this->coupons->filter(function ($coupon) {
            return $coupon->type == CouponType::MASTER;
        })->first();
        return $coupon ? $coupon->code : null;
    }

    public function getDiscountActionTextAttribute()
    {
        return PromotionDiscountAction::values()[$this->attributes['discount_action']];
    }
}