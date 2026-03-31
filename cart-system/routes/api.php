<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterationController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VendorController;
use Illuminate\Support\Facades\Route;

Route::post('register', [RegisterationController::class, '__invoke']);
Route::post('login', [LoginController::class, '__invoke']);

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('users', UserController::class);
    Route::apiResource('vendors', VendorController::class);
    Route::apiResource('products', ProductController::class);
    Route::apiResource('orders', OrderController::class)->only(['index', 'show']);
    Route::get('cart', [CartController::class, 'index']);
    Route::post('cart/items', [CartController::class, 'store']);
    Route::patch('cart/items/{cartItem}', [CartController::class, 'update']);
    Route::delete('cart/items/{cartItem}', [CartController::class, 'destroy']);
    Route::post('checkout', CheckoutController::class);
});
