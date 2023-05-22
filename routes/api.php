<?php

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


// Route::get('/ssd', 'App\Http\Controllers\idb2dataController@index');



// _________________________________________________________________



Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});



// ------------ProductsController----------------
//!Public Route for Search
// Route::get('product/search-products/{slug}', 'App\Http\Controllers\ProductController@show');

// Route::get('product/search-products/{generic_name}/{product}', 'App\Http\Controllers\ProductController@search');

// Route::get('product/all-generics', 'App\Http\Controllers\ProductController@generics');

//!Must be Hidden/Admin Route 
Route::group(['middleware' => 'api', 'prefix' => 'admin'], function ($router) {

    // Route::resource('/product/add-products', App\Http\Controllers\ProductController::class)->only([
    //     'store'
    // ]);

    // ------------ShofipyController----------------
    Route::resource('/shopify/all-products', App\Http\Controllers\ShopifyController::class)->only([
        'index'
    ]);



    // ------------OrderController---------------
    // Route::get('/ssd/orders', 'App\Http\Controllers\OrderController@index');
});


Route::get('/ssd/search-product/{pid}', 'App\Http\Controllers\AdminController@show');





//!User Password Reset/Change
Route::get('/ssd/password-change-request/{email}', [App\Http\Controllers\AuthController::class, 'passwordChangeRequest']);
Route::get('/ssd/password-change-verify/{email}', [App\Http\Controllers\AuthController::class, 'verifyPasswordChange']);

//!User Manangement
Route::group(['middleware' => 'api', 'prefix' => 'auth'], function ($router) {
    Route::post('/register', [App\Http\Controllers\AuthController::class, 'register']);
    Route::post('/token-access', [App\Http\Controllers\AuthController::class, 'token']);
    Route::post('/reclaim', [App\Http\Controllers\AuthController::class, 'logout']);
    Route::get('/verify/{email}', [App\Http\Controllers\AuthController::class, 'verify']);
    Route::get('/profile', [App\Http\Controllers\AuthController::class, 'profile']);

    // ------------SSDController---------------
    // Route::get('/sdd/all-products', [App\Http\Controllers\ProductController::class, 'index']);

    Route::get('/ssd/search-product/{pid}', 'App\Http\Controllers\ShopifyController@show');
    Route::get('/ssd/db2', 'App\Http\Controllers\idb2dataController@index');
    // ------------OrderController---------------
    // Route::post('/ssd/products/order', [App\Http\Controllers\OrderController::class, 'order']);
});



//!Admin Manangement
Route::group(['middleware' => 'api', 'prefix' => 'admin'], function ($router) {
    //Permissions
    Route::get('/ssd/new-rbac-permissions/{permission}', 'App\Http\Controllers\AdminController@newPermission');
    Route::get('/ssd/permissions-list', 'App\Http\Controllers\AdminController@permissions');
    //Users
    Route::get('/ssd/users-list', 'App\Http\Controllers\AdminController@GetAllUser');
});
