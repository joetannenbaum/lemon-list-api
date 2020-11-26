<?php

namespace App\Repository;

use App\Models\Item;
use App\Models\ShoppingListItem;
use App\Models\ShoppingListVersion;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class ShoppingListVersionRepository
{
    public function addItem(ShoppingListVersion $version, $user, $params)
    {
        if (Arr::get($params, 'item_id')) {
            // They sent in an explicit item id, just add it and move on
            $item = Item::findOrFail($params['item_id']);

            $existing = $version->items()->where('item_id', $item->id)->first();

            if ($existing) {
                return $this->increaseItemQuantity($existing);
            }

            return $this->saveNewShoppingListItem($version, $item);
        }

        // They sent in a "name" field, let's see what we've got
        $existing_item_ids = $this->findExistingItemsFromUsers($params['name'], $version);

        if ($existing_item_ids->count()) {
            $existing = $version->items()
                ->whereIn('item_id', $existing_item_ids)
                ->first();

            if ($existing) {
                return $this->increaseItemQuantity($existing);
            }
        }

        $item = Item::create([
            'name'    => $params['name'],
            'user_id' => $user->id,
        ]);

        return $this->saveNewShoppingListItem($version, $item);
    }

    public function updateItem(ShoppingListVersion $version, ShoppingListItem $item, $user, $params)
    {
        $item->fill($params);

        if (!Arr::get($params, 'name') || $params['name'] === $item->item->name) {
            $item->save();

            return $item;
        }

        // We have a name and we are updating it, let's see if it already exists or not
        $existing_items = $this->findExistingItemsFromUsers($params['name'], $version);

        if ($existing_items->count()) {
            return $this->handleExistingItemUpdate($existing_items, $item, $version);
        }

        // This is a new item, associate it with the user and attach it
        $primary_item = Item::firstOrCreate([
            'user_id' => $user->id,
            'name'    => $params['name']
        ]);

        $item->item()->associate($primary_item);
        $item->save();

        $this->checkForOrphanItem($item->item);

        return $item;
    }

    public function deleteItem(ShoppingListItem $item)
    {
        $original_item = $item->item;
        $item->delete();
        $this->checkForOrphanItem($original_item);
    }

    protected function handleExistingItemUpdate(Collection $existing_items, ShoppingListItem $item, ShoppingListVersion $version)
    {
        $new_item_id = $existing_items->first();

        // Double check that this item isn't already in the list
        $item_in_list = $version->items()
            ->where('item_id', $new_item_id)
            ->first();

        if ($item_in_list) {
            $item_in_list->quantity = $item_in_list->quantity + $item->quantity;
            $item_in_list->save();
            // Delete the original item, we don't need it anymore
            $item->delete();

            $this->checkForOrphanItem($item->item);

            return $item_in_list;
        }

        // We found an existing item, associate that with the record
        $item->item()->associate($new_item_id);
        $item->save();

        return $item;
    }

    protected function increaseItemQuantity(ShoppingListItem $item)
    {
        $item->quantity = $item->quantity + 1;
        $item->save();
        $item->load('item');

        return $item;
    }

    protected function findExistingItemsFromUsers($name, $version)
    {
        return Item::where('name', $name)
            ->whereIn(
                'user_id',
                $version->shoppingList->users()->pluck('users.id')
            )
            ->pluck('id');
    }

    protected function saveNewShoppingListItem(ShoppingListVersion $version, Item $item)
    {
        $list_item = ShoppingListItem::make()->item()->associate($item);

        $version->items()->save($list_item);

        return $list_item;
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
}
