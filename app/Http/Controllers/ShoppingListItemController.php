<?php

namespace App\Http\Controllers;

use App\Events\ShoppingListUpdated;
use App\Http\Resources\ShoppingListItemResource;
use App\Models\Item;
use App\Models\ShoppingListItem;
use App\Models\ShoppingListVersion;
use App\Repository\ShoppingListVersionRepository;
use Illuminate\Database\QueryException;
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
        $list_item = app(ShoppingListVersionRepository::class)->addItem(
            $shopping_list_version,
            $request->user(),
            $request->all(),
        );

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
        $item = app(ShoppingListVersionRepository::class)->updateItem(
            $shopping_list_version,
            $item,
            $request->user(),
            $request->all()
        );

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
        app(ShoppingListVersionRepository::class)->deleteItem($item);
        event(new ShoppingListUpdated($shopping_list_version->shoppingList, $request->user()));
    }

    public function addBatchItems(Request $request, ShoppingListVersion $shopping_list_version)
    {
        $new_items = collect($request->input('items'))->map(function ($item) use ($shopping_list_version, $request) {

            // They sent in a "name" field, let's see what we've got
            $existing_item_ids = $this->findExistingItemsFromUsers($item['name'], $shopping_list_version);

            if ($existing_item_ids->count()) {
                $existing = $shopping_list_version->items()
                    ->whereIn('item_id', $existing_item_ids)
                    ->first();

                if ($existing) {
                    $existing->quantity = $existing->quantity + $item['quantity'];

                    if (!$existing->note) {
                        $existing->note = $item['note'] ?: null;
                    }

                    $existing->save();

                    return $existing;
                }
            }

            $item = Item::create([
                'name'    => $item['name'],
                'user_id' => $request->user()->id,
            ]);

            $list_item = ShoppingListItem::make([
                'quantity' => $item['quantity'],
                'note'     => $item['note'],
            ]);

            $list_item->item()->associate($item);

            $shopping_list_version->items()->save($list_item);

            return $list_item;
        });

        return ShoppingListItemResource::collection($new_items);
    }
}
