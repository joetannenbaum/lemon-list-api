<?php

namespace App\Observers;

use App\Models\Store;
use App\Models\StoreTag;

class StoreObserver
{
    /**
     * Handle the Store "created" event.
     *
     * @param  \App\Models\Store  $store
     * @return void
     */
    public function created(Store $store)
    {
        $default_tags = collect([
            'Bakery',
            'Baking',
            'Beer & Wine',
            'Beverages',
            'Bread',
            'Canned Goods',
            'Cereal',
            'Cheese',
            'Cleaning Products',
            'Dairy',
            'Deli/Prepared Foods',
            'Health & Beauty',
            'Meat',
            'Paper Products',
            'Pasta & Rice',
            'Produce',
            'Seafood',
            'Snacks',
        ])->map(function ($name) {
            return new StoreTag([
                'name' => $name,
            ]);
        });

        $store->tags()->saveMany($default_tags);
    }

    /**
     * Handle the Store "updated" event.
     *
     * @param  \App\Models\Store  $store
     * @return void
     */
    public function updated(Store $store)
    {
        //
    }

    /**
     * Handle the Store "deleted" event.
     *
     * @param  \App\Models\Store  $store
     * @return void
     */
    public function deleted(Store $store)
    {
        //
    }

    /**
     * Handle the Store "restored" event.
     *
     * @param  \App\Models\Store  $store
     * @return void
     */
    public function restored(Store $store)
    {
        //
    }

    /**
     * Handle the Store "force deleted" event.
     *
     * @param  \App\Models\Store  $store
     * @return void
     */
    public function forceDeleted(Store $store)
    {
        //
    }
}
