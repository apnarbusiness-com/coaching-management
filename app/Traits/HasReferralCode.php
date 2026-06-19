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
        $code = null;

        if (!empty($user->user_name)) {
            $code = $user->user_name;
        } elseif (!empty($user->admission_id)) {
            $code = $user->admission_id;
        }

        if ($code) {
            $baseCode = $code;
            $i = 1;
            while (static::where(function ($q) use ($code) {
                    $q->where('referral_code', $code)
                      ->orWhere('user_name', $code)
                      ->orWhere('admission_id', $code);
                })->exists()) {
                $code = $baseCode . '_' . $i;
                $i++;
            }
            return $code;
        }

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

        while (static::where(function ($q) use ($code) {
                $q->where('referral_code', $code)
                  ->orWhere('user_name', $code)
                  ->orWhere('admission_id', $code);
            })->exists()) {
            $random = strtoupper(Str::random(4));
            $code = $prefix . $namePart . $random;
        }

        return $code;
    }
}
