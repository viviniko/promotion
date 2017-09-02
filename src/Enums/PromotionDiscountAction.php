<?php

namespace Viviniko\Promotion\Enums;

class PromotionDiscountAction
{
    const CART_PERCENT      = 1;
    const CART_AMOUNT       = 2;
    const PRODUCT_PERCENT   = 3;
    const PRODUCT_AMOUNT    = 4;

    public static function values()
    {
        return [
            static::CART_PERCENT => '按照购物车金额比例优惠',
            static::CART_AMOUNT => '按照购物车金额优惠',
            static::PRODUCT_PERCENT => '按照产品金额比例优惠',
            static::PRODUCT_AMOUNT => '按照产品金额优惠',
        ];
    }
}