<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE student_basic_infos MODIFY last_name VARCHAR(255) NULL');
            return;
        }

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE student_basic_infos ALTER COLUMN last_name DROP NOT NULL');
            return;
        }

        Schema::table('student_basic_infos', function (Blueprint $table) {
            $table->string('last_name')->nullable()->change();
        });
    }

    public function down(): void
    {
        DB::table('student_basic_infos')->whereNull('last_name')->update(['last_name' => '']);

        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE student_basic_infos MODIFY last_name VARCHAR(255) NOT NULL');
            return;
        }

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE student_basic_infos ALTER COLUMN last_name SET NOT NULL');
            return;
        }

        Schema::table('student_basic_infos', function (Blueprint $table) {
            $table->string('last_name')->nullable(false)->change();
        });
    }
};

