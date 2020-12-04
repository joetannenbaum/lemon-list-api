<?php

namespace App\Providers;

use App\Models\Item;
use App\Models\ShoppingList;
use App\Models\ShoppingListItem;
use App\Models\Store;
use App\Observers\ItemObserver;
use App\Observers\ShoppingListItemObserver;
use App\Observers\ShoppingListObserver;
use App\Observers\StoreObserver;
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
        ShoppingListItem::observe(ShoppingListItemObserver::class);
        Item::observe(ItemObserver::class);
    }
}
