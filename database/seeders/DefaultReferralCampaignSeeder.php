<?php

namespace Database\Seeders;

use App\Models\ReferralCampaign;
use Illuminate\Database\Seeder;

class DefaultReferralCampaignSeeder extends Seeder
{
    public function run()
    {
        ReferralCampaign::firstOrCreate(
            ['name' => 'HSC-26 Farewell Referral'],
            [
                'reward_amount' => 500,
                'description' => 'HSC-26 batch farewell referral campaign. Refer new students and earn 500 TK per successful enrollment.',
                'start_date' => now()->startOfMonth()->format('Y-m-d'),
                'end_date' => null,
                'is_active' => true,
            ]
        );

        $this->command->info('Default referral campaign created: HSC-26 Farewell Referral (500 TK).');
    }
}
