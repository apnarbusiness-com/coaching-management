<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\StudentBasicInfo;
use App\Models\User;
use App\Services\StudentImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StudentImportServiceTest extends TestCase
{
    use RefreshDatabase;

    protected StudentImportService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(StudentImportService::class);
        Role::create(['title' => 'Student']);
    }

    public function test_it_creates_student_user_and_details(): void
    {
        $row = $this->buildRow([
            'id_no' => 'ST-1001',
            'contact_number' => '01711001100',
            'password' => 'secret123',
        ]);

        $result = $this->service->importRow($row, StudentImportService::MODE_SKIP);

        $this->assertSame('created', $result['status']);
        $this->assertDatabaseHas('student_basic_infos', [
            'id_no' => 'ST-1001',
            'contact_number' => '01711001100',
        ]);
        $this->assertDatabaseHas('student_details_informations', [
            'student_id' => $result['student_id'],
            'guardian_contact_number' => '01711001100',
        ]);

        $user = User::where('admission_id', 'ST-1001')->first();
        $this->assertNotNull($user);
        $this->assertTrue(Hash::check('secret123', $user->password));
    }

    public function test_duplicate_skip_mode_skips_existing_student(): void
    {
        $existing = StudentBasicInfo::create([
            'first_name' => 'Existing',
            'last_name' => 'Student',
            'gender' => 'male',
            'dob' => '2000-01-01',
            'contact_number' => '01799999999',
            'status' => '1',
        ]);

        $row = $this->buildRow([
            'contact_number' => '01799999999',
            'first_name' => 'New Name',
        ]);

        $result = $this->service->importRow($row, StudentImportService::MODE_SKIP);

        $this->assertSame('skipped', $result['status']);
        $this->assertSame($existing->id, $result['student_id']);
        $this->assertDatabaseHas('student_basic_infos', [
            'id' => $existing->id,
            'first_name' => 'Existing',
        ]);
    }

    public function test_duplicate_update_mode_updates_existing_student(): void
    {
        $existing = StudentBasicInfo::create([
            'first_name' => 'Old',
            'last_name' => 'Student',
            'gender' => 'male',
            'dob' => '2000-01-01',
            'contact_number' => '01811111111',
            'status' => '1',
        ]);

        $row = $this->buildRow([
            'contact_number' => '01811111111',
            'first_name' => 'Updated Name',
            'id_no' => 'ST-UPDATED',
        ]);

        $result = $this->service->importRow($row, StudentImportService::MODE_UPDATE);

        $this->assertSame('updated', $result['status']);
        $this->assertSame($existing->id, $result['student_id']);
        $this->assertDatabaseHas('student_basic_infos', [
            'id' => $existing->id,
            'first_name' => 'Updated Name',
            'id_no' => 'ST-UPDATED',
        ]);
    }

    public function test_duplicate_mode_creates_new_row_even_if_duplicate_exists(): void
    {
        StudentBasicInfo::create([
            'first_name' => 'First',
            'last_name' => 'Student',
            'gender' => 'male',
            'dob' => '2000-01-01',
            'contact_number' => '01911111111',
            'status' => '1',
        ]);

        $row = $this->buildRow([
            'contact_number' => '01911111111',
            'id_no' => 'ST-DUP-NEW',
        ]);

        $result = $this->service->importRow($row, StudentImportService::MODE_DUPLICATE);

        $this->assertSame('created', $result['status']);
        $this->assertSame(2, StudentBasicInfo::count());
    }

    /**
     * @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    protected function buildRow(array $overrides = []): array
    {
        return array_merge([
            'roll' => null,
            'id_no' => 'ST-0001',
            'first_name' => 'Test Name',
            'last_name' => 'N/A',
            'gender' => 'others',
            'dob' => '2000-01-01',
            'contact_number' => '01700000000',
            'email' => null,
            'class_id' => null,
            'section_id' => null,
            'shift_id' => null,
            'academic_background_id' => null,
            'joining_date' => null,
            'status' => '1',
            'fathers_name' => null,
            'mothers_name' => null,
            'guardian_name' => null,
            'guardian_relation' => 'Other',
            'guardian_contact_number' => '01711001100',
            'guardian_email' => null,
            'address' => null,
            'student_blood_group' => null,
            'user_name' => null,
            'password' => null,
        ], $overrides);
    }
}

