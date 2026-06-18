<?php

namespace App\Providers;

use App\Observers\WeddingSyncV2AutoExportObserver;

use App\Models\Guest;

use App\Models\ChecklistItem;

use App\Models\BudgetItem;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        
        ChecklistItem::observe(WeddingSyncV2AutoExportObserver::class);
        BudgetItem::observe(WeddingSyncV2AutoExportObserver::class);
        Guest::observe(WeddingSyncV2AutoExportObserver::class);
//
    }
}
