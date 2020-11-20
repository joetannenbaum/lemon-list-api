<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ShoppingListController;
use App\Http\Controllers\ShoppingListItemController;
use App\Http\Controllers\ShoppingListVersionController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\StoreTagController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('auth/register', [AuthController::class, 'register']);

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => 'auth:api'], function () {
    Route::put('shopping-list-versions/{shopping_list_version}/reorder-items', [ShoppingListVersionController::class, 'reorderItems']);

    Route::put('stores/{store}/reorder-tags', [StoreController::class, 'reorderTags']);

    Route::apiResources([
        'shopping-lists'               => ShoppingListController::class,
        'shopping-list-versions.items' => ShoppingListItemController::class,
        'items'                        => ItemController::class,
        'stores'                       => StoreController::class,
        'stores.tags'                  => StoreTagController::class,
    ]);
});
