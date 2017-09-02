<?php

namespace Viviniko\Promotion\Models;

use Viviniko\Support\Database\Eloquent\Model;

class PromotionUsage extends Model
{
    protected $tableConfigKey = 'promotion.promotion_usages_table';

    public $timestamps = false;

    protected $fillable = ['coupon_id', 'customer_id', 'client_id', 'created_at', 'used_at', 'start_time', 'expire_time'];
}