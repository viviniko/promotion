<?php

namespace Viviniko\Promotion\Models;

use \Viviniko\Currency\Money;
use Viviniko\Promotion\Enums\CouponType;
use Viviniko\Promotion\Enums\PromotionDiscountAction;
use Viviniko\Promotion\Enums\PromotionDiscountConditions;
use Viviniko\Support\Database\Eloquent\Model;

class Promotion extends Model
{
    protected $tableConfigKey = 'promotion.promotions_table';

    protected $fillable = ['title', 'description', 'is_active', 'event', 'discount_conditions', 'discount_action',
        'discount_amount', 'start_time', 'end_time', 'type', 'auto_gen_coupon', 'uses_per_user', 'uses_per_coupon'];

    protected $appends = ['coupon_code'];

    protected $casts = [
        'discount_conditions' => 'array',
        'uses_per_user' => 'integer',
        'uses_per_coupon' => 'integer',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'type' => 'integer',
        'is_active' => 'boolean',
    ];

    public function setDiscountConditionsAttribute($value)
    {
        $this->attributes['discount_conditions'] = json_encode(self::formatConditions($value));
    }

    public function coupons()
    {
        return $this->hasMany(Coupon::class);
    }

    public function getCouponCodeAttribute()
    {
        $coupon = $this->coupons->filter(function ($coupon) {
            return $coupon->type == CouponType::MASTER;
        })->first();
        return $coupon ? $coupon->code : null;
    }

    public function getDiscountActionTextAttribute()
    {
        return PromotionDiscountAction::values()[$this->attributes['discount_action']];
    }

    public function discount($cart)
    {
        $discountItems = [];
        $conditions = self::parseConditions($this->discount_conditions);
        foreach ($cart->getItems() as $item) {
            if (self::_testItem($item, $conditions, $cart)) {
                $discountItems[] = $item;
            }
        }

        $discount = Money::create(0);
        switch ($this->discount_action) {
            case PromotionDiscountAction::PRODUCT_PERCENT:
                foreach ($discountItems as $item) {
                    $discount = $discount->add($item->subtotal->mul($this->discount_amount / 100));
                }
                break;
            case PromotionDiscountAction::PRODUCT_AMOUNT:
                $discount = $discount->add(Money::create($this->discount_amount * count($discountItems)));
                break;
            case PromotionDiscountAction::CART_AMOUNT:
                $discount = Money::create($this->discount_amount);
                break;
            case PromotionDiscountAction::CART_PERCENT:
                $discount = $cart->subtotal->mul($this->discount_amount / 100);
                break;
            default:
                break;
        }

        return $discount;
    }

    public static function formatConditions($dataConditions)
    {
        $conditions = [];
        if (is_null($dataConditions['operation']) || !isset(PromotionDiscountConditions::$operations[$dataConditions['operation']])) {
            return $conditions;
        }
        $conditions['operation'] = $dataConditions['operation'];
        if (!isset($dataConditions['rules']) || !is_array($dataConditions['rules'])) {
            return $conditions;
        }
        $conditions['rules'] = [];
        foreach ($dataConditions['rules'] as $rule) {
            if (!is_null($rule['expression']['item'])) {
                array_walk($rule['expression'], function (&$item) { $item = trim($item); });
                if (isset(PromotionDiscountConditions::$conditionItems[$rule['expression']['item']])) {
                    $conditions['rules'][]['expression'] = $rule['expression'];
                } else if (!empty($rule['condition'])) {
                    $children = self::formatConditions($rule['condition']);
                    if (!empty($children)) {
                        $conditions['rules'][]['condition'] = $children;
                    }
                }
            }
        }
        return $conditions;
    }

    /**
     * Parse conditions.
     * [ 'all_true' => [ [ 'eq', 'game_gold_id', '23' ], 'any_true' => [ ['eq', 'category_id', '2'], [...] ] ] ]
     *
     * @param array $data
     *
     * @return array
     */
    public static function parseConditions($data) {
        $conditions = null;
        if (isset($data['operation']) && isset(PromotionDiscountConditions::$operations[$data['operation']])) {
            $conditions[$data['operation']] = [];
            if (!empty($data['rules'])) {
                foreach($data['rules'] as $item) {
                    if (!empty($item['expression'])) {
                        $conditions[$data['operation']][] = [ $item['expression']['exp'], $item['expression']['item'], $item['expression']['value'] ];
                    } else if (!empty($item['condition'])) {
                        $conditions[$data['operation']] = self::mergeOperations($conditions[$data['operation']], self::parseConditions($item['condition']));
                    }
                }
            }
        }
        return $conditions;
    }

    protected static function _testItem($item, $conditions, $cart) {
        $operation = key($conditions);
        $conditions = $conditions[$operation];

        if (empty($conditions)) {
            return true;
        }
        foreach ($conditions as $key => $condition) {
            $result = null;
            if (isset(PromotionDiscountConditions::$operations[$key])) {
                $result = self::_testItem($item, [$key => $condition], $cart);
            } else if (isset(PromotionDiscountConditions::$conditionExps[$condition[0]])) {
                switch ($condition[1]) {
                    case PromotionDiscountConditions::CONDITION_ITEM_CATEGORY:
                        // 产品类型
                        $result = self::_testExp($condition[0], $item->category_id, $condition[2]);
                        break;
                    case PromotionDiscountConditions::CONDITION_ITEM_CART_AMOUNT:
                        // 购物车金额小计
                        $result = self::_testExp($condition[0], $cart->subtotal, $condition[2]);
                        break;
                }
            }
            if ($result) {
                if ($operation == PromotionDiscountConditions::IF_ANY_TRUE)
                    return true;
            } else {
                if ($operation == PromotionDiscountConditions::IF_ALL_TRUE)
                    return false;
            }
        }

        // 当条件都不满足时
        switch ($operation) {
            case PromotionDiscountConditions::IF_ALL_TRUE:
                return true;
                break;
            case PromotionDiscountConditions::IF_ANY_TRUE:
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
            case PromotionDiscountConditions::CONDITION_EXP_EQ:
                return in_array($a, $b);
                break;
            case PromotionDiscountConditions::CONDITION_EXP_GE:
                return $a >= max($b);
                break;
            case PromotionDiscountConditions::CONDITION_EXP_LT:
                return $a < min($b);
                break;
            case PromotionDiscountConditions::CONDITION_EXP_NE:
                return !in_array($a, $b);
                break;
            default:
                break;
        }

        return false;
    }

    protected static function mergeOperations($operations, $data) {
        foreach (array_keys(PromotionDiscountConditions::$operations) as $oper) {
            if (isset($data[$oper])) {
                $operations[$oper] = isset($operations[$oper]) ? array_merge($operations[$oper], $data[$oper]) : $data[$oper];
            }
        }
        return $operations;
    }
}