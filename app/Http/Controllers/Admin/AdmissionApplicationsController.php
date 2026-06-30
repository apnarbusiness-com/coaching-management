<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudentBasicInfo;
use App\Models\User;
use App\Services\ReferralService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            ->when($filter === 'referred', fn ($q) => $q->whereNotNull('referred_by_user_id'))
            ->when($filter === 'no-ref', fn ($q) => $q->whereNull('referred_by_user_id'))
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

        try {
            DB::transaction(function () use ($student) {
                $lastStudent = StudentBasicInfo::whereNotNull('id_no')
                    ->orderBy('id_no', 'desc')
                    ->lockForUpdate()
                    ->first();
                $idNo = $lastStudent ? $lastStudent->id_no + 1 : 101;
                $roll = $student->roll ?? $idNo;

                $email = $student->email;

                if (empty($email)) {
                    $domain = parse_url(config('app.url'), PHP_URL_HOST) ?? 'excellency.test';
                    $email = $idNo.'@'.$domain;
                    $counter = 0;
                    while (
                        User::where('email', $email)->exists()
                        || StudentBasicInfo::where('email', $email)->where('id', '!=', $student->id)->exists()
                    ) {
                        $counter++;
                        if ($counter > 100) {
                            throw new \Exception('Could not generate unique email. Please contact support.');
                        }
                        $email = $idNo.'_'.$counter.'@'.$domain;
                    }
                } else {
                    if (
                        User::where('email', $email)->exists()
                        || StudentBasicInfo::where('email', $email)->where('id', '!=', $student->id)->exists()
                    ) {
                        throw new \Exception("A user with email \"{$email}\" already exists. Cannot create duplicate account.");
                    }
                }

                $student->update([
                    'status' => '1',
                    'id_no' => $idNo,
                    'roll' => $roll,
                    'email' => $email,
                ]);

                $user = User::create([
                    'name' => trim($student->first_name.' '.($student->last_name ?? '')),
                    'email' => $email,
                    'user_name' => generateUserName(),
                    'admission_id' => $idNo,
                    'password' => bcrypt($idNo),
                ]);

                $role = \App\Models\Role::whereIn('title', ['Student', 'student'])->first();
                if ($role) {
                    $user->roles()->sync([$role->id]);
                }

                $student->user_id = $user->id;
                $student->save();

                if ($student->referred_by_user_id) {
                    try {
                        app(ReferralService::class)->processReferralRewardByStudent($student);
                    } catch (\Exception $e) {
                        report($e);
                    }
                }
            });

            return redirect()
                ->route('admin.student-basic-infos.show', $student->id)
                ->with('status', 'Student approved successfully.');
        } catch (\Exception $e) {
            report($e);

            return redirect()
                ->route('admin.admission-applications.show', $student->id)
                ->with('error', $e->getMessage());
        }
    }

    public function destroy(StudentBasicInfo $student)
    {
        abort_if(Gate::denies('student_basic_info_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($student->status !== 'pending') {
            return redirect()
                ->route('admin.admission-applications.index')
                ->with('status', 'Only pending applications can be removed.');
        }

        try {
            DB::transaction(function () use ($student) {
                $student->studentDetails()->forceDelete();
                $student->clearMediaCollection('image');
                $student->forceDelete();
            });

            return redirect()
                ->route('admin.admission-applications.index')
                ->with('status', 'Application rejected and removed.');
        } catch (\Exception $e) {
            report($e);

            return redirect()
                ->route('admin.admission-applications.show', $student->id)
                ->with('error', 'Failed to reject application: '.$e->getMessage());
        }
    }
}
