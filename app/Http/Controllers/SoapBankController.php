<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\Transfer;

class SoapBankController extends Controller
{
    // Méthode pour obtenir le solde d'un compte
    public function getAccountBalance($accountId)
    {
        $account = Account::where('accountId', $accountId)->firstOrFail();
        return [
            'accountId' => $account->accountId,
            'balance' => $account->balance,
            'currency' => $account->currency
        ];
    }

    // Méthode pour récupérer l'historique des transactions
    public function getTransactions($accountId, $pageNumber = 1, $pageSize = 10)
    {
        $account = Account::where('accountId', $accountId)->firstOrFail();
        $transactions = $account->transactions()
            ->orderBy('created_at', 'desc')
            ->paginate($pageSize, ['*'], 'page', $pageNumber);
        return [
            'transactions' => $transactions->items(), 
            'currentPage' => $transactions->currentPage(),
            'totalPages' => $transactions->lastPage(),
            'totalItems' => $transactions->total()
        ];
    }


    // Méthode pour effectuer un virement
    public function transferFunds($creditorId, $debtorId, $amount, $currency)
    {
        // Récupérer les comptes créditeur et débiteur
        $creditor = Account::where('accountId', $creditorId)->firstOrFail();
        $debtor = Account::where('accountId', $debtorId)->firstOrFail();
    
        // Vérification des soldes
        if ($debtor->balance < $amount) {
            return ['error' => 'Insufficient balance'];
        }
    
        // Vérification de la correspondance des devises
        if ($creditor->currency !== $currency || $debtor->currency !== $currency) {
            return ['error' => 'Currency mismatch'];
        }
    
        // Effectuer le virement
        $debtor->balance -= $amount;
        $creditor->balance += $amount;
    
        $debtor->save();
        $creditor->save();
    
        // Enregistrer la transaction
        Transfer::create([
            'transactionId' => uniqid('txn_'),
            'from_account_id' => $debtor->id,
            'to_account_id' => $creditor->id,
            'amount' => $amount,
            'currency' => $currency,
            'timestamp' => now(),
        ]);
    
        return ['status' => 'success', 'message' => 'Transfer completed successfully'];
    }
    
}
