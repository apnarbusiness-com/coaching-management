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
        $count = User::whereNull('admission_id')->count();
        return 'EXC-' . str_pad($count + 1, 3, '0', STR_PAD_LEFT);
    }
}