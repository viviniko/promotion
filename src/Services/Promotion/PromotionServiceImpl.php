<?php

namespace Viviniko\Promotion\Services\Promotion;

use Carbon\Carbon;
use Viviniko\Agent\Facades\Agent;
use Viviniko\Cart\Services\Collection;
use Viviniko\Promotion\Contracts\PromotionService;
use Viviniko\Promotion\Enums\CouponFormat;
use Viviniko\Promotion\Enums\CouponType;
use Viviniko\Promotion\Enums\PromotionDiscountConditions;
use Viviniko\Promotion\Exceptions\InvalidCouponException;
use Viviniko\Promotion\Repositories\Coupon\CouponRepository;
use Viviniko\Promotion\Repositories\Promotion\PromotionRepository;
use Viviniko\Promotion\Repositories\PromotionUsage\PromotionUsageRepository;
use Illuminate\Support\Facades\Auth;

class PromotionServiceImpl implements PromotionService
{
    protected $promotions;

    protected $coupons;

    protected $usages;

    public function __construct(PromotionRepository $promotions, CouponRepository $coupons, PromotionUsageRepository $usages)
    {
        $this->promotions = $promotions;
        $this->coupons = $coupons;
        $this->usages = $usages;
    }

    public function formatConditions($dataConditions)
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
     * Get coupon discount amount.
     *
     * @param  Collection  $items
     * @param  string  $couponCode
     * @return float
     * @throws InvalidCouponException
     */
    public function getCouponDiscountAmount(Collection $items, $couponCode)
    {
        $now = new Carbon();
        $customerId = Auth::id();

        if ($couponCode && ($coupon = $this->coupons->findByCode($couponCode)) && ($promotion = $coupon->promotion)) {

            // 开始时间检查
            if (!empty($promotion->start_time) && $now->lt(Carbon::parse($promotion->start_time))) {
                throw new InvalidCouponException('This Coupon has not yet begun.');
            }
            // 结束时间检查
            if (!empty($promotion->end_time) && $now->gt(Carbon::parse($promotion->end_time))) {
                throw new InvalidCouponException('This Coupon has expired.');
            }

            // 总的使用次数检查
            if ($coupon->used_num > $promotion->uses_per_coupon) {
                throw new InvalidCouponException('This coupon has run out.');
            }

            $customerCouponUsages = $this->usages->findByCouponIdAndCustomerId($coupon->id, $customerId);
            if ($customerCouponUsages->isEmpty()) {
                // 用户使用次数检查
                if ($coupon->uses_per_user <= $this->usages->getUsageNumber($coupon->id, Agent::clientId())) {
                    throw new InvalidCouponException('This coupon has run out.');
                }
            } else {
                $customerUsedCount = $customerCouponUsages->filter(function ($item) {
                    return !empty($item->used_at);
                })->count();
                // 用户使用次数检查
                if ($coupon->uses_per_user <= $customerUsedCount) {
                    throw new InvalidCouponException('This coupon has run out.');
                }
                // 用户优惠码时间检查
                $customerEffectiveUsages = $customerCouponUsages->filter(function ($item) use ($now) {
                    if (!empty($item->used_at)) {
                        return false;
                    }
                    if (!empty($item->start_time) && $now->lt(Carbon::parse($item->start_time))) {
                        return false;
                    }
                    if (!empty($item->expire_time) && $now->gt(Carbon::parse($item->expire_time))) {
                        return false;
                    }
                    return true;
                });
                if ($customerEffectiveUsages->isEmpty()) {
                    throw new InvalidCouponException('This Coupon is invalid.');
                }
            }

            $discountAction = new PromotionAction($promotion->discount_action, $promotion->discount_amount, $promotion->discount_conditions);
            $amount = $discountAction->getDiscountAmount($items);
            if (!$amount) {
                throw new InvalidCouponException('This coupon doesn\'t apply for items in the cart.');
            }
            return $amount;
        }

        throw new InvalidCouponException('This Coupon does not exist.');
    }

    /**
     * Get customer coupons.
     *
     * @param $customerId
     * @param bool $invalid
     * @return mixed
     */
    public function getCustomerPromotionCoupons($customerId, $invalid = false)
    {
        return $this->promotions->getCustomerPromotionCoupons($customerId, $invalid);
    }

    /**
     * Generate coupons.
     *
     * @param array $rules
     * @return mixed
     */
    public function generatePromotionCoupons(array $rules = [])
    {
        $rules['prefix'] = trim($rules['prefix'] ?? '');
        $rules['suffix'] = trim($rules['suffix'] ?? '');
        $rules['length'] = ($rules['length'] ?? 8) - strlen($rules['prefix'] . $rules['suffix']);
        $rules['dash'] = $rules['dash'] ?? 0;
        $rules['qty'] = $rules['qty'] ?? 1;
        $rules['format'] = $rules['format'] ?? CouponFormat::ALPHABETICAL;

        $coupons = [];

        while (count($coupons) <  $rules['qty']) {
            $code = CouponFormat::randStr(CouponFormat::getChars($rules['format']), $rules['length'], $rules['dash'], $rules['prefix'], $rules['suffix']);
            if (!$this->coupons->findByCode($code)) {
                $coupons[] = $code;
            }
        }

        return $rules['qty'] == 1 ? $coupons[0] : $coupons;
    }

    public function generateCouponForCustomerAction($action, array $mergeData = [], $customerId = null)
    {
        $code = null;
        if ($promotion = $this->promotions->findBy('behavior', $action)->first()) {
            $code = $this->generatePromotionCoupons();
            $coupon = $this->coupons->create(array_merge([
                'promotion_id' => $promotion->id,
                'code' => $code,
                'usage_limit' => $promotion->uses_per_coupon,
                'uses_per_user' => $promotion->uses_per_user,
                'type' => CouponType::SLAVE,
            ], $mergeData));
            if ($customerId) {
                $this->usages->create(array_merge([
                    'customer_id' => $customerId,
                    'coupon_id' => $coupon->id,
                    'client_id' => Agent::clientId(),
                    'start_time' => new Carbon(),
                    'expire_time' => (new Carbon())->addDay(),
                    'used_at' => null,
                    'created_at' => new Carbon(),
                ], $mergeData));
            }
        }

        return $code;
    }

    public function __call($method, $parameters)
    {
        if (method_exists($this->promotions, $method)) {
            return $this->promotions->$method(...$parameters);
        }

        throw new \BadMethodCallException("Method [{$method}] does not exist.");
    }
}