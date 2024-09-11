<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/accounts/{accountId}/balance', [AccountController::class, 'getBalance']);
Route::get('/accounts/{accountId}/balancetest', [AccountController::class, 'getBalanceTest']);
Route::get('/accounts/{accountId}/transactions', [AccountController::class, 'getTransactions']);
Route::post('/accounts/{accountId}/transfer', [TransferController::class, 'transferFunds']);