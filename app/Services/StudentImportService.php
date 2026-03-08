<?php

namespace App\Services;

use App\Models\Role;
use App\Models\StudentBasicInfo;
use App\Models\StudentDetailsInformation;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class StudentImportService
{
    /**
     * @param array<string, mixed> $row
     * @return array{status:string, student_id:int|null, message:string|null}
     */
    public function importRow(array $row): array
    {
        return DB::transaction(function () use ($row) {
            $existing = $this->findExistingStudent($row);
            $isUpdate = $existing instanceof StudentBasicInfo;

            $student = $isUpdate ? $existing : new StudentBasicInfo();

            $student->roll = $row['roll'];
            $student->id_no = $row['id_no'];
            $student->first_name = $row['first_name'];
            $student->last_name = $row['last_name'];
            $student->gender = $row['gender'];
            $student->dob = $row['dob'];
            $student->contact_number = $row['contact_number'];
            $student->email = $row['email'];
            $student->class_id = $row['class_id'];
            $student->section_id = $row['section_id'];
            $student->shift_id = $row['shift_id'];
            $student->academic_background_id = $row['academic_background_id'];
            $student->joining_date = $row['joining_date'];
            $student->status = $row['status'];
            $student->save();

            $admissionId = $this->resolveAdmissionId($row, $student->id);
            $password = $row['password'] ?: $admissionId;

            $user = $this->resolveUser($student, $row, $admissionId, $password);
            $student->user_id = $user?->id;
            $student->save();

            $this->syncStudentDetails($student, $row);

            return [
                'status' => $isUpdate ? 'updated' : 'created',
                'student_id' => $student->id,
                'message' => null,
            ];
        });
    }

    /**
     * @param array<string, mixed> $row
     */
    protected function findExistingStudent(array $row): ?StudentBasicInfo
    {
        $idNo = trim((string) ($row['id_no'] ?? ''));
        if ($idNo !== '') {
            $student = StudentBasicInfo::where('id_no', $idNo)->first();
            if ($student) {
                return $student;
            }
            $user = User::where('admission_id', $idNo)->first();
            if ($user) {
                return StudentBasicInfo::where('user_id', $user->id)->first();
            }
        }

        return null;
    }

    /**
     * @param array<string, mixed> $row
     */
    protected function resolveAdmissionId(array $row, int $studentId): string
    {
        if (!empty($row['id_no'])) {
            return (string) $row['id_no'];
        }

        return 'IMP-' . str_pad((string) $studentId, 6, '0', STR_PAD_LEFT);
    }

    /**
     * @param array<string, mixed> $row
     */
    protected function resolveUser(
        StudentBasicInfo $student,
        array $row,
        string $admissionId,
        string $password
    ): ?User {
        $studentRoleId = Role::whereIn('title', ['Student', 'student'])->value('id');

        $user = null;

        if ($student->user_id) {
            $user = User::find($student->user_id);
        }

        if (!$user && !empty($admissionId)) {
            $user = User::where('admission_id', $admissionId)->first();
        }

        if (!$user && !empty($row['email'])) {
            $user = User::where('email', $row['email'])->first();
        }

        $userPayload = [
            'name' => trim($row['first_name'] . ' ' . $row['last_name']),
            'email' => $row['email'],
            'user_name' => $row['user_name'] ?: $admissionId,
            'admission_id' => $admissionId,
        ];

        if ($user) {
            if (!empty($row['password'])) {
                $userPayload['password'] = $password;
            }
            $user->update($userPayload);
        } else {
            $userPayload['password'] = $password;
            $user = User::create($userPayload);
        }

        if ($studentRoleId) {
            $user->roles()->sync([$studentRoleId]);
        }

        return $user;
    }

    /**
     * @param array<string, mixed> $row
     */
    protected function syncStudentDetails(StudentBasicInfo $student, array $row): void
    {
        $details = StudentDetailsInformation::firstOrNew(['student_id' => $student->id]);

        $details->fill([
            'fathers_name' => $row['fathers_name'],
            'mothers_name' => $row['mothers_name'],
            'guardian_name' => $row['guardian_name'],
            'guardian_relation' => $row['guardian_relation'],
            'guardian_contact_number' => $row['guardian_contact_number'],
            'guardian_email' => $row['guardian_email'],
            'address' => $row['address'],
            'student_blood_group' => $row['student_blood_group'],
        ]);
        $details->save();
    }

}
