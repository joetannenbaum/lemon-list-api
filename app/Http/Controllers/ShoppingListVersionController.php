<?php

namespace App\Http\Controllers;

use App\Models\ShoppingListItem;
use App\Models\ShoppingListVersion;
use Illuminate\Http\Request;

class ShoppingListVersionController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware(function ($request, $next) {
    //         if ($request->store->user->id !== $request->user()->id) {
    //             abort(401);
    //         }

    //         return $next($request);
    //     });
    // }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ShoppingListVersion  $shoppingListVersion
     * @return \Illuminate\Http\Response
     */
    public function show(ShoppingListVersion $shoppingListVersion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ShoppingListVersion  $shoppingListVersion
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ShoppingListVersion $shoppingListVersion)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ShoppingListVersion  $shoppingListVersion
     * @return \Illuminate\Http\Response
     */
    public function destroy(ShoppingListVersion $shoppingListVersion)
    {
        //
    }

    public function reorderItems(Request $request, ShoppingListVersion $shopping_list_version)
    {
        ShoppingListItem::setNewOrder($request->input('order'));

        return $shopping_list_version;
    }
}
