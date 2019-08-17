<?php

namespace Viviniko\Promotion\Models;

use Viviniko\Support\Database\Eloquent\Model;

class Usage extends Model
{
    protected $tableConfigKey = 'promotion.promotion_usages_table';

    public $timestamps = false;

    protected $fillable = ['promotion_id', 'coupon_id', 'user_id', 'created_at', 'discount_amount'];

}