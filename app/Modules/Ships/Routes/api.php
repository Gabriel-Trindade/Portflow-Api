<?php

use App\Modules\Ships\Http\Controllers\ShipController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/ships', [ShipController::class, 'index']);
    Route::post('/ships', [ShipController::class, 'store']);
    Route::get('/ships/{id}', [ShipController::class, 'show'])->where('id', '[0-9]+');
    Route::put('/ships/{id}', [ShipController::class, 'update'])->where('id', '[0-9]+');
    Route::patch('/ships/{id}/dock', [ShipController::class, 'dock'])->where('id', '[0-9]+');
    Route::patch('/ships/{id}/finalize', [ShipController::class, 'finalize'])->where('id', '[0-9]+');
});
