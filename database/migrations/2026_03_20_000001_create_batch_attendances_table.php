<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('batch_attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('batch_id');
            $table->unsignedBigInteger('student_id');
            $table->date('attendance_date');
            $table->enum('status', ['present', 'absent', 'late'])->default('present');
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('recorded_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('batch_id', 'batch_attendance_batch_fk')
                ->references('id')
                ->on('batches')
                ->onDelete('cascade');

            $table->foreign('student_id', 'batch_attendance_student_fk')
                ->references('id')
                ->on('student_basic_infos')
                ->onDelete('cascade');

            $table->foreign('recorded_by', 'batch_attendance_recorder_fk')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            $table->unique(['batch_id', 'student_id', 'attendance_date'], 'batch_attendance_unique');
            $table->index(['batch_id', 'attendance_date']);
            $table->index(['student_id', 'attendance_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('batch_attendances');
    }
};
