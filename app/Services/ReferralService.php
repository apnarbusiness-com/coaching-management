<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\ReferralCampaign;
use App\Models\StudentAdmissionApplication;
use App\Models\StudentBasicInfo;
use App\Models\User;

class ReferralService
{
    public function __construct(
        protected WalletService $walletService
    ) {}

    public function lookupReferrer($referralCode)
    {
        if (empty($referralCode)) {
            return null;
        }
        return User::where('referral_code', $referralCode)
            ->where('wallet_access', true)
            ->first();
    }

    public function getActiveCampaign()
    {
        return ReferralCampaign::active()
            ->where(function ($q) {
                $q->whereNull('start_date')
                  ->orWhere('start_date', '<=', now()->format('Y-m-d'));
            })
            ->where(function ($q) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', now()->format('Y-m-d'));
            })
            ->orderBy('reward_amount', 'desc')
            ->first();
    }

    public function processReferralReward(StudentAdmissionApplication $application)
    {
        if (empty($application->referred_by_user_id)) {
            return null;
        }

        $campaign = $this->getActiveCampaign();
        if (!$campaign) {
            return null;
        }

        $referrer = User::find($application->referred_by_user_id);
        if (!$referrer || !$referrer->wallet_access) {
            return null;
        }

        return $this->walletService->credit(
            $referrer->id,
            $campaign->reward_amount,
            'App\Models\StudentAdmissionApplication',
            $application->id,
            "Referral reward for {$application->first_name} {$application->last_name} via campaign: {$campaign->name}"
        );
    }

    public function processReferralRewardByStudent(StudentBasicInfo $student)
    {
        if (empty($student->referred_by_user_id)) {
            return null;
        }

        $campaign = $this->getActiveCampaign();
        if (!$campaign) {
            return null;
        }

        $referrer = User::find($student->referred_by_user_id);
        if (!$referrer || !$referrer->wallet_access) {
            return null;
        }

        $transaction = $this->walletService->credit(
            $referrer->id,
            $campaign->reward_amount,
            'App\Models\StudentBasicInfo',
            $student->id,
            "Referral reward for {$student->first_name} {$student->last_name} via campaign: {$campaign->name}"
        );

        $this->sendReferralRewardNotification($referrer, $student, $campaign->reward_amount);

        return $transaction;
    }

    protected function sendReferralRewardNotification(User $referrer, StudentBasicInfo $student, $rewardAmount)
    {
        Notification::create([
            'user_id' => $referrer->id,
            'type' => 'referral_reward',
            'data' => [
                'student_name' => $student->first_name . ' ' . ($student->last_name ?? ''),
                'student_id' => $student->id,
                'reward_amount' => $rewardAmount,
            ],
            'link' => route('admin.wallet.index'),
        ]);
    }
}
