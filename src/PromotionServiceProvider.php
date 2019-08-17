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
            \Viviniko\Promotion\Repositories\UserCoupon\UserCouponRepository::class,
            \Viviniko\Promotion\Repositories\UserCoupon\EloquentUserCoupon::class
        );
        $this->app->singleton(
            \Viviniko\Promotion\Repositories\Usage\UsageRepository::class,
            \Viviniko\Promotion\Repositories\Usage\EloquentUsage::class
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
        ];
    }
}