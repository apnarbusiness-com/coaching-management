<?php

namespace App\Services;

use App\Models\Wallet;
use App\Models\WalletTransaction;

class WalletService
{
    public function getOrCreateWallet($userId)
    {
        return Wallet::firstOrCreate(
            ['user_id' => $userId],
            ['balance' => 0, 'total_earned' => 0, 'total_withdrawn' => 0]
        );
    }

    public function credit($userId, $amount, $referenceType = null, $referenceId = null, $description = null)
    {
        $wallet = $this->getOrCreateWallet($userId);
        $wallet->increment('balance', $amount);
        $wallet->increment('total_earned', $amount);

        return WalletTransaction::create([
            'wallet_id' => $wallet->id,
            'amount' => $amount,
            'type' => 'credit',
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'description' => $description,
        ]);
    }

    public function debit($userId, $amount, $description = null)
    {
        $wallet = $this->getOrCreateWallet($userId);
        if ($wallet->balance < $amount) {
            throw new \Exception('Insufficient balance');
        }
        $wallet->decrement('balance', $amount);

        return WalletTransaction::create([
            'wallet_id' => $wallet->id,
            'amount' => $amount,
            'type' => 'debit',
            'description' => $description,
        ]);
    }
}
