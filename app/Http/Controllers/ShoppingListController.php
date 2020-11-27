<?php

namespace App\Http\Controllers;

use App\Events\ShoppingListUpdated;
use App\Http\Resources\ShoppingListResource;
use App\Models\ShoppingList;
use Illuminate\Http\Request;

class ShoppingListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return ShoppingListResource::collection(
            $request->user()->shoppingLists()->with('activeVersion')->orderBy('name')->get()
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $list = ShoppingList::make($request->all());

        $list->owner()->associate($request->user());

        $list->save();

        $list->load('activeVersion');

        return new ShoppingListResource($list);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ShoppingList  $shoppingList
     * @return \Illuminate\Http\Response
     */
    public function show(ShoppingList $shoppingList)
    {
        // TODO: Include trashed items for archived list?
        $shoppingList->load('activeVersion.items.item.storeTags');

        return new ShoppingListResource($shoppingList);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ShoppingList  $shoppingList
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ShoppingList $shoppingList)
    {
        $shoppingList->fill($request->all())->save();

        event(new ShoppingListUpdated($shoppingList, $request->user()));

        return new ShoppingListResource($shoppingList);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ShoppingList  $shoppingList
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, ShoppingList $shoppingList)
    {
        if ($shoppingList->owner->id === $request->user()->id) {
            $shoppingList->delete();
        } else {
            $request->user()->shoppingLists()->detach($shoppingList);
        }

        event(new ShoppingListUpdated($shoppingList, $request->user()));
    }

    public function findByUuid($uuid)
    {
        return new ShoppingListResource(ShoppingList::where('uuid', $uuid)->firstOrFail());
    }

    public function joinByUuid(Request $request, $uuid)
    {
        $list = ShoppingList::where('uuid', $uuid)->firstOrFail();

        if ($list->users()->pluck('users.id')->contains($request->user()->id)) {
            // This user is already associated with the list, just return
            return $list;
        }

        $list->users()->save($request->user());

        return new ShoppingListResource($list);
    }
}
