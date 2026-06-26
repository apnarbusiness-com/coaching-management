<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudentBasicInfo;
use App\Models\User;
use App\Services\ReferralService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class AdmissionApplicationsController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('student_basic_info_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $search = $request->get('search');
        $filter = $request->get('filter', 'all');

        $students = StudentBasicInfo::with('studentDetails', 'referredBy')
            ->where('status', 'pending')
            ->when($filter === 'referred', fn($q) => $q->whereNotNull('referred_by_user_id'))
            ->when($filter === 'no-ref', fn($q) => $q->whereNull('referred_by_user_id'))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('id', $search)
                      ->orWhere('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('contact_number', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(20)
            ->appends(['search' => $search, 'filter' => $filter]);

        return view('admin.admissionApplications.index', compact('students', 'search', 'filter'));
    }

    public function show(StudentBasicInfo $student)
    {
        abort_if(Gate::denies('student_basic_info_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $student->load('studentDetails', 'referredBy.wallet');

        return view('admin.admissionApplications.show', compact('student'));
    }

    public function approve(Request $request, StudentBasicInfo $student)
    {
        abort_if(Gate::denies('student_basic_info_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($student->status !== 'pending') {
            return redirect()
                ->route('admin.admission-applications.show', $student->id)
                ->with('status', 'Already processed.');
        }

        $idNo = generateAdmissionID();
        $roll = $student->roll ?? generateAdmissionID();

        $student->update([
            'status' => '1',
            'id_no' => $idNo,
            'roll' => $roll,
        ]);

        $student->refresh();

        if (!$student->user_id) {
            if (User::where('email', $student->email)->exists()) {
                return redirect()
                    ->route('admin.admission-applications.show', $student->id)
                    ->with('error', "A user with email \"{$student->email}\" already exists. Cannot create duplicate account.");
            }

            $user = User::create([
                'name' => trim($student->first_name . ' ' . ($student->last_name ?? '')),
                'email' => $student->email,
                'user_name' => generateUserName(),
                'admission_id' => $idNo,
                'password' => bcrypt($idNo),
            ]);

            $user->roles()->sync(\App\Models\Role::whereIn('title', ['Student', 'student'])->first()->id ?? []);

            $student->user_id = $user->id;
            $student->save();
        }

        if ($student->referred_by_user_id) {
            try {
                $referralService = app(ReferralService::class);
                $referralService->processReferralRewardByStudent($student);
            } catch (\Exception $e) {
                report($e);
            }
        }

        return redirect()
            ->route('admin.student-basic-infos.show', $student->id)
            ->with('status', 'Student approved successfully.');
    }

    public function destroy(StudentBasicInfo $student)
    {
        abort_if(Gate::denies('student_basic_info_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($student->status !== 'pending') {
            return redirect()
                ->route('admin.admission-applications.index')
                ->with('status', 'Only pending applications can be removed.');
        }

        $student->studentDetails()->delete();
        $student->clearMediaCollection('image');
        $student->forceDelete();

        return redirect()
            ->route('admin.admission-applications.index')
            ->with('status', 'Application rejected and removed.');
    }
}
