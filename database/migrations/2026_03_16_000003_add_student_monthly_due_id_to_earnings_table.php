<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('earnings', function (Blueprint $table) {
            $table->unsignedBigInteger('student_monthly_due_id')->nullable()->after('batch_id');
            $table->foreign('student_monthly_due_id', 'earning_due_fk')->references('id')->on('student_monthly_dues')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('earnings', function (Blueprint $table) {
            $table->dropForeign(['student_monthly_due_id']);
            $table->dropColumn('student_monthly_due_id');
        });
    }
};
