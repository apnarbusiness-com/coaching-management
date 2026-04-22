<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('batch_student_basic_info', function (Blueprint $table) {
            $table->decimal('one_time_discount', 10, 2)->default(0)->after('per_student_discount');
        });
    }

    public function down(): void
    {
        Schema::table('batch_student_basic_info', function (Blueprint $table) {
            $table->dropColumn('one_time_discount');
        });
    }
};