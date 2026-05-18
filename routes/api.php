<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::controller(OrderController::class)->prefix('order')->group(function () {
    Route::get('/', 'index');
    Route::post('/', 'store');
});

Route::controller(ProductController::class)->prefix('products')->group(function () {
    Route::get('/', 'index');
    Route::post('/', 'store');
    Route::delete('/{id}', 'destroy');
    Route::put('/{id}', 'update');
});

Route::controller(UserController::class)->prefix('user')->group(function () {
    Route::get('/', 'index');
    Route::get('/{id}', 'show');
    Route::post('/', 'store');
    Route::delete('/{id}','destroy');
    Route::put('/{id}','update');
});
