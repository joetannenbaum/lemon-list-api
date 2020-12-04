<?php

namespace Tests\Feature;

use App\Models\ShoppingList;
use App\Models\ShoppingListItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class ShoppingListVersionTest extends TestCase
{
    use RefreshDatabase;

    protected function addItemsToList($version_id, $params)
    {
        return $this->postJson(
            sprintf('api/shopping-list-versions/%d/items', $version_id),
            $params
        );
    }

    public function testAddBasicItem()
    {
        $user = User::factory()->create();
        $list = ShoppingList::factory()->make();
        $list->owner()->associate($user);
        $list->save();
        $list->load('activeVersion');

        Passport::actingAs($user);

        $response = $this->postJson(
            sprintf('api/shopping-list-versions/%d/items', $list->activeVersion->id),
            [
                'name' => 'blueberries'
            ]
        );

        $response->assertCreated()->assertExactJson([
            'data' => [
                'id' => 1,
                'shopping_list_version_id' => $list->activeVersion->id,
                'item_id' => 1,
                'order' => 1,
                'quantity' => 1,
                'checked_off' => false,
                'note' => null,
                'item' => [
                    'id' => 1,
                    'owner_id' => $user->id,
                    'name' => 'blueberries',
                ],
            ],
        ]);
    }

    public function testIncreaseQuantityWhenAddingSameItem()
    {
        $user = User::factory()->create();
        $list = ShoppingList::factory()->make();
        $list->owner()->associate($user);
        $list->save();
        $list->load('activeVersion');

        Passport::actingAs($user);

        $this->addItemsToList(
            $list->activeVersion->id,
            [
                'name' => 'blueberries'
            ]
        );

        $this->addItemsToList(
            $list->activeVersion->id,
            [
                'name' => 'raspberries'
            ]
        );

        $response = $this->addItemsToList(
            $list->activeVersion->id,
            [
                'name' => 'blueberries'
            ]
        );

        $response->assertOk()->assertExactJson([
            'data' => [
                'id' => 1,
                'shopping_list_version_id' => $list->activeVersion->id,
                'item_id' => 1,
                'order' => 1,
                'quantity' => 2,
                'checked_off' => false,
                'note' => null,
                'item' => [
                    'id' => 1,
                    'owner_id' => $user->id,
                    'name' => 'blueberries',
                ],
            ],
        ]);

        $this->assertSame(ShoppingListItem::find(2)->quantity, 1);
    }

    public function testUpdateItem()
    {
        $user = User::factory()->create();
        $list = ShoppingList::factory()->make();
        $list->owner()->associate($user);
        $list->save();
        $list->load('activeVersion');

        Passport::actingAs($user);

        $this->addItemsToList(
            $list->activeVersion->id,
            [
                'name' => 'blueberries'
            ]
        );

        $this->addItemsToList(
            $list->activeVersion->id,
            [
                'name' => 'raspberries'
            ]
        );

        $this->addItemsToList(
            $list->activeVersion->id,
            [
                'name' => 'chicken'
            ]
        );

        $response = $this->putJson(
            sprintf('api/shopping-list-versions/%d/items/3', $list->activeVersion->id),
            [
                'name' => 'chicken',
                'quantity' => 4,
            ]
        );

        $response->assertOk();

        $this->assertSame(ShoppingListItem::find(3)->quantity, 4);
    }

    public function testUpdateItemWithSameNameAsAnotherItem()
    {
        $user = User::factory()->create();
        $list = ShoppingList::factory()->make();
        $list->owner()->associate($user);
        $list->save();
        $list->load('activeVersion');

        Passport::actingAs($user);

        $this->addItemsToList(
            $list->activeVersion->id,
            [
                'name' => 'blueberries'
            ]
        );

        $this->addItemsToList(
            $list->activeVersion->id,
            [
                'name' => 'raspberries'
            ]
        );

        $this->addItemsToList(
            $list->activeVersion->id,
            [
                'name' => 'chicken'
            ]
        );

        $response = $this->putJson(
            sprintf('api/shopping-list-versions/%d/items/3', $list->activeVersion->id),
            [
                'name' => 'blueberries',
                'quantity' => 4,
            ]
        );

        $response->assertOk();

        $this->assertSame(ShoppingListItem::find(1)->quantity, 5);
        $this->assertCount(2, $list->activeVersion->items);
    }

    public function testAddBatchItems()
    {
        $user = User::factory()->create();
        $list = ShoppingList::factory()->make();
        $list->owner()->associate($user);
        $list->save();
        $list->load('activeVersion');

        Passport::actingAs($user);

        $this->addItemsToList(
            $list->activeVersion->id,
            [
                'name' => 'blueberries'
            ]
        );

        $this->addItemsToList(
            $list->activeVersion->id,
            [
                'name' => 'raspberries'
            ]
        );

        $this->addItemsToList(
            $list->activeVersion->id,
            [
                'name' => 'chicken',
                'note' => 'Wowzers',
            ]
        );

        $response = $this->postJson(
            sprintf('api/shopping-list-versions/%d/batch-items', $list->activeVersion->id),
            [
                'items' => [
                    [
                        'name'     => 'blueberries',
                        'note'     => 'Just a few',
                        'quantity' => 2,
                    ],
                    [
                        'name'     => 'chocolate',
                        'note'     => null,
                        'quantity' => 1,
                    ],
                    [
                        'name'     => 'chicken',
                        'note'     => 'Thigh please',
                        'quantity' => 1,
                    ],
                    [
                        'name'     => 'ham',
                        'note'     => 'Whole ham, thanks',
                        'quantity' => 4,
                    ],
                ],
            ]
        );

        $response->assertOk();

        $item1 = ShoppingListItem::find(1);

        $this->assertSame($item1->item->name, 'blueberries');
        $this->assertSame($item1->quantity, 3);
        $this->assertSame($item1->note, 'Just a few');

        $item2 = ShoppingListItem::find(2);

        $this->assertSame($item2->item->name, 'raspberries');
        $this->assertSame($item2->quantity, 1);
        $this->assertNull($item2->note);

        $item3 = ShoppingListItem::find(3);

        $this->assertSame($item3->item->name, 'chicken');
        $this->assertSame($item3->quantity, 2);
        $this->assertSame($item3->note, 'Wowzers, Thigh please');

        $item4 = ShoppingListItem::find(4);

        $this->assertSame($item4->item->name, 'chocolate');
        $this->assertSame($item4->quantity, 1);
        $this->assertNull($item4->note);

        $item5 = ShoppingListItem::find(5);

        $this->assertSame($item5->item->name, 'ham');
        $this->assertSame($item5->quantity, 4);
        $this->assertSame($item5->note, 'Whole ham, thanks');

        $this->assertCount(5, $list->activeVersion->items);
    }
}
