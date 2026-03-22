<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudentAdmissionApplication;
use App\Models\StudentBasicInfo;
use App\Models\StudentDetailsInformation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class AdmissionApplicationsController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('student_basic_info_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $applications = StudentAdmissionApplication::query()
            ->orderByRaw("case when status = 'pending' then 0 when status = 'approved' then 1 else 2 end")
            ->latest()
            ->paginate(20);

        return view('admin.admissionApplications.index', compact('applications'));
    }

    public function show(StudentAdmissionApplication $application)
    {
        abort_if(Gate::denies('student_basic_info_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.admissionApplications.show', compact('application'));
    }

    public function approve(Request $request, StudentAdmissionApplication $application)
    {
        abort_if(Gate::denies('student_basic_info_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($application->status === 'approved') {
            return redirect()
                ->route('admin.admission-applications.show', $application->id)
                ->with('status', 'Already approved.');
        }

        $student = DB::transaction(function () use ($application) {
            $student = StudentBasicInfo::create([
                'roll' => $this->resolveRoll($application->class_roll),
                'id_no' => $application->admission_id_no,
                'first_name' => $application->first_name,
                'last_name' => $application->last_name ?? '',
                'gender' => $application->gender,
                'contact_number' => $application->contact_number,
                'email' => $application->email,
                'dob' => $application->dob,
                'status' => '1',
                'joining_date' => $application->admission_date
                    ? Carbon::parse($application->admission_date)->format('Y-m-d 00:00:00')
                    : null,
            ]);

            $referencePayload = [
                'school_name' => $application->school_name,
                'class_name' => $application->class_name,
                'batch_name' => $application->batch_name,
                'subjects' => $application->subjects,
                'village' => $application->village,
                'post_office' => $application->post_office,
                'class_roll' => $application->class_roll,
                'admission_id_no' => $application->admission_id_no,
            ];

            StudentDetailsInformation::create([
                'fathers_name' => $application->fathers_name,
                'mothers_name' => $application->mothers_name,
                'guardian_name' => $application->guardian_name,
                'guardian_relation' => $application->guardian_relation,
                'guardian_contact_number' => $application->guardian_contact_number,
                'guardian_email' => $application->guardian_email,
                'student_birth_no' => $application->student_birth_no,
                'student_blood_group' => $application->student_blood_group,
                'address' => $this->resolveAddress($application),
                'reference' => json_encode($referencePayload),
                'student_id' => $student->id,
            ]);

            $application->update([
                'status' => 'approved',
                'student_id' => $student->id,
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            return $student;
        });

        return redirect()
            ->route('admin.student-basic-infos.show', $student->id)
            ->with('status', 'Student created from application.');
    }

    public function destroy(StudentAdmissionApplication $application)
    {
        abort_if(Gate::denies('student_basic_info_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $application->delete();

        return redirect()
            ->route('admin.admission-applications.index')
            ->with('status', 'Application removed.');
    }

    protected function resolveRoll(?string $classRoll): ?int
    {
        if (!$classRoll) {
            return null;
        }

        if (!is_numeric($classRoll)) {
            return null;
        }

        return (int) $classRoll;
    }

    protected function resolveAddress(StudentAdmissionApplication $application): ?string
    {
        if ($application->address) {
            return $application->address;
        }

        $parts = array_filter([
            $application->village ? 'Village: ' . $application->village : null,
            $application->post_office ? 'P.O: ' . $application->post_office : null,
        ]);

        if (empty($parts)) {
            return null;
        }

        return implode(', ', $parts);
    }
}
