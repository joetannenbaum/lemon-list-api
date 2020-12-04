<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\ShoppingList;
use App\Models\ShoppingListItem;
use App\Models\Store;
use App\Models\StoreTag;
use App\Models\User;
use Database\Factories\StoreTagFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class ItemTest extends TestCase
{
    use RefreshDatabase;

    protected function addItemsToList($list, $params)
    {
        return $this->postJson(
            sprintf('api/shopping-list-versions/%d/items', $list->activeVersion->id),
            $params
        );
    }

    protected function createUserAndShoppingList()
    {
        $user = User::factory()->create();
        $list = ShoppingList::factory()->make();
        $list->owner()->associate($user);
        $list->save();
        $list->load('activeVersion');

        return [$user, $list];
    }

    protected function createUserAndAttachList($list)
    {
        $user = User::factory()->create();
        $list->users()->save($user);

        return $user;
    }

    public function testAddingItemsBelongToUser()
    {
        list($user, $list) = $this->createUserAndShoppingList();

        $user2 = $this->createUserAndAttachList($list);

        Passport::actingAs($user);

        $this->addItemsToList($list, [
            'name' => 'blueberries',
        ]);

        Passport::actingAs($user2);

        $this->addItemsToList($list, [
            'name' => 'chicken',
        ]);

        $this->assertSame(Item::find(1)->owner->id, $user->id);
        $this->assertSame(Item::find(2)->owner->id, $user2->id);
    }

    public function testTagItemInStore()
    {
        list($user, $list) = $this->createUserAndShoppingList();

        $user2 = $this->createUserAndAttachList($list);

        Passport::actingAs($user);

        $this->addItemsToList($list, [
            'name' => 'blueberries',
        ]);

        $store = Store::factory()->make();

        $user->stores()->save($store);

        $tag = StoreTag::factory()->make();

        $store->tags()->save($tag);

        $item = Item::first();

        $this->assertSame($item->storeTags()->wherePivot('user_id', $user2->id)->count(), 0);

        // User 2 shouldn't have this item attached to their account
        $this->assertNull($user2->items()->where('items.id', $item->id)->first());

        // The non-owner tags the item
        Passport::actingAs($user2);

        $response = $this->putJson(
            sprintf('api/items/%d', $item->id),
            [
                'store_tags' => [
                    $tag->id,
                ]
            ]
        );

        $response->assertOk();

        // User 2 should now have this item attached to their account
        $this->assertNotNull($user2->items()->where('items.id', $item->id)->first());
    }
}
