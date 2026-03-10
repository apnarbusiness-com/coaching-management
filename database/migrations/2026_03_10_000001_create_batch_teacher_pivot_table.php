<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('batch_teacher', function (Blueprint $table) {
            $table->unsignedBigInteger('batch_id');
            $table->unsignedBigInteger('teacher_id');
            $table->decimal('salary_amount', 15, 2)->default(0);
            $table->string('role')->nullable();
            $table->timestamps();

            $table->unique(['batch_id', 'teacher_id'], 'batch_teacher_unique');
            $table->foreign('batch_id', 'batch_teacher_batch_fk')->references('id')->on('batches')->onDelete('cascade');
            $table->foreign('teacher_id', 'batch_teacher_teacher_fk')->references('id')->on('teachers')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('batch_teacher');
    }
};
