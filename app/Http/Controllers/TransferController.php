<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\Transfer;

class TransferController extends Controller
{
    // Méthode pour effectuer un virement
    public function transferFunds(Request $request, $accountId)
    {
        $request->validate([
            'toAccountId' => 'required|string|exists:accounts,accountId',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|string|size:3',
            'description' => 'nullable|string'
        ]);

        $fromAccount = Account::where('accountId', $accountId)->firstOrFail();
        $toAccount = Account::where('accountId', $request->toAccountId)->firstOrFail();

        // Vérification des devises
        if ($fromAccount->currency !== $request->currency || $toAccount->currency !== $request->currency) {
            return response()->json(['error' => 'Currency mismatch'], 400);
        }

        // Vérification du solde suffisant
        if ($fromAccount->balance < $request->amount) {
            return response()->json(['error' => 'Insufficient balance'], 400);
        }

        // Transaction atomique pour garantir la cohérence des données
        DB::transaction(function () use ($fromAccount, $toAccount, $request) {
            // Débit du compte source
            $fromAccount->balance -= $request->amount;
            $fromAccount->save();

            // Crédit du compte destinataire
            $toAccount->balance += $request->amount;
            $toAccount->save();

            // Création de l'enregistrement du virement
            Transfer::create([
                'transactionId' => uniqid('txn_'),
                'from_account_id' => $fromAccount->id,
                'to_account_id' => $toAccount->id,
                'amount' => $request->amount,
                'currency' => $request->currency,
                'description' => $request->description,
                'timestamp' => now()
            ]);
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Transfer completed successfully'
        ]);
    }
}
