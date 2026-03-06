<?php

namespace App\Services;

use App\Models\Role;
use App\Models\StudentBasicInfo;
use App\Models\StudentDetailsInformation;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class StudentImportService
{
    public const MODE_SKIP = 'skip';
    public const MODE_UPDATE = 'update';
    public const MODE_DUPLICATE = 'duplicate';

    /**
     * @param array<string, mixed> $row
     * @return array{status:string, student_id:int|null, message:string|null}
     */
    public function importRow(array $row, string $duplicateMode = self::MODE_SKIP): array
    {
        $duplicateMode = in_array($duplicateMode, [self::MODE_SKIP, self::MODE_UPDATE, self::MODE_DUPLICATE], true)
            ? $duplicateMode
            : self::MODE_SKIP;

        return DB::transaction(function () use ($row, $duplicateMode) {
            $existing = $this->findExistingStudent($row);
            $isUpdate = $existing instanceof StudentBasicInfo;

            if ($this->hasDuplicateAdmissionId($row, $existing)) {
                return [
                    'status' => 'duplicate_id',
                    'student_id' => $existing?->id,
                    'message' => 'Duplicate Admission ID found. Row skipped.',
                ];
            }

            if ($isUpdate && $duplicateMode === self::MODE_SKIP) {
                return [
                    'status' => 'skipped',
                    'student_id' => $existing->id,
                    'message' => 'Duplicate student skipped.',
                ];
            }

            $student = $isUpdate && $duplicateMode === self::MODE_UPDATE
                ? $existing
                : new StudentBasicInfo();

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

            $user = $this->resolveUser($student, $row, $admissionId, $password, $duplicateMode);
            $student->user_id = $user?->id;
            $student->save();

            $this->syncStudentDetails($student, $row);

            return [
                'status' => $isUpdate && $duplicateMode === self::MODE_UPDATE ? 'updated' : 'created',
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
        if (!empty($row['contact_number'])) {
            $student = StudentBasicInfo::where('contact_number', $row['contact_number'])->first();
            if ($student) {
                return $student;
            }
        }

        if (!empty($row['id_no'])) {
            $student = StudentBasicInfo::where('id_no', $row['id_no'])->first();
            if ($student) {
                return $student;
            }
        }

        if (!empty($row['email'])) {
            return StudentBasicInfo::where('email', $row['email'])->first();
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
        string $password,
        string $duplicateMode
    ): ?User {
        $studentRoleId = Role::whereIn('title', ['Student', 'student'])->value('id');

        if ($duplicateMode === self::MODE_DUPLICATE) {
            $user = User::create([
                'name' => trim($row['first_name'] . ' ' . $row['last_name']),
                'email' => $row['email'],
                'user_name' => $row['user_name'] ?: $admissionId,
                'admission_id' => $admissionId,
                'password' => $password,
            ]);
            if ($studentRoleId) {
                $user->roles()->sync([$studentRoleId]);
            }

            return $user;
        }

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

    /**
     * @param array<string, mixed> $row
     */
    protected function hasDuplicateAdmissionId(array $row, ?StudentBasicInfo $existing): bool
    {
        $admissionId = trim((string) ($row['id_no'] ?? ''));
        if ($admissionId === '') {
            return false;
        }

        $studentWithSameId = StudentBasicInfo::where('id_no', $admissionId)->first();
        if ($studentWithSameId && (!$existing || $studentWithSameId->id !== $existing->id)) {
            return true;
        }

        $userWithSameAdmission = User::where('admission_id', $admissionId)->first();
        if ($userWithSameAdmission) {
            $existingUserId = $existing?->user_id;
            if (!$existingUserId || (int) $existingUserId !== (int) $userWithSameAdmission->id) {
                return true;
            }
        }

        return false;
    }
}
