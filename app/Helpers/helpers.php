<?php

use App\Models\StudentBasicInfo;

if (!function_exists('generateAdmissionID')) {
    function generateAdmissionID()
    {
        $last_student = StudentBasicInfo::orderBy('id_no', 'desc')->first();
        $latest_id = ($last_student) ? $last_student->id_no + 1 : 101;
        return $latest_id;
    }
}