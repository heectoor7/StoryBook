<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\CompanyController;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', function (Request $request) {
        $user = $request->user()->load('roles');
        return response()->json($user);
    });

    Route::post('/services', [ServiceController::class, 'store']);

    Route::get('/company/profile', [CompanyController::class, 'getProfile']);
    Route::put('/company/profile', [CompanyController::class, 'updateProfile']);
});
