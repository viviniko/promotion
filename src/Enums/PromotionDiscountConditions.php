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

    const CONDITION_ITEM_GOLD           = 'game_gold_id';
    const CONDITION_ITEM_GOODS          = 'game_goods_id';
    const CONDITION_ITEM_TASK           = 'game_task_id';
    const CONDITION_ITEM_ACCOUNT        = 'game_account_id';
    const CONDITION_ITEM_GROUPON        = 'game_groupon_id';
    const CONDITION_ITEM_LEVELING       = 'game_leveling_id';
    const CONDITION_ITEM_GOLD_PRICE     = 'game_gold_price';
    const CONDITION_ITEM_GOODS_PRICE    = 'game_goods_price';
    const CONDITION_ITEM_TASK_PRICE     = 'game_task_price';
    const CONDITION_ITEM_ACCOUNT_PRICE  = 'game_account_price';
    const CONDITION_ITEM_GROUPON_PRICE  = 'game_groupon_price';
    const CONDITION_ITEM_LEVELING_PRICE = 'game_leveling_price';
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
        self::CONDITION_ITEM_GOLD_PRICE         => '金币金额',
        self::CONDITION_ITEM_GOODS_PRICE        => '物品金额',
        self::CONDITION_ITEM_LEVELING_PRICE     => '代练金额',
        self::CONDITION_ITEM_CART_AMOUNT        => '购物车金额',
        self::CONDITION_ITEM_GOLD               => '金币ID',
        self::CONDITION_ITEM_GOODS              => '物品ID',
        self::CONDITION_ITEM_LEVELING           => '代练ID',
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