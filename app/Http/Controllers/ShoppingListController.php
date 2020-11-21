<?php

namespace App\Http\Controllers;

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
        return $request->user()->shoppingLists()->orderBy('name')->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return $request->user()->shoppingLists()->save(ShoppingList::make($request->all()));
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

        return $shoppingList;
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
        $shoppingList->update($request->all());

        return $shoppingList;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ShoppingList  $shoppingList
     * @return \Illuminate\Http\Response
     */
    public function destroy(ShoppingList $shoppingList)
    {
        $shoppingList->delete();
    }
}
