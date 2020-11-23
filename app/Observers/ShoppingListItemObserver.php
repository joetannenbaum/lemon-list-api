<?php

namespace App\Observers;

use App\Models\ShoppingListItem;

class ShoppingListItemObserver
{
    /**
     * Handle the ShoppingListItem "created" event.
     *
     * @param  \App\Models\ShoppingListItem  $shoppingListItem
     * @return void
     */
    public function creating(ShoppingListItem $shoppingListItem)
    {
        $shoppingListItem->quantity = 1;
    }

    /**
     * Handle the ShoppingListItem "updated" event.
     *
     * @param  \App\Models\ShoppingListItem  $shoppingListItem
     * @return void
     */
    public function updated(ShoppingListItem $shoppingListItem)
    {
        //
    }

    /**
     * Handle the ShoppingListItem "deleted" event.
     *
     * @param  \App\Models\ShoppingListItem  $shoppingListItem
     * @return void
     */
    public function deleted(ShoppingListItem $shoppingListItem)
    {
        //
    }

    /**
     * Handle the ShoppingListItem "restored" event.
     *
     * @param  \App\Models\ShoppingListItem  $shoppingListItem
     * @return void
     */
    public function restored(ShoppingListItem $shoppingListItem)
    {
        //
    }

    /**
     * Handle the ShoppingListItem "force deleted" event.
     *
     * @param  \App\Models\ShoppingListItem  $shoppingListItem
     * @return void
     */
    public function forceDeleted(ShoppingListItem $shoppingListItem)
    {
        //
    }
}
