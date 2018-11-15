<?php

namespace Viviniko\Promotion\Services\Impl;

use Viviniko\Cart\Collection;

class PromotionCondition {

    const IF_ALL_TRUE   = 'all_true';
    const IF_ANY_TRUE   = 'any_true';

    /**
     * @var array
     */
    private static $operations = [
        self::IF_ALL_TRUE => '当满足所有条件时',
        self::IF_ANY_TRUE => '当满足条件之一时',
    ];

    const CONDITION_ITEM_PRODUCT        = 'product_id';
    const CONDITION_ITEM_CATEGORY       = 'category_id';
    const CONDITION_ITEM_CART_AMOUNT    = 'cart_amount';

    /**
     * @var array
     */
    private static $conditionItems = [
        self::CONDITION_ITEM_PRODUCT            => '产品ID',
        self::CONDITION_ITEM_CATEGORY           => '类别ID',
        self::CONDITION_ITEM_CART_AMOUNT        => '购物车金额小计',
    ];

    const CONDITION_EXP_EQ  = 'eq';
    const CONDITION_EXP_NE  = 'ne';
    const CONDITION_EXP_LT  = 'lt';
    const CONDITION_EXP_GE  = 'ge';

    private static $conditionExps = [
        self::CONDITION_EXP_EQ => '等于',
        self::CONDITION_EXP_NE => '不等于',
        self::CONDITION_EXP_LT => '小于',
        self::CONDITION_EXP_GE => '大于等于',
    ];

    /**
     * @var array
     */
    private $data = [];

    /**
     * PromotionDiscountConditions constructor.
     * @param $conditions
     */
    public function __construct($conditions) {
        $this->data = $this->parseData($conditions);
    }

    /**
     * @var \Viviniko\Cart\Collection
     */
    private $items;

    /**
     * 设置购物车商品
     *
     * @param Collection $cartItems 产品
     *
     * @return $this
     */
    public function setCartItems(Collection $cartItems)
    {
        $this->items = $cartItems;
        return $this;
    }


    /**
     * 寻找符合条件的商品
     *
     * @return array
     */
    public function find()
    {
        $products = [];

        foreach ($this->items as $cart) {
            if ($this->_testCartItem($this->data, $cart)) {
                $products[] = $cart;
            }
        }

        return $products;
    }

    private function _testCartItem($conditions, $cart) {
        $operation = key($conditions);
        $conditions = $conditions[$operation];

        if (empty($conditions)) {
            return true;
        }

        foreach ($conditions as $key => $condition) {
            $result = null;
            if (isset(self::$operations[$key])) {
                $result = self::_testCartItem([$key => $condition], $cart);
            } else if (isset(self::$conditionExps[$condition[0]]) && isset(self::$conditionItems[$condition[1]])) {
                $value = null;
                switch ($condition[1]) {
                    case self::CONDITION_ITEM_PRODUCT:
                        // 产品类型
                        $result = self::_testExp($condition[0], $cart->product_id, $condition[2]);
                        break;
                    case self::CONDITION_ITEM_CATEGORY:
                        // 产品类型
                        $result = self::_testExp($condition[0], $cart->category_id, $condition[2]);
                        break;
                    case self::CONDITION_ITEM_CART_AMOUNT:
                        // 购物车金额小计
                        $result = self::_testExp($condition[0], $this->items->getSubtotal(), $condition[2]);
                        break;
                }
            }
            if ($result) {
                if ($operation == self::IF_ANY_TRUE)
                    return true;
            } else {
                if ($operation == self::IF_ALL_TRUE)
                    return false;
            }
        }

        // 当条件都不满足时
        switch ($operation) {
        case self::IF_ALL_TRUE:
            return true;
            break;
        case self::IF_ANY_TRUE:
            return false;
            break;
        }
        return false;
    }

    protected static function _testExp($exp, $a, $b) {
        $b = is_array($b) ? $b : preg_split('/[^\d]+/', $b);
        if (empty($b))
            return false;

        switch($exp) {
        case self::CONDITION_EXP_EQ:
            return in_array($a, $b);
            break;
        case self::CONDITION_EXP_GE:
            return $a >= max($b);
            break;
        case self::CONDITION_EXP_LT:
            return $a < min($b);
            break;
        case self::CONDITION_EXP_NE:
            return !in_array($a, $b);
            break;
        default:
            break;
        }

        return false;
    }

    /**
     * 将用户提交的数据解析为容易处理的数据
     * 格式如：[ 'all_true' => [ [ 'eq', 'game_gold_id', '23' ], 'any_true' => [ ['eq', 'category_id', '2'], [...] ] ] ]
     *
     * @param array $data
     *
     * @return array
     */
    public function parseData($data) {
        $conditions = null;
        if (isset($data['operation']) && isset(self::$operations[$data['operation']])) {
            $conditions[$data['operation']] = [];
            if (!empty($data['rules'])) {
                foreach($data['rules'] as $item) {
                    if (!empty($item['expression'])) {
                        $conditions[$data['operation']][] = [ $item['expression']['exp'], $item['expression']['item'], $item['expression']['value'] ];
                    } else if (!empty($item['condition'])) {
                        $conditions[$data['operation']] = self::mergeOperations($conditions[$data['operation']], $this->parseData($item['condition']));
                    }
                }
            }
        }
        return $conditions;
    }

    public static function FormatData($data) {
        $conditions = [];
        if (isset($data['operation']) && isset(self::$operations[$data['operation']])) {
            $conditions['operation'] = $data['operation'];
            if (isset($data['rules']) && is_array($data['rules'])) {
                $conditions['rules'] = [];
                foreach($data['rules'] as $item) {
                    if (!empty($item['expression']['item'])) {
                        array_walk($item['expression'], function(&$val, $key) { $val = trim($val); });
                        if (isset(self::$conditionItems[$item['expression']['item']])) {
                            $conditions['rules'][]['expression'] = $item['expression'];
                        }
                    } else if (!empty($item['condition'])) {
                        $children = self::FormatData($item['condition']);
                        if (!empty($children)) {
                            $conditions['rules'][]['condition'] = $children;
                        }
                    }
                }
            }
        }
        return $conditions;
    }

    protected static function mergeOperations($operations, $data) {
        foreach (array_keys(self::$operations) as $oper) {
            if (isset($data[$oper])) {
                $operations[$oper] = isset($operations[$oper]) ? array_merge($operations[$oper], $data[$oper]) : $data[$oper];
            }
        }
        return $operations;
    }

    public static function getOperations() {
        return self::$operations;
    }

    public static function getConditionItems() {
        return self::$conditionItems;
    }

    public static function getConditionExps() {
        return self::$conditionExps;
    }

    public static function createFromJson($json) {
        return new static(json_decode($json));
    }

    public function getData() {
        return $this->data;
    }

    public function toJson() {
        return json_encode($this->data);
    }

    public function __toString() {
        return $this->toJson();
    }

}