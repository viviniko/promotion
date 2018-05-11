<?php

namespace Viviniko\Promotion\Enums;


class PromotionType
{
    const NONE = 0;
    const COUPON = 1;
    const REGISTER = 2;
    const SUBSCRIBER = 3;

    public static function values() {
        return [
            self::NONE => 'No Coupon',
            self::COUPON => 'Specific Coupon',
            self::REGISTER => 'Register',
            self::SUBSCRIBER => 'Subscriber',
        ];
    }
}