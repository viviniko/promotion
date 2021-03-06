<?php

namespace Viviniko\Promotion\Repositories\Promotion;

use Viviniko\Repository\EloquentRepository;
use Illuminate\Support\Facades\Config;

class EloquentPromotion extends EloquentRepository implements PromotionRepository
{
    public function __construct()
    {
        parent::__construct(Config::get('promotion.promotion'));
    }

    /**
     * {@inheritdoc}
     */
    public function findAllByEvent($event)
    {
        return $this->findAllBy('event', $event);
    }
}