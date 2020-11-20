<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\StoreTag;
use Illuminate\Http\Request;

class StoreTagController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if ($request->route('store')->user->id !== $request->user()->id) {
                abort(401);
            }

            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Store $store)
    {
        return $store->tags()->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Store $store)
    {
        return $store->tags()->save(StoreTag::make($request->all()));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\StoreTag  $storeTag
     * @return \Illuminate\Http\Response
     */
    public function show(StoreTag $storeTag)
    {
        return $storeTag;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\StoreTag  $storeTag
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, StoreTag $storeTag)
    {
        $storeTag->update($request->all());

        return $storeTag;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\StoreTag  $storeTag
     * @return \Illuminate\Http\Response
     */
    public function destroy(StoreTag $storeTag)
    {
        $storeTag->delete();
    }
}
