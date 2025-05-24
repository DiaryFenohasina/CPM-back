<?php

use App\Http\Controllers\CpmController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/cpm',[CpmController::class, 'addTask']);
Route::get('/cpm',[CpmController::class, 'getTask']);
Route::delete('/cpm',[CpmController::class, 'clearTasks']);
