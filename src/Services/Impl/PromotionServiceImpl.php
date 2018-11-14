<?php

namespace Viviniko\Promotion\Services\Impl;

use Carbon\Carbon;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Viviniko\Cart\Services\Collection;
use Viviniko\Promotion\Enums\CouponFormat;
use Viviniko\Promotion\Enums\CouponType;
use Viviniko\Promotion\Enums\PromotionDiscountConditions;
use Viviniko\Promotion\Exceptions\InvalidCouponException;
use Viviniko\Promotion\Repositories\Coupon\CouponRepository;
use Viviniko\Promotion\Repositories\Promotion\PromotionRepository;
use Viviniko\Promotion\Repositories\Usage\UsageRepository;
use Viviniko\Promotion\Repositories\UserCoupon\UserCouponRepository;
use Viviniko\Promotion\Services\PromotionService;
use Viviniko\Repository\SearchPageRequest;

class PromotionServiceImpl implements PromotionService
{
    protected $promotions;

    protected $coupons;

    protected $userCoupons;

    protected $usages;

    public function __construct(PromotionRepository $promotions, CouponRepository $coupons, UserCouponRepository $userCoupons, UsageRepository $usages)
    {
        $this->promotions = $promotions;
        $this->coupons = $coupons;
        $this->userCoupons = $userCoupons;
        $this->usages = $usages;
    }

    /**
     * {@inheritdoc}
     */
    public function paginate($perPage, $wheres = [], $orders = [])
    {
        $request = request();
        $couponCode = $wheres['coupon_code'] ?? $request->get('search.coupon_code');
        return $this->promotions->search(
            SearchPageRequest::create($perPage, $wheres, $orders)
                ->rules(['title', 'coupon_code', 'start_time' => 'between', 'end_time' => 'between', 'discount_action', 'is_active'])
                ->request($request, 'search')
                ->filter(function ($builder) use ($couponCode) {
                    if (empty($couponCode)) return $builder;
                    $promotionTable = Config::get('promotion.promotions_table');
                    $couponTable = Config::get('promotion.promotion_coupons_table');
                    return $builder->join($couponTable, "{$couponTable}.promotion_id", '=', "{$promotionTable}.id")
                        ->whereRaw("({$couponTable}.type='1' AND {$couponTable}.code = '{$couponCode}')");
                })
        );
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

            // 用户使用次数检查
            if ($coupon->uses_per_user > 0) {
                if (!Auth::check()) {
                    throw new AuthenticationException();
                }
                if ($coupon->uses_per_user <= $this->usages->getUsageNumber($coupon->id, Auth::user()->email)) {
                    throw new InvalidCouponException('This coupon has run out.');
                }
            }

            if ($this->userCoupons->existsCouponId($coupon->id)) {
                if (!Auth::check()) {
                    throw new AuthenticationException();
                }

                $userCoupon = $this->userCoupons->findByCouponIdAndUserId($coupon->id, Auth::user()->email);
                if ($userCoupon) {
                    if (!empty($userCoupon->start_time) && $now->lt(Carbon::parse($userCoupon->start_time))) {
                        throw new InvalidCouponException('This Coupon has not yet begun.');
                    }
                    if (!empty($userCoupon->expire_time) && $now->gt(Carbon::parse($userCoupon->expire_time))) {
                        throw new InvalidCouponException('This Coupon has expired.');
                    }
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
     * @return mixed
     */
    public function getUserCoupons($customerId)
    {
        return $this->userCoupons->findByUserId($customerId);
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

    public function generateCouponByUserEvent($userId, $event, array $mergeData = [])
    {
        $code = null;
        if ($promotion = $this->promotions->findByEvent($event)->first()) {
            $code = $this->generatePromotionCoupons();
            $coupon = $this->coupons->create(array_merge([
                'promotion_id' => $promotion->id,
                'code' => $code,
                'usage_limit' => $promotion->uses_per_coupon,
                'uses_per_user' => $promotion->uses_per_user,
                'type' => CouponType::SLAVE,
            ], $mergeData));

            $this->userCoupons->create(array_merge([
                'user_id' => $userId,
                'coupon_id' => $coupon->id,
                'start_time' => new Carbon(),
                'expire_time' => (new Carbon())->addDay(),
                'description' => $event,
            ], $mergeData));
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