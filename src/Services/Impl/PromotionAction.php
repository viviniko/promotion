<?php

namespace Viviniko\Promotion\Services\Impl;

use Viviniko\Cart\Collection;

class PromotionAction {

    const CART_PERCENT      = 1;
    const CART_AMOUNT       = 2;
    const PRODUCT_PERCENT   = 3;
    const PRODUCT_AMOUNT    = 4;

    private static $actions = [
        self::CART_PERCENT => '按照购物车金额比例优惠',
        self::CART_AMOUNT => '按照购物车金额优惠',
        self::PRODUCT_PERCENT => '按照产品金额比例优惠',
        self::PRODUCT_AMOUNT => '按照产品金额优惠',
    ];

    public static function lists() {
        return static::$actions;
    }

    protected $action;

    protected $amount;

    /**
     * @var PromotionCondition
     */
    protected $discountCondition;

    public function __construct($action, $amount, $conditions) {
        $this->action = $action;
        $this->amount = $amount;
        $this->discountCondition = new PromotionCondition($conditions);
    }

    /**
     * 获取优惠后的金额
     *
     * @param Collection $cartItems
     *
     * @return float
     */
    public function getDiscountAmount(Collection $cartItems) {
        $amount = false;
        $items = $this->discountCondition->setCartItems($cartItems)->find();
        if (count($items) > 0) {
            switch ($this->action) {
            case self::PRODUCT_PERCENT:
            case self::PRODUCT_AMOUNT:
                foreach ($items as $item) {
                    if ($this->action == self::PRODUCT_PERCENT) {
                        $amount += $item->price * $item->qty * ($this->amount / 100);
                    } else {
                        $amount += $this->amount;
                    }
                }
                break;
            case self::CART_AMOUNT:
                $amount = $this->amount;
                break;
            case self::CART_PERCENT:
                $amount = $cartItems->getSubtotal() * ($this->amount / 100);
                break;
            default:
                $amount = 0;
                break;
            }
        }
        return $amount;
    }

}