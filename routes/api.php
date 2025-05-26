<?php

use App\Http\Controllers\CpmController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/cpm',[CpmController::class, 'addTasks']);
Route::get('/cpm',[CpmController::class, 'getTasks']);
Route::delete('/cpm',[CpmController::class, 'clearTasks']);
Route::get('/critical-path', [CpmController::class, 'getCriticalPath']);
