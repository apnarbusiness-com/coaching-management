<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasReferralCode
{
    public static function bootHasReferralCode()
    {
        static::created(function ($model) {
            if (empty($model->referral_code)) {
                $model->referral_code = static::generateReferralCode($model);
                $model->save();
            }
        });
    }

    public static function generateReferralCode($user)
    {
        $prefix = 'REF';
        $namePart = '';
        if ($user->name) {
            $words = explode(' ', $user->name);
            $namePart = '';
            foreach ($words as $w) {
                $namePart .= strtoupper(substr($w, 0, 1));
            }
            $namePart = substr($namePart, 0, 3);
        }
        $random = strtoupper(Str::random(4));
        $code = $prefix . $namePart . $random;

        while (static::where('referral_code', $code)->exists()) {
            $random = strtoupper(Str::random(4));
            $code = $prefix . $namePart . $random;
        }

        return $code;
    }
}
