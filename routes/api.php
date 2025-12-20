<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\AuthController;

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