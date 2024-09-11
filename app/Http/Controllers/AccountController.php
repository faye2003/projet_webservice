<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\Transaction;

class AccountController extends Controller
{
    // Méthode pour récupérer le solde d'un compte

    // public function getBalanceTest(Request $request, $accountId)
    // {
    //     $account = new Account();
    //     $account->accountId = $request->accountId;
    //     $account->balance = $request->balance;
    //     $account->currency = $request->currency;
    //     $account->get();
    // }

    public function getBalance($accountId)
    {

        // dd($accountId);

        $account = Account::where('accountId', $accountId)->firstOrFail();
        return response()->json([
            'accountId' => $account->accountId,
            'balance' => $account->balance,
            'currency' => $account->currency
        ]);
    }

    // Méthode pour récupérer l'historique des transactions
    public function getTransactions($accountId, Request $request)
    {
        $account = Account::where('accountId', $accountId)->firstOrFail();

        $transactions = Transaction::where('account_id', $accountId)->get();


        $pagination = $account->transactions()
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('size', 10));

        return response()->json([$transactions->items(), $pagination]);
    }
    
}
