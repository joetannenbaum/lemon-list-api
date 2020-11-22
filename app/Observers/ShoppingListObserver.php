<?php

namespace App\Observers;

use App\Models\ShoppingList;
use App\Models\ShoppingListVersion;
use Illuminate\Support\Str;

class ShoppingListObserver
{
    /**
     * Handle the ShoppingList "created" event.
     *
     * @param  \App\Models\ShoppingList  $shoppingList
     * @return void
     */
    public function creating(ShoppingList $shoppingList)
    {
        $shoppingList->uuid = Str::uuid();
    }

    /**
     * Handle the ShoppingList "created" event.
     *
     * @param  \App\Models\ShoppingList  $shoppingList
     * @return void
     */
    public function created(ShoppingList $shoppingList)
    {
        $shoppingList->versions()->save(new ShoppingListVersion());
        $shoppingList->users()->save($shoppingList->owner);
    }

    /**
     * Handle the ShoppingList "updated" event.
     *
     * @param  \App\Models\ShoppingList  $shoppingList
     * @return void
     */
    public function updated(ShoppingList $shoppingList)
    {
        //
    }

    /**
     * Handle the ShoppingList "deleted" event.
     *
     * @param  \App\Models\ShoppingList  $shoppingList
     * @return void
     */
    public function deleted(ShoppingList $shoppingList)
    {
        //
    }

    /**
     * Handle the ShoppingList "restored" event.
     *
     * @param  \App\Models\ShoppingList  $shoppingList
     * @return void
     */
    public function restored(ShoppingList $shoppingList)
    {
        //
    }

    /**
     * Handle the ShoppingList "force deleted" event.
     *
     * @param  \App\Models\ShoppingList  $shoppingList
     * @return void
     */
    public function forceDeleted(ShoppingList $shoppingList)
    {
        //
    }
}
