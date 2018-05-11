<?php

namespace Viviniko\Promotion\Models;

use Viviniko\Support\Database\Eloquent\Model;

class UserCoupon extends Model
{
    protected $tableConfigKey = 'promotion.promotion_user_coupon_table';

    public $timestamps = false;

    protected $fillable = ['coupon_id', 'user_id', 'start_time', 'expire_time', 'description'];
}