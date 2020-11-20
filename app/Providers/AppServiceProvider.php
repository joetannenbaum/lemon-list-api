<?php

namespace App\Providers;

use App\Models\ShoppingList;
use App\Models\Store;
use App\Observers\ShoppingListObserver;
use App\Observers\StoreObserver;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        ShoppingList::observe(ShoppingListObserver::class);
        Store::observe(StoreObserver::class);

        // DB::listen(function ($query) {
        //     \Log::info($query->sql);
        //     \Log::info($query->bindings);

        //     // $query->sql
        //     // $query->bindings
        //     // $query->time
        // });
    }
}
