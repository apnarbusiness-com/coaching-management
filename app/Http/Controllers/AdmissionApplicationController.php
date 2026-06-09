<?php

namespace App\Http\Controllers;

use App\Models\StudentAdmissionApplication;
use App\Models\User;
use App\Services\ReferralService;
use Illuminate\Http\Request;

class AdmissionApplicationController extends Controller
{
    public function create()
    {
        return view('admission.public');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'admission_date' => ['nullable', 'date'],
            'admission_id_no' => ['nullable', 'string', 'max:50'],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'gender' => ['required', 'in:male,female,others'],
            'dob' => ['required', 'date'],
            'contact_number' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:150'],

            'fathers_name' => ['nullable', 'string', 'max:150'],
            'mothers_name' => ['nullable', 'string', 'max:150'],
            'guardian_name' => ['nullable', 'string', 'max:150'],
            'guardian_relation' => ['nullable', 'string', 'max:100'],
            'guardian_contact_number' => ['required', 'string', 'max:50'],
            'guardian_email' => ['nullable', 'email', 'max:150'],
            'student_birth_no' => ['nullable', 'string', 'max:100'],
            'student_blood_group' => ['nullable', 'in:A+,A-,B+,B-,O+,O-,AB+,AB-'],
            'address' => ['nullable', 'string', 'max:1000'],

            'village' => ['nullable', 'string', 'max:150'],
            'post_office' => ['nullable', 'string', 'max:150'],
            'school_name' => ['nullable', 'string', 'max:200'],
            'class_name' => ['nullable', 'string', 'max:100'],
            'class_roll' => ['nullable', 'string', 'max:50'],
            'batch_name' => ['nullable', 'string', 'max:150'],
            'subjects' => ['nullable', 'array'],
            'subjects.*' => ['string', 'in:Bangla,English,Math,Science,ICT'],
            'photo' => ['nullable', 'image', 'max:2048'],
            'terms' => ['accepted'],
            'referral_code' => ['nullable', 'string', 'max:20'],
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('admission-photos', 'public');
        }

        $referredByUserId = null;
        if (!empty($validated['referral_code'])) {
            $referrer = User::where('referral_code', $validated['referral_code'])->first();
            if ($referrer) {
                $referredByUserId = $referrer->id;
            }
        }

        $application = StudentAdmissionApplication::create([
            'admission_date' => $validated['admission_date'] ?? null,
            'admission_id_no' => $validated['admission_id_no'] ?? null,
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'] ?? null,
            'gender' => $validated['gender'],
            'dob' => $validated['dob'],
            'contact_number' => $validated['contact_number'],
            'email' => $validated['email'] ?? null,
            'fathers_name' => $validated['fathers_name'] ?? null,
            'mothers_name' => $validated['mothers_name'] ?? null,
            'guardian_name' => $validated['guardian_name'] ?? null,
            'guardian_relation' => $validated['guardian_relation'] ?? null,
            'guardian_contact_number' => $validated['guardian_contact_number'],
            'guardian_email' => $validated['guardian_email'] ?? null,
            'student_birth_no' => $validated['student_birth_no'] ?? null,
            'student_blood_group' => $validated['student_blood_group'] ?? null,
            'address' => $validated['address'] ?? null,
            'village' => $validated['village'] ?? null,
            'post_office' => $validated['post_office'] ?? null,
            'school_name' => $validated['school_name'] ?? null,
            'class_name' => $validated['class_name'] ?? null,
            'class_roll' => $validated['class_roll'] ?? null,
            'batch_name' => $validated['batch_name'] ?? null,
            'subjects' => $validated['subjects'] ?? null,
            'photo_path' => $photoPath,
            'status' => 'pending',
            'referral_code' => $validated['referral_code'] ?? null,
            'referred_by_user_id' => $referredByUserId,
        ]);

        return redirect()
            ->route('admission.public.thankyou', $application->id)
            ->with('status', 'Application submitted successfully.');
    }

    public function thankYou(StudentAdmissionApplication $application)
    {
        return view('admission.thankyou', compact('application'));
    }

    public function checkReferral(Request $request)
    {
        $code = $request->query('code');
        if (empty($code)) {
            return response()->json(['valid' => false]);
        }
        $user = User::where('referral_code', $code)->first();
        if ($user) {
            return response()->json(['valid' => true, 'name' => $user->name]);
        }
        return response()->json(['valid' => false]);
    }
}
