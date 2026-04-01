<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductsApiController;
use App\Http\Controllers\Api\CartApiController;
use App\Http\Controllers\Api\CheckoutApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Semua route otomatis pakai prefix /api
*/

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES (TANPA LOGIN)
|--------------------------------------------------------------------------
*/

// 🔐 AUTH
Route::post('/login', [AuthController::class, 'login']);

// 📦 PRODUCTS
Route::get('/products', [ProductsApiController::class, 'index']);
Route::get('/products/{id}', [ProductsApiController::class, 'show']);

// 📲 CHECKOUT WHATSAPP (GUEST BISA)
Route::post('/checkout/whatsapp', [CheckoutApiController::class, 'whatsapp']);


/*
|--------------------------------------------------------------------------
| PROTECTED ROUTES (LOGIN WAJIB)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    // 🔓 LOGOUT
    Route::post('/logout', [AuthController::class, 'logout']);

    /*
    |-----------------
    | CART (LOGIN ONLY)
    |-----------------
    */
    Route::get('/cart', [CartApiController::class, 'index']);
    Route::post('/cart/add', [CartApiController::class, 'add']);
    Route::post('/cart/update', [CartApiController::class, 'update']);
    Route::delete('/cart/{id}', [CartApiController::class, 'delete']);

    /*
    |-----------------
    | CHECKOUT
    |-----------------
    */

    // 💳 MIDTRANS (WAJIB LOGIN)
    Route::post('/checkout/midtrans', [CheckoutApiController::class, 'midtrans']);
});