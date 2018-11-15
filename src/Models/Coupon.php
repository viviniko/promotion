<?php

namespace Viviniko\Promotion\Models;

use Viviniko\Support\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $tableConfigKey = 'promotion.promotion_coupons_table';

    protected $fillable = ['code', 'usage_limit', 'uses_per_user', 'used_num', 'total_amount', 'type', 'promotion_id', 'status'];

    public function promotion()
    {
        return $this->belongsTo(Promotion::class, 'promotion_id');
    }
}