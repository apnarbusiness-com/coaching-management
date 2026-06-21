<?php

use App\Models\StudentBasicInfo;
use App\Models\User;

if (!function_exists('generateAdmissionID')) {
    function generateAdmissionID()
    {
        $last_student = StudentBasicInfo::orderBy('id_no', 'desc')->first();
        $latest_id = ($last_student) ? $last_student->id_no + 1 : 101;
        return $latest_id;
    }
}

if (!function_exists('generateUserName')) {
    function generateUserName()
    {
        $prefix = 'EXC-';
        $prefixLen = strlen($prefix);

        $numbers = User::withTrashed()
            ->where('user_name', 'like', $prefix . '%')
            ->pluck('user_name')
            ->map(fn($name) => (int) substr($name, $prefixLen))
            ->toArray();

        $num = $numbers ? max($numbers) + 1 : 1;
        $name = $prefix . str_pad($num, 3, '0', STR_PAD_LEFT);

        while (User::withTrashed()->where('user_name', $name)->exists()) {
            $num++;
            $name = $prefix . str_pad($num, 3, '0', STR_PAD_LEFT);
        }

        return $name;
    }
}

if (!function_exists('setting')) {
    function setting($key, $default = null)
    {
        $setting = \App\Models\Setting::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }
}

if (!function_exists('number_to_words')) {
    function number_to_words($number)
    {
        $number = (float) $number;
        $integer = floor($number);
        $decimal = round(($number - $integer) * 100);

        $words = convert_integer_to_words($integer);

        if ($decimal > 0) {
            $words .= ' and ' . convert_integer_to_words($decimal) . ' paisa';
        }

        return $words;
    }
}

if (!function_exists('convert_integer_to_words')) {
    function convert_integer_to_words($number)
    {
        $number = (int) $number;
        if ($number == 0) return 'zero';

        $ones = ['', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine',
                 'ten', 'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen',
                 'seventeen', 'eighteen', 'nineteen'];
        $tens = ['', '', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety'];

        $words = '';

        if ($number >= 10000000) {
            $words .= convert_integer_to_words(floor($number / 10000000)) . ' crore ';
            $number %= 10000000;
        }
        if ($number >= 100000) {
            $words .= convert_integer_to_words(floor($number / 100000)) . ' lakh ';
            $number %= 100000;
        }
        if ($number >= 1000) {
            $words .= convert_integer_to_words(floor($number / 1000)) . ' thousand ';
            $number %= 1000;
        }
        if ($number >= 100) {
            $words .= $ones[floor($number / 100)] . ' hundred ';
            $number %= 100;
        }
        if ($number > 0) {
            if ($number < 20) {
                $words .= $ones[$number];
            } else {
                $words .= $tens[floor($number / 10)];
                if ($number % 10 > 0) {
                    $words .= '-' . $ones[$number % 10];
                }
            }
        }

        return trim($words);
    }
}