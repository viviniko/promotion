<?php

namespace Viviniko\Promotion\Repositories\Promotion;

use Viviniko\Repository\SearchRequest;

interface PromotionRepository
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
     * Get promotions by given event.
     *
     * @param $event
     * @return mixed
     */
    public function findAllByEvent($event);
}