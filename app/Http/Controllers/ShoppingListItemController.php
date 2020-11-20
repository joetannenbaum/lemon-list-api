<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ShoppingListItem;
use App\Models\ShoppingListVersion;
use Illuminate\Http\Request;

class ShoppingListItemController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if ($request->route('shopping_list_version')->shoppingList->user->id !== $request->user()->id) {
                abort(401);
            }

            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, ShoppingListVersion $shopping_list_version)
    {
        return $shopping_list_version->items()->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, ShoppingListVersion $shopping_list_version)
    {
        $item = Item::firstOrCreate([
            'name'    => $request->input('name'),
            'user_id' => $shopping_list_version->shoppingList->user->id,
        ]);

        $existing = $shopping_list_version->items()->where('item_id', $item->id)->first();

        if ($existing) {
            $existing->quantity = $existing->quantity + 1;
            $existing->save();

            return $existing;
        }

        $list_item = new ShoppingListItem([
            'quantity' => 1,
        ]);

        $list_item->item()->associate($item);

        return $shopping_list_version->items()->save($list_item);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ShoppingListItem  $shoppingListItem
     * @return \Illuminate\Http\Response
     */
    public function show(ShoppingListVersion $shopping_list_version, ShoppingListItem $shoppingListItem)
    {
        return $shoppingListItem;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ShoppingListItem  $shoppingListItem
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ShoppingListVersion $shopping_list_version, ShoppingListItem $item)
    {
        $item->fill($request->all())->save();

        return $item;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ShoppingListItem  $shoppingListItem
     * @return \Illuminate\Http\Response
     */
    public function destroy(ShoppingListVersion $shopping_list_version, ShoppingListItem $item)
    {
        $item->delete();
    }
}
