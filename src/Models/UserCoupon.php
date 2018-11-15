<?php

namespace Viviniko\Promotion\Models;

use Illuminate\Support\Facades\Config;
use Viviniko\Support\Database\Eloquent\Model;

class UserCoupon extends Model
{
    protected $tableConfigKey = 'promotion.promotion_user_coupon_table';

    public $timestamps = false;

    protected $fillable = ['coupon_id', 'user_id', 'start_time', 'expire_time', 'description'];

    public function coupon()
    {
        return $this->belongsTo(Config::get('promotion.promotion_coupon'), 'coupon_id');
    }
}