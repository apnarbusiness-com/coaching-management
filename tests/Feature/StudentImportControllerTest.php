<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StudentImportControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_parse_and_process_student_import_creates_login_ready_records(): void
    {
        $user = $this->createAuthorizedUser([
            'student_basic_info_access',
        ]);

        $csv = implode("\n", [
            ',,,,,,,,,',
            'ID,Name,Mobile,Guardian,Active Status,Joining Date,Address,Blood Group,Password',
            '1001,Rahim,01770000001,01770000002,Yes,2026-01-15,Nogorbari,A+,pass@123',
        ]);

        $file = UploadedFile::fake()->createWithContent('students.csv', $csv);

        $parseResponse = $this
            ->actingAs($user)
            ->from(route('admin.student-basic-infos.index'))
            ->post(route('admin.student-basic-infos.parseStudentImport'), [
                'csv_file' => $file,
                'duplicate_mode' => 'skip',
            ]);

        $parseResponse->assertOk();
        $parseResponse->assertSee('Student Import Preview');

        $html = $parseResponse->getContent();
        preg_match('/name="filename" value="([^"]+)"/', $html, $filenameMatch);
        preg_match('/name="headerIndex" value="([^"]+)"/', $html, $headerMatch);

        $this->assertArrayHasKey(1, $filenameMatch);
        $this->assertArrayHasKey(1, $headerMatch);

        $processResponse = $this
            ->actingAs($user)
            ->post(route('admin.student-basic-infos.processStudentImport'), [
                'filename' => $filenameMatch[1],
                'redirect' => route('admin.student-basic-infos.index'),
                'headerIndex' => $headerMatch[1],
                'duplicate_mode' => 'skip',
            ]);

        $processResponse->assertRedirect(route('admin.student-basic-infos.index'));
        $this->assertDatabaseHas('student_basic_infos', [
            'id_no' => '1001',
            'contact_number' => '01770000001',
            'first_name' => 'Rahim',
        ]);
        $this->assertDatabaseHas('student_details_informations', [
            'guardian_contact_number' => '01770000002',
            'address' => 'Nogorbari',
        ]);

        $importedUser = User::where('admission_id', '1001')->first();
        $this->assertNotNull($importedUser);
        $this->assertTrue(Hash::check('pass@123', $importedUser->password));
    }

    public function test_manual_store_flow_still_works(): void
    {
        $user = $this->createAuthorizedUser([
            'student_basic_info_create',
            'student_basic_info_access',
        ]);

        $response = $this
            ->actingAs($user)
            ->post(route('admin.student-basic-infos.store'), [
                'roll' => 1,
                'id_no' => 'REG-1001',
                'first_name' => 'Manual',
                'last_name' => 'Student',
                'gender' => 'male',
                'dob' => '2008-01-01',
                'contact_number' => '01610000000',
                'email' => 'manual.student@example.com',
                'status' => '1',
                'need_login' => 1,
                'password' => 'manualPass',
                'fathers_name' => 'Father',
                'mothers_name' => 'Mother',
                'guardian_name' => 'Father',
                'guardian_relation_type' => 'Father',
                'guardian_contact_number' => '01610000000',
                'guardian_email' => 'guardian@example.com',
                'address' => 'Test Address',
                'student_blood_group' => 'A+',
                'subjects' => [],
                'batches' => [],
            ]);

        $response->assertRedirect(route('admin.student-basic-infos.index'));
        $this->assertDatabaseHas('student_basic_infos', [
            'id_no' => 'REG-1001',
            'contact_number' => '01610000000',
        ]);
        $this->assertDatabaseHas('users', [
            'admission_id' => 'REG-1001',
        ]);
    }

    /**
     * @param array<int, string> $permissionTitles
     */
    protected function createAuthorizedUser(array $permissionTitles): User
    {
        $role = Role::create(['title' => 'Admin']);

        $permissionIds = [];
        foreach ($permissionTitles as $title) {
            $permissionIds[] = Permission::create(['title' => $title])->id;
        }
        $role->permissions()->sync($permissionIds);

        // Needed for imported students.
        Role::firstOrCreate(['title' => 'Student']);

        $user = User::factory()->create();
        $user->roles()->sync([$role->id]);

        return $user;
    }
}

