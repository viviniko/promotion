<?php

namespace Viviniko\Promotion;

use Viviniko\Promotion\Console\Commands\PromotionTableCommand;
use Illuminate\Support\ServiceProvider;

class PromotionServiceProvider extends ServiceProvider
{
    protected $defer = false;

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/promotion.php' => config_path('promotion.php')
        ]);
        $this->commands('command.promotion.table');
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/promotion.php', 'promotion');

        $this->registerRepositories();
        $this->registerPromotionService();
        $this->registerCouponService();

        $this->registerCommands();
    }

    protected function registerRepositories()
    {
        $this->app->singleton(
            \Viviniko\Promotion\Repositories\Promotion\PromotionRepository::class,
            \Viviniko\Promotion\Repositories\Promotion\EloquentPromotion::class
        );
        $this->app->singleton(
            \Viviniko\Promotion\Repositories\Coupon\CouponRepository::class,
            \Viviniko\Promotion\Repositories\Coupon\EloquentCoupon::class
        );
        $this->app->singleton(
            \Viviniko\Promotion\Repositories\PromotionUsage\PromotionUsageRepository::class,
            \Viviniko\Promotion\Repositories\PromotionUsage\EloquentPromotionUsage::class
        );
    }

    private function registerPromotionService()
    {
        $this->app->singleton(
            \Viviniko\Promotion\Contracts\PromotionService::class,
            \Viviniko\Promotion\Services\Promotion\PromotionServiceImpl::class
        );
    }

    private function registerCouponService()
    {
        $this->app->singleton(
            \Viviniko\Promotion\Contracts\CouponService::class,
            \Viviniko\Promotion\Services\Coupon\CouponServiceImpl::class
        );
    }

    private function registerCommands()
    {
        $this->app->singleton('command.promotion.table', function ($app) {
            return new PromotionTableCommand($app['files'], $app['composer']);
        });
    }

    public function provides()
    {
        return [
            \Viviniko\Promotion\Contracts\PromotionService::class,
            \Viviniko\Promotion\Contracts\CouponService::class,
        ];
    }
}