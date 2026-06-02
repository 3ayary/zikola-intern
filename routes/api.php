<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::controller(OrderController::class)->prefix('order')->middleware('auth:sanctum')->group(function () {
    Route::get('/', 'index')->middleware('IsAdmin');
    Route::post('/', 'store');
    Route::get('/expensive', 'expensive')->middleware('IsAdmin');
    Route::get('/trash', 'trashOrders')->middleware('IsAdmin');
    Route::delete('/{id}', 'destroy');  //admin or owner
});

Route::controller(ProductController::class)->prefix('products')->group(function () {
    Route::get('/', 'index');
    Route::get('/{id}', 'show');
    Route::post('/', 'store')->middleware(['auth:sanctum', 'IsAdmin']);
    Route::delete('/{id}', 'destroy')->middleware(['auth:sanctum', 'IsAdmin']);
    Route::put('/{id}', 'update')->middleware(['auth:sanctum', 'IsAdmin']);
});

Route::controller(UserController::class)->prefix('user')->middleware(['auth:sanctum', 'IsAdmin'])->group(function () {
    Route::get('/', 'index');
    Route::get('/show', 'show');
    Route::post('/', 'store');
    Route::delete('/{id}', 'destroy');
    Route::put('/{id}', 'update');
});

Route::controller(ReviewController::class)->prefix('products/{id}/reviews')->group(function () {
    Route::post('/', 'store')->middleware('auth:sanctum');
});

Route::controller(AuthController::class)->group(function () {
    Route::post('/login', 'login');
    Route::post('/register', 'register');
    Route::post('/logout', 'logout')->middleware('auth:sanctum');
});

Route::controller(ProfileController::class)->prefix('profile')->middleware('auth:sanctum')->group(function () {
    Route::post('/', 'store');
    Route::post('/update', 'update');
    Route::delete('/', 'destroy');
});
