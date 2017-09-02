<?php

namespace Viviniko\Promotion\Enums;


class PromotionType
{
    const NONE = 0;
    const COUPON = 1;

    public static function values() {
        return [
            self::NONE => 'No Coupon',
            self::COUPON => 'Specific Coupon',
        ];
    }
}