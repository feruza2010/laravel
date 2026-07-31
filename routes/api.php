<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProviderController;
use App\Http\Controllers\StorageController;

Route::get('/providers',              [ProviderController::class, 'index']);
Route::post('/providers',             [ProviderController::class, 'store']);
Route::get('/providers/{provider}',   [ProviderController::class, 'show']);
Route::put('/providers/{provider}',   [ProviderController::class, 'update']);
Route::delete('/providers/{provider}',[ProviderController::class, 'destroy']);

Route::get('/categories',              [CategoryController::class, 'index']);
Route::post('/categories',             [CategoryController::class, 'store']);
Route::get('/categories/{category}',   [CategoryController::class, 'show']);
Route::put('/categories/{category}',   [CategoryController::class, 'update']);
Route::delete('/categories/{category}',[CategoryController::class, 'destroy']);

Route::get('/batches',                 [BatchController::class, 'index']);
Route::post('/batches/purchase',       [BatchController::class, 'purchase']);
Route::post('/batches/refund',         [BatchController::class, 'refund']);
Route::get('/batches/profit',              [BatchController::class,   'profit']);
Route::get('/products/available',          [ProductController::class, 'available']);
Route::get('/products/remaining',          [ProductController::class, 'remainingAtDate']);
Route::get('/products',                    [ProductController::class, 'index']);
Route::post('/products',                   [ProductController::class, 'store']);
Route::get('/products/{product}',          [ProductController::class, 'show']);
Route::put('/products/{product}',          [ProductController::class, 'update']);
Route::delete('/products/{product}',       [ProductController::class, 'destroy']);
Route::get('/orders',                      [OrderController::class,   'index']);
Route::post('/orders',                     [OrderController::class,   'store']);
Route::post('/orders/refund',              [OrderController::class,   'refund']);
Route::get('/storages',                    [StorageController::class, 'index']);
Route::post('/storages',                   [StorageController::class, 'store']);
Route::get('/storages/remaining',          [StorageController::class, 'remaining']);
Route::get('/storages/{storage}',          [StorageController::class, 'show']);
Route::put('/storages/{storage}',          [StorageController::class, 'update']);
Route::delete('/storages/{storage}',       [StorageController::class, 'destroy']);
