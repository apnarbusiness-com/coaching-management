<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('student_flag_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_flag_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_basic_info_id')->constrained()->onDelete('cascade');
            $table->text('comment')->nullable();
            $table->foreignId('created_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->unique(['student_flag_id', 'student_basic_info_id'], 'sflag_student_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_flag_assignments');
    }
};
