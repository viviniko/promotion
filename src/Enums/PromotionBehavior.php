<?php

namespace Viviniko\Promotion\Enums;

class PromotionBehavior
{
    const REGISTER = 'Register';
    const SUBSCRIBER = 'Subscriber';

    public static function values()
    {
        return [
            static::REGISTER => static::REGISTER,
            static::SUBSCRIBER => static::SUBSCRIBER,
        ];
    }
}