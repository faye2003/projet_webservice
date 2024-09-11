<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\SoapBankController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/accounts/{accountId}/balance', [AccountController::class, 'getBalance']);
Route::get('/accounts/{accountId}/balancetest', [AccountController::class, 'getBalanceTest']);
Route::get('/accounts/{accountId}/transactions', [AccountController::class, 'getTransactions']);
Route::post('/accounts/{accountId}/transfer', [TransferController::class, 'transferFunds']);

Route::any('/soap', function () {
    $options = [
        'uri' => url('/soap'),
        'location' => url('/soap'),
        'soap_version' => SOAP_1_2,
    ];
    $server = new SoapServer(null, $options);
    $server->setObject(new SoapBankController());
    $server->handle();
});