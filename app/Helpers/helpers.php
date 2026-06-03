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