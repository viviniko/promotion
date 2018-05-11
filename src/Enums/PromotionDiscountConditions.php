<?php

namespace Viviniko\Promotion\Enums;


class PromotionDiscountConditions
{
    const IF_ALL_TRUE   = 'all_true';
    const IF_ANY_TRUE   = 'any_true';

    public static $operations = [
        self::IF_ALL_TRUE => '当满足所有条件时',
        self::IF_ANY_TRUE => '当满足条件之一时',
    ];

    const CONDITION_ITEM_CATEGORY       = 'category_id';
    const CONDITION_ITEM_CATEGORY_PRICE = 'category_price';
    const CONDITION_ITEM_CART_AMOUNT    = 'cart_amount';

    const CONDITION_ITEM_TYPE_PRODUCT   = 'product';
    const CONDITION_ITEM_TYPE_CATEGORY  = 'category';
    const CONDITION_ITEM_TYPE_CART      = 'cart';

    /**
     * @var array
     */
    public static $conditionItems = [
        self::CONDITION_ITEM_CATEGORY           => '类别ID',
        self::CONDITION_ITEM_CART_AMOUNT        => '购物车金额',
    ];

    const CONDITION_EXP_EQ  = 'eq';
    const CONDITION_EXP_NE  = 'ne';
    const CONDITION_EXP_LT  = 'lt';
    const CONDITION_EXP_GE  = 'ge';

    public static $conditionExps = [
        self::CONDITION_EXP_EQ => '等于',
        self::CONDITION_EXP_NE => '不等于',
        self::CONDITION_EXP_LT => '小于',
        self::CONDITION_EXP_GE => '大于等于',
    ];
}