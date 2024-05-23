<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/user', [AuthController::class, 'getUserData']);
    Route::get('/user/{email}', [AuthController::class, 'getUserDataByEmail']);
    Route::put('/user/name/{userId}', [AuthController::class, 'updateName']);
    Route::put('/user/password/{userId}', [AuthController::class, 'updatePassword']);
    Route::put('/user/email/{userId}', [AuthController::class, 'updateEmail']);
    Route::put('/user/phone-number/{userId}', [AuthController::class, 'updatePhoneNumber']);
    Route::get('/transactions', [AuthController::class, 'getTransactions']);
    Route::post('/recenteTransactions', [AuthController::class, 'getRecenteTransactions']);
    Route::post('/depot', [AuthController::class, 'makeDeposit']);
    Route::post('/retrait', [AuthController::class, 'makeWithdrawal']);
    Route::post('/transfert', [AuthController::class, 'transferMoney']);
});
