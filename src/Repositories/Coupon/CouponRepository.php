<?php

namespace Viviniko\Promotion\Repositories\Coupon;

use Viviniko\Repository\SearchRequest;

interface CouponRepository
{
    /**
     * Search.
     *
     * @param SearchRequest $searchRequest
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
     */
    public function search(SearchRequest $searchRequest);

    /**
     * Find data by coupon code.
     *
     * @param $code
     * @return mixed
     */
    public function findByCode($code);
}