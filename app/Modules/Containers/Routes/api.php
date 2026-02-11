<?php

use App\Modules\Containers\Http\Controllers\ContainerController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/containers', [ContainerController::class, 'index']);
    Route::post('/containers', [ContainerController::class, 'store']);
    Route::get('/containers/{id}', [ContainerController::class, 'show'])->where('id', '[0-9]+');
    Route::put('/containers/{id}', [ContainerController::class, 'update'])->where('id', '[0-9]+');
    Route::patch('/containers/{id}/status', [ContainerController::class, 'changeStatus'])->where('id', '[0-9]+');
});
