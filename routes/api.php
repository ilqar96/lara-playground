<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransportController;
use App\Http\Controllers\AuthController;


Route::group(['prefix' => 'v1'], function () {

    //AUTH ROUTES

//    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);


    //TEST ROUTE
    Route::post('transport/calculate/test', [TransportController::class, 'calculatePrice']);

    Route::group(['middleware' => ['auth:sanctum'] ], function () {

        Route::post('transport/calculate', [TransportController::class, 'calculatePrice']);

        //AUTH ROUTES
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});
