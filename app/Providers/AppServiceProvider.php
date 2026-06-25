<?php
// app/Providers/AppServiceProvider.php

namespace App\Providers;

use App\Models\Category;
use App\Models\Item;
use App\Models\Supplier;
use App\Observers\CategoryObserver;
use App\Observers\ItemObserver;
use App\Observers\SupplierObserver;
use App\Services\CacheService;
use App\Services\DocumentNumberService;
use App\Services\EoqService;
use App\Services\ForecastService;
use App\Services\NotificationService;
use App\Services\StockService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(DocumentNumberService::class);
        $this->app->singleton(EoqService::class);
        $this->app->singleton(ForecastService::class);
        $this->app->singleton(NotificationService::class);
        $this->app->singleton(CacheService::class);

        $this->app->singleton(StockService::class, function ($app) {
            return new StockService(
                $app->make(DocumentNumberService::class),
                $app->make(NotificationService::class),
            );
        });
    }

    public function boot(): void
    {
        Item::observe(ItemObserver::class);
        Category::observe(CategoryObserver::class);
        Supplier::observe(SupplierObserver::class);
    }
}