<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            [
                'id'    => 1,
                'title' => 'user_management_access',
                'parent_id' => null,
            ],



            [
                'id'    => 2,
                'title' => 'permission_access',
                'parent_id' => null,
            ],
            [
                'id'    => 3,
                'title' => 'permission_create',
                'parent_id' => 2,
            ],
            [
                'id'    => 4,
                'title' => 'permission_edit',
                'parent_id' => 2,
            ],
            [
                'id'    => 5,
                'title' => 'permission_show',
                'parent_id' => 2,
            ],
            [
                'id'    => 6,
                'title' => 'permission_delete',
                'parent_id' => 2,
            ],




            [
                'id'    => 7,
                'title' => 'role_access',
                'parent_id' => null,
            ],
            [
                'id'    => 8,
                'title' => 'role_create',
                'parent_id' => 7,
            ],
            [
                'id'    => 9,
                'title' => 'role_edit',
                'parent_id' => 7,
            ],
            [
                'id'    => 10,
                'title' => 'role_show',
                'parent_id' => 7,
            ],
            [
                'id'    => 11,
                'title' => 'role_delete',
                'parent_id' => 7,
            ],




            [
                'id'    => 12,
                'title' => 'user_access',
                'parent_id' => null,
            ],
            [
                'id'    => 13,
                'title' => 'user_create',
                'parent_id' => 12,
            ],
            [
                'id'    => 14,
                'title' => 'user_edit',
                'parent_id' => 12,
            ],
            [
                'id'    => 15,
                'title' => 'user_show',
                'parent_id' => 12,
            ],
            [
                'id'    => 16,
                'title' => 'user_delete',
                'parent_id' => 12,
            ],



            [
                'id'    => 17,
                'title' => 'audit_log_access',
                'parent_id' => null,
            ],
            [
                'id'    => 18,
                'title' => 'audit_log_show',
                'parent_id' => 17,
            ],




            [
                'id'    => 19,
                'title' => 'student_information_access',
                'parent_id' => null,
            ],
            [
                'id'    => 20,
                'title' => 'class_information_access',
                'parent_id' => null,
            ],


            [
                'id'    => 21,
                'title' => 'section_access',
                'parent_id' => null,
            ],
            [
                'id'    => 22,
                'title' => 'section_create',
                'parent_id' => 21,
            ],
            [
                'id'    => 23,
                'title' => 'section_edit',
                'parent_id' => 21,
            ],
            [
                'id'    => 24,
                'title' => 'section_show',
                'parent_id' => 21,
            ],
            [
                'id'    => 25,
                'title' => 'section_delete',
                'parent_id' => 21,
            ],


            [
                'id'    => 26,
                'title' => 'shift_access',
                'parent_id' => null,
            ],
            [
                'id'    => 27,
                'title' => 'shift_create',
                'parent_id' => 26,
            ],
            [
                'id'    => 28,
                'title' => 'shift_edit',
                'parent_id' => 26,
            ],
            [
                'id'    => 29,
                'title' => 'shift_show',
                'parent_id' => 26,
            ],
            [
                'id'    => 30,
                'title' => 'shift_delete',
                'parent_id' => 26,
            ],


            [
                'id'    => 31,
                'title' => 'academic_class_access',
                'parent_id' => null,
            ],
            [
                'id'    => 32,
                'title' => 'academic_class_create',
                'parent_id' => 31,
            ],
            [
                'id'    => 33,
                'title' => 'academic_class_edit',
                'parent_id' => 31,
            ],
            [
                'id'    => 34,
                'title' => 'academic_class_show',
                'parent_id' => 31,
            ],
            [
                'id'    => 35,
                'title' => 'academic_class_delete',
                'parent_id' => 31,
            ],



            [
                'id'    => 36,
                'title' => 'student_basic_info_access',
                'parent_id' => null,
            ],
            [
                'id'    => 37,
                'title' => 'student_basic_info_create',
                'parent_id' => 36,
            ],
            [
                'id'    => 38,
                'title' => 'student_basic_info_edit',
                'parent_id' => 36,
            ],
            [
                'id'    => 39,
                'title' => 'student_basic_info_show',
                'parent_id' => 36,
            ],
            [
                'id'    => 40,
                'title' => 'student_basic_info_delete',
                'parent_id' => 36,
            ],


            [
                'id'    => 41,
                'title' => 'student_details_information_access',
                'parent_id' => null,
            ],
            [
                'id'    => 42,
                'title' => 'student_details_information_create',
                'parent_id' => 41,
            ],
            [
                'id'    => 43,
                'title' => 'student_details_information_edit',
                'parent_id' => 41,
            ],
            [
                'id'    => 44,
                'title' => 'student_details_information_show',
                'parent_id' => 41,
            ],
            [
                'id'    => 45,
                'title' => 'student_details_information_delete',
                'parent_id' => 41,
            ],


            [
                'id'    => 46,
                'title' => 'expense_category_access',
                'parent_id' => null,
            ],
            [
                'id'    => 47,
                'title' => 'expense_category_create',
                'parent_id' => 46,
            ],
            [
                'id'    => 48,
                'title' => 'expense_category_edit',
                'parent_id' => 46,
            ],
            [
                'id'    => 49,
                'title' => 'expense_category_show',
                'parent_id' => 46,
            ],
            [
                'id'    => 50,
                'title' => 'expense_category_delete',
                'parent_id' => 46,
            ],


            [
                'id'    => 51,
                'title' => 'category_access',
                'parent_id' => null,
            ],



            [
                'id'    => 52,
                'title' => 'earning_category_access',
                'parent_id' => null,
            ],
            [
                'id'    => 53,
                'title' => 'earning_category_create',
                'parent_id' => 52,
            ],
            [
                'id'    => 54,
                'title' => 'earning_category_edit',
                'parent_id' => 52,
            ],
            [
                'id'    => 55,
                'title' => 'earning_category_show',
                'parent_id' => 52,
            ],
            [
                'id'    => 56,
                'title' => 'earning_category_delete',
                'parent_id' => 52,
            ],



            [
                'id'    => 57,
                'title' => 'expense_access',
                'parent_id' => null,
            ],
            [
                'id'    => 58,
                'title' => 'expense_create',
                'parent_id' => 57,
            ],
            [
                'id'    => 59,
                'title' => 'expense_edit',
                'parent_id' => 57,
            ],
            [
                'id'    => 60,
                'title' => 'expense_show',
                'parent_id' => 57,
            ],
            [
                'id'    => 61,
                'title' => 'expense_delete',
                'parent_id' => 57,
            ],


            [
                'id'    => 62,
                'title' => 'teacher_access',
                'parent_id' => null,
            ],
            [
                'id'    => 63,
                'title' => 'teacher_create',
                'parent_id' => 62,
            ],
            [
                'id'    => 64,
                'title' => 'teacher_edit',
                'parent_id' => 62,
            ],
            [
                'id'    => 65,
                'title' => 'teacher_show',
                'parent_id' => 62,
            ],
            [
                'id'    => 66,
                'title' => 'teacher_delete',
                'parent_id' => 62,
            ],



            [
                'id'    => 67,
                'title' => 'subject_access',
                'parent_id' => null,
            ],
            [
                'id'    => 68,
                'title' => 'subject_create',
                'parent_id' => 67,
            ],
            [
                'id'    => 69,
                'title' => 'subject_edit',
                'parent_id' => 67,
            ],
            [
                'id'    => 70,
                'title' => 'subject_show',
                'parent_id' => 67,
            ],
            [
                'id'    => 71,
                'title' => 'subject_delete',
                'parent_id' => 67,
            ],



            [
                'id'    => 72,
                'title' => 'teachers_payment_access',
                'parent_id' => null,
            ],
            [
                'id'    => 73,
                'title' => 'teachers_payment_create',
                'parent_id' => 72,
            ],
            [
                'id'    => 74,
                'title' => 'teachers_payment_edit',
                'parent_id' => 72,
            ],
            [
                'id'    => 75,
                'title' => 'teachers_payment_show',
                'parent_id' => 72,
            ],
            [
                'id'    => 76,
                'title' => 'teachers_payment_delete',
                'parent_id' => 72,
            ],



            [
                'id'    => 77,
                'title' => 'earning_access',
                'parent_id' => null,
            ],
            [
                'id'    => 78,
                'title' => 'earning_create',
                'parent_id' => 77,
            ],
            [
                'id'    => 79,
                'title' => 'earning_edit',
                'parent_id' => 77,
            ],
            [
                'id'    => 80,
                'title' => 'earning_show',
                'parent_id' => 77,
            ],
            [
                'id'    => 81,
                'title' => 'earning_delete',
                'parent_id' => 77,
            ],
            



            [
                'id'    => 82,
                'title' => 'teacher_sudent_access',
                'parent_id' => null,
            ],
            [
                'id'    => 85,
                'title' => 'batch_access',
                'parent_id' => null,
            ],
            [
                'id'    => 86,
                'title' => 'batch_create',
                'parent_id' => 85,
            ],
            [
                'id'    => 87,
                'title' => 'batch_edit',
                'parent_id' => 85,
            ],
            [
                'id'    => 88,
                'title' => 'batch_show',
                'parent_id' => 85,
            ],
            [
                'id'    => 89,
                'title' => 'batch_delete',
                'parent_id' => 85,
            ],
            [
                'id'    => 83,
                'title' => 'profile_password_access',
                'parent_id' => null,
            ],
            [
                'id'    => 84,
                'title' => 'profile_password_edit',
                'parent_id' => 83,
            ],
            [
                'id'    => 90,
                'title' => 'academic_background_access',
                'parent_id' => null,
            ],
            [
                'id'    => 91,
                'title' => 'academic_background_create',
                'parent_id' => 90,
            ],
            [
                'id'    => 92,
                'title' => 'academic_background_edit',
                'parent_id' => 90,
            ],
            [
                'id'    => 93,
                'title' => 'academic_background_show',
                'parent_id' => 90,
            ],
            [
                'id'    => 94,
                'title' => 'academic_background_delete',
                'parent_id' => 90,
            ],
            [
                'id'    => 100,
                'title' => 'due_collection_access',
                'parent_id' => null,
            ],
            [
                'id'    => 101,
                'title' => 'due_collection_create',
                'parent_id' => 100,
            ],
            [
                'id'    => 102,
                'title' => 'due_collection_show',
                'parent_id' => 100,
            ],
            [
                'id'    => 103,
                'title' => 'due_collection_edit',
                'parent_id' => 100,
            ],
            [
                'id'    => 104,
                'title' => 'due_collection_delete',
                'parent_id' => 100,
            ],
            [
                'id'    => 105,
                'title' => 'batch_attendance_access',
                'parent_id' => 19,
            ],
        ];

        Permission::upsert($permissions, ['id'], ['title', 'parent_id']);
    }
}
