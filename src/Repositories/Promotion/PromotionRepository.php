<?php

namespace Viviniko\Promotion\Repositories\Promotion;

use Viviniko\Repository\CrudRepository;

interface PromotionRepository extends CrudRepository
{
    /**
     * Get promotions by given event.
     *
     * @param $event
     * @return mixed
     */
    public function findAllByEvent($event);
}