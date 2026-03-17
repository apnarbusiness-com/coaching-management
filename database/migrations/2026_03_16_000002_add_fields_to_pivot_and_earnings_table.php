<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('batch_student_basic_info', function (Blueprint $table) {
            $table->date('enrolled_at')->nullable()->after('student_basic_info_id');
            $table->decimal('per_student_discount', 10, 2)->default(0)->after('enrolled_at');
            $table->decimal('custom_monthly_fee', 10, 2)->nullable()->after('per_student_discount');
        });

        Schema::table('earnings', function (Blueprint $table) {
            $table->unsignedBigInteger('batch_id')->nullable()->after('subject_id');
            $table->foreign('batch_id', 'earning_batch_fk')->references('id')->on('batches')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('earnings', function (Blueprint $table) {
            $table->dropForeign(['batch_id']);
            $table->dropColumn('batch_id');
        });

        Schema::table('batch_student_basic_info', function (Blueprint $table) {
            $table->dropColumn(['enrolled_at', 'per_student_discount', 'custom_monthly_fee']);
        });
    }
};
