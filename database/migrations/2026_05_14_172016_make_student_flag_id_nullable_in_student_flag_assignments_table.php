<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE student_flag_assignments DROP FOREIGN KEY student_flag_assignments_student_flag_id_foreign');
        DB::statement('ALTER TABLE student_flag_assignments DROP INDEX sflag_student_unique');
        DB::statement('ALTER TABLE student_flag_assignments MODIFY student_flag_id BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE student_flag_assignments ADD CONSTRAINT student_flag_assignments_student_flag_id_foreign FOREIGN KEY (student_flag_id) REFERENCES student_flags(id) ON DELETE CASCADE');
        DB::statement('ALTER TABLE student_flag_assignments ADD UNIQUE INDEX sflag_student_unique (student_flag_id, student_basic_info_id)');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE student_flag_assignments DROP FOREIGN KEY student_flag_assignments_student_flag_id_foreign');
        DB::statement('ALTER TABLE student_flag_assignments DROP INDEX sflag_student_unique');
        DB::statement('ALTER TABLE student_flag_assignments MODIFY student_flag_id BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE student_flag_assignments ADD CONSTRAINT student_flag_assignments_student_flag_id_foreign FOREIGN KEY (student_flag_id) REFERENCES student_flags(id) ON DELETE CASCADE');
        DB::statement('ALTER TABLE student_flag_assignments ADD UNIQUE INDEX sflag_student_unique (student_flag_id, student_basic_info_id)');
    }
};
