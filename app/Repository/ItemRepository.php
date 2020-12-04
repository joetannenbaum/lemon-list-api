<?php

namespace App\Repository;

use App\Models\Item;
use App\Models\User;
use Illuminate\Support\Arr;

class ItemRepository
{
    public function update(Item $item, $params, User $user)
    {
        $user_has_item = $user->items()->where('items.id', $item->id)->first() !== null;

        $item->fill($params)->save();

        if (Arr::get($params, 'store_tags')) {
            if (!$user_has_item) {
                $user->items()->save($item);
            }

            $tags = collect($params['store_tags'])->filter();

            if ($tags->count() === 0) {
                $item->storeTags()->wherePivot('user_id', $user->id)->detach();
            } else {
                $to_sync = $tags->mapWithKeys(function ($tag_id) use ($user) {
                    return [
                        $tag_id => ['user_id' => $user->id],
                    ];
                })->toArray();

                $item->storeTags()->wherePivot('user_id', $user->id)->sync($to_sync);
            }
        }

        return $item;
    }
}
