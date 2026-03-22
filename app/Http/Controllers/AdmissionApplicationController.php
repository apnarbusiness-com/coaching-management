<?php

namespace App\Http\Controllers;

use App\Models\StudentAdmissionApplication;
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
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('admission-photos', 'public');
        }

        $application = StudentAdmissionApplication::create(array_merge($validated, [
            'photo_path' => $photoPath,
            'status' => 'pending',
        ]));

        return redirect()
            ->route('admission.public.thankyou', $application->id)
            ->with('status', 'Application submitted successfully.');
    }

    public function thankYou(StudentAdmissionApplication $application)
    {
        return view('admission.thankyou', compact('application'));
    }
}
