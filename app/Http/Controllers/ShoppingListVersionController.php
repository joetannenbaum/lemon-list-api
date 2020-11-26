<?php

namespace App\Http\Controllers;

use App\Events\ShoppingListUpdated;
use App\Http\Resources\ShoppingListVersionResource;
use App\Models\ShoppingListItem;
use App\Models\ShoppingListVersion;
use Illuminate\Http\Request;

class ShoppingListVersionController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware(function ($request, $next) {
    //         if ($request->store->user->id !== $request->user()->id) {
    //             abort(401);
    //         }

    //         return $next($request);
    //     });
    // }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ShoppingListVersion  $shoppingListVersion
     * @return \Illuminate\Http\Response
     */
    public function show(ShoppingListVersion $shoppingListVersion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ShoppingListVersion  $shoppingListVersion
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ShoppingListVersion $shoppingListVersion)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ShoppingListVersion  $shoppingListVersion
     * @return \Illuminate\Http\Response
     */
    public function destroy(ShoppingListVersion $shoppingListVersion)
    {
        //
    }

    public function reorderItems(Request $request, ShoppingListVersion $shopping_list_version)
    {
        // TODO: Verify that they own all of these items
        ShoppingListItem::setNewOrder($request->input('order'));

        event(new ShoppingListUpdated($shopping_list_version->shoppingList, $request->user()));

        return new ShoppingListVersionResource($shopping_list_version);
    }

    public function addItemsFromAnotherList(Request $request, ShoppingListVersion $shopping_list_version)
    {
        ShoppingListItem::whereIn('id', $request->input('item_ids'))
            ->get()
            ->each(function ($item) use ($shopping_list_version) {
                $existing = $shopping_list_version->items()
                    ->where('item_id', $item->item_id)
                    ->first();

                if ($existing) {
                    $existing->quantity = $existing->quantity + $item->quantity;
                    $existing->save();

                    return $existing;
                }

                $new_item = $item->replicate();

                $new_item->checked_off = false;

                $shopping_list_version->items()->save($new_item);
            });

        event(new ShoppingListUpdated($shopping_list_version->shoppingList, $request->user()));
    }
}
