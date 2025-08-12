<?php

use App\Http\Controllers\Web\Api\StoreSensorValueController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/store-sensor-value', StoreSensorValueController::class);
