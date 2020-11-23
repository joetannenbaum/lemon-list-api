<?php

namespace App\Http\Controllers;

use App\Events\ShoppingListUpdated;
use App\Http\Resources\ShoppingListItemResource;
use App\Models\Item;
use App\Models\ShoppingListItem;
use App\Models\ShoppingListVersion;
use Illuminate\Http\Request;

class ShoppingListItemController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!$request->route('shopping_list_version')->shoppingList->users()->find($request->user()->id, ['users.id'])) {
                abort(401, 'You are unauthorized to perform this action');
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
        return ShoppingListItemResource::collection($shopping_list_version->items()->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, ShoppingListVersion $shopping_list_version)
    {
        $existing_item_ids = Item::where('name', $request->input('name'))
                                    ->whereIn(
                                        'user_id',
                                        $shopping_list_version->shoppingList->users()->pluck('users.id')
                                    )
                                    ->pluck('id');


        if ($existing_item_ids->count()) {
            $existing = $shopping_list_version->items()
                                            ->whereIn('item_id', $existing_item_ids)
                                            ->first();

            if ($existing) {
                $existing->quantity = $existing->quantity + 1;
                $existing->save();

                event(new ShoppingListUpdated($shopping_list_version->shoppingList, $request->user()));

                return new ShoppingListItemResource($existing);
            }
        }

        $item = Item::create([
            'name'    => $request->input('name'),
            'user_id' => $request->user()->id,
        ]);

        $list_item = new ShoppingListItem([
            'quantity' => 1,
        ]);

        $list_item->item()->associate($item);

        event(new ShoppingListUpdated($shopping_list_version->shoppingList, $request->user()));

        $shopping_list_version->items()->save($list_item);

        return new ShoppingListItemResource($list_item);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ShoppingListItem  $shoppingListItem
     * @return \Illuminate\Http\Response
     */
    public function show(ShoppingListVersion $shopping_list_version, ShoppingListItem $shoppingListItem)
    {
        return new ShoppingListItemResource($shoppingListItem);
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
        $original_item = $item->item;
        $user =  $shopping_list_version->shoppingList->owner;

        $item->fill($request->all());

        if ($request->input('name')) {
            $primary_item = Item::firstOrCreate([
                'user_id' => $user->id,
                'name'    => $request->input('name')
            ]);

            $item->item()->associate($primary_item);
        }

        $item->save();

        if ($request->input('name')) {
            $original_item_count = ShoppingListItem::where('item_id', $original_item->id)->count();

            if ($original_item_count === 0) {
                // If this item isn't in any of the user's lists,
                // it's probably a mis-type and we should just clean up after ourselves.
                $original_item->forceDelete();
            }
        }

        event(new ShoppingListUpdated($shopping_list_version->shoppingList, $request->user()));

        return new ShoppingListItemResource($item);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ShoppingListItem  $shoppingListItem
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, ShoppingListVersion $shopping_list_version, ShoppingListItem $item)
    {
        $item->delete();

        event(new ShoppingListUpdated($shopping_list_version->shoppingList, $request->user()));
    }
}
