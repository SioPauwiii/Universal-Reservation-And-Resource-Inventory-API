<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\CacheInspectorController;
use App\Http\Services\CacheService;

// user auth routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// item CRUD routes
// support both singular and plural endpoints used in tests
Route::post('/items', [ItemController::class, 'create']);
Route::get('/items', [ItemController::class, 'fetchAll']);

// item RETRIEVE routes
Route::get('/items/id/{id}', [ItemController::class, 'fetchOneById']);
Route::get('/items/name/{name}', [ItemController::class, 'fetchOneByName']);
Route::get('/items/sku/{sku}', [ItemController::class, 'fetchOneBySku']);

// item SEARCH route
Route::get('/items/find', [ItemController::class, 'search']);

// item UPDATE route
Route::patch('/items/update/{id}', [ItemController::class, 'update']);

// item ARCHIVE/UNARCHIVE route
Route::patch('/items/archive/{id}', [ItemController::class, 'archive']);
Route::patch('/items/unarchive/{id}', [ItemController::class, 'unarchive']);

// item DELETE route
Route::delete('/items/delete/{id}', [ItemController::class, 'delete']);

// Reservation fendpoints
// reservation CREATE and CONFIRM/DENY/CANCEL routes
Route::post('/reservations', [ReservationController::class, 'store']);
Route::post('/reservations/{id}/confirm', [ReservationController::class, 'confirm']);
Route::post('/reservations/{id}/cancel', [ReservationController::class, 'cancel']);
Route::post('/reservations/expire', [ReservationController::class, 'expire']);

// reservation RETRIEVE routes
Route::get('/reservations/id/{id}', [ReservationController::class, 'fetchById']);
Route::get('/reservations/user/{userId}', [ReservationController::class, 'fetchByUser']);
Route::get('/reservations/item/{itemId}', [ReservationController::class, 'fetchByItem']);
Route::get('/reservations/tenant/{tenantId}', [ReservationController::class, 'fetchByTenant']);

// reservation SEARCH route
// Route::get('/reservations/find', [ReservationController::class, 'search']);

// Cache inspection endpoint (for testing)
Route::get('/redis-cache/inspect/{key}', [CacheInspectorController::class, 'inspect']);
Route::post('/redis-cache/clear', [CacheService::class, 'clearAllCache']);
