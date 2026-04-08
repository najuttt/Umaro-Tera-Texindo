<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductsApiController;
use App\Http\Controllers\Api\CartApiController;
use App\Http\Controllers\Api\CheckoutApiController;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

Route::get('/debug', function () {
    return response()->json([
        'msg' => 'API hidup 🚀'
    ]);
});

// 🔐 AUTH
Route::post('/login', [AuthController::class, 'login']);

// 📦 PRODUCTS
Route::get('/products', [ProductsApiController::class, 'index']);
Route::get('/products/{id}', [ProductsApiController::class, 'show']);

/*
|--------------------------------------------------------------------------
| CART (GUEST + LOGIN)
|--------------------------------------------------------------------------
*/
Route::get('/cart', [CartApiController::class, 'index']);
Route::post('/cart/add', [CartApiController::class, 'add']);
Route::post('/cart/update', [CartApiController::class, 'update']);
Route::delete('/cart/{id}', [CartApiController::class, 'delete']);

/*
|--------------------------------------------------------------------------
| CHECKOUT
|--------------------------------------------------------------------------
*/

// 📲 WA (guest boleh)
Route::post('/checkout/whatsapp', [CheckoutApiController::class, 'whatsapp']);

// 💳 MIDTRANS (login wajib)
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::post('/checkout/midtrans', [CheckoutApiController::class, 'midtrans']);
});