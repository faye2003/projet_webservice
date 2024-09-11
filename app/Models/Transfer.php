<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Account;
use App\Models\Transaction;

class Transfer extends Model
{
    use HasFactory;

    protected $fillable = ['transactionId', 'from_account_id', 'to_account_id', 'amount', 'currency', 'description', 'timestamp'];

    public function fromAccount()
    {
        return $this->belongsTo(Account::class, 'from_account_id');
    }

    public function toAccount()
    {
        return $this->belongsTo(Account::class, 'to_account_id');
    }
}
