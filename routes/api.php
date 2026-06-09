<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::controller(OrderController::class)->prefix('order')->middleware('auth:api')->group(function () {
    Route::get('/', 'index')->middleware('IsAdmin');
    Route::post('/', 'store')->middleware('verified');
    Route::get('/expensive', 'expensive')->middleware('IsAdmin');
    Route::get('/trash', 'trashOrders')->middleware('IsAdmin');
    Route::delete('/{id}', 'destroy')->middleware('verified');;  //admin or owner
    Route::put('/{id}/status', 'updateStatus')->middleware('IsAdmin');
});

Route::controller(ProductController::class)->prefix('products')->group(function () {
    Route::get('/', 'index');
    Route::get('/{id}', 'show');
    Route::post('/', 'store')->middleware(['auth:api', 'IsAdmin']);
    Route::delete('/{id}', 'destroy')->middleware(['auth:api', 'IsAdmin']);
    Route::put('/{id}', 'update')->middleware(['auth:api', 'IsAdmin']);
});

Route::controller(UserController::class)->prefix('user')->middleware(['auth:api', 'IsAdmin'])->group(function () {
    Route::get('/', 'index');
    Route::get('/show', 'show');
    Route::post('/', 'store');
    Route::delete('/{id}', 'destroy');
    Route::put('/{id}', 'update');
});

Route::controller(ReviewController::class)->prefix('products/{id}/reviews')->middleware(['auth:api', 'verified'])->group(function () {
    Route::post('/', 'store');
});

Route::controller(AuthController::class)->group(function () {
    Route::post('/login', 'login');
    Route::post('/register', 'register');
    Route::post('/logout', 'logout')->middleware('auth:api');
    Route::post('/verify-email', 'verifyEmail')->middleware('auth:api');
    Route::post('/forgot-password', 'forgotPassword');
    Route::post('/reset-password', 'resetPassword');
    Route::get('/me','me')->middleware('auth:api');
});

Route::controller(ProfileController::class)->prefix('profile')->middleware(['auth:api', 'verified'])->group(function () {
    Route::post('/', 'store');
    Route::post('/update', 'update');
    Route::delete('/', 'destroy');
});
