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

    protected function saveNewShoppingListItem(ShoppingListVersion $shopping_list_version, Item $item)
    {
        $list_item = ShoppingListItem::make()->item()->associate($item);

        $shopping_list_version->items()->save($list_item);

        return $list_item;
    }

    protected function increaseItemQuantity(ShoppingListItem $item)
    {
        $item->quantity = $item->quantity + 1;
        $item->save();

        return $item;
    }

    protected function findOrCreateItemInList(Request $request, ShoppingListVersion $shopping_list_version)
    {
        if ($request->input('item_id')) {
            // They sent in an explicit item id, just add it and move on
            $item = Item::findOrFail($request->input('item_id'));

            $existing = $shopping_list_version->items()->where('item_id', $item->id)->first();

            if ($existing) {
                return $this->increaseItemQuantity($existing);
            }

            return $this->saveNewShoppingListItem($shopping_list_version, $item);
        }

        // They sent in a "name" field, let's see what we've got
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
                return $this->increaseItemQuantity($existing);
            }
        }

        $item = Item::create([
            'name'    => $request->input('name'),
            'user_id' => $request->user()->id,
        ]);

        return $this->saveNewShoppingListItem($shopping_list_version, $item);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, ShoppingListVersion $shopping_list_version)
    {
        $list_item = $this->findOrCreateItemInList($request, $shopping_list_version);

        event(new ShoppingListUpdated($shopping_list_version->shoppingList, $request->user()));

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
        $user = $shopping_list_version->shoppingList->owner;

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
            // If this item isn't in any of the user's lists,
            // it's probably a mis-type and we should just clean up after ourselves.
            $this->checkForOrphanItem($original_item);
        }

        event(new ShoppingListUpdated($shopping_list_version->shoppingList, $request->user()));

        return new ShoppingListItemResource($item);
    }

    protected function checkForOrphanItem(Item $item)
    {
        $item_count = ShoppingListItem::where('item_id', $item->id)->count();

        if ($item_count === 0) {
            // If this item isn't in any of the user's lists,
            // it's probably a mis-type and we should just clean up after ourselves.
            $item->forceDelete();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ShoppingListItem  $shoppingListItem
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, ShoppingListVersion $shopping_list_version, ShoppingListItem $item)
    {
        $original_item = $item->item;

        $item->delete();

        $this->checkForOrphanItem($original_item);

        event(new ShoppingListUpdated($shopping_list_version->shoppingList, $request->user()));
    }
}
