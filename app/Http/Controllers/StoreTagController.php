<?php

namespace App\Http\Controllers;

use App\Http\Resources\StoreTagResource;
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
        return StoreTagResource::collection($store->tags()->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Store $store)
    {
        $tag = StoreTag::make($request->all());

        $store->tags()->save($tag);

        return new StoreTagResource($tag);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\StoreTag  $storeTag
     * @return \Illuminate\Http\Response
     */
    public function show(StoreTag $tag)
    {
        return new StoreTagResource($tag);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\StoreTag  $storeTag
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Store $store, StoreTag $tag)
    {
        $tag->update($request->all());

        return new StoreTagResource($tag);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\StoreTag  $storeTag
     * @return \Illuminate\Http\Response
     */
    public function destroy(Store $store, StoreTag $tag)
    {
        $tag->delete();
    }
}
