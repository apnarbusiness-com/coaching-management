<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Seeder;

class ReferralAndWalletSeeder extends Seeder
{
    public function run()
    {
        $users = User::all();

        foreach ($users as $user) {
            if (empty($user->referral_code)) {
                $user->referral_code = User::generateReferralCode($user);
                $user->save();
            }

            Wallet::firstOrCreate(
                ['user_id' => $user->id],
                ['balance' => 0, 'total_earned' => 0, 'total_withdrawn' => 0]
            );
        }

        $this->command->info('Referral codes and wallets created for all existing users.');
    }
}
