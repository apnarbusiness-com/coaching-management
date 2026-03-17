<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_monthly_dues', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('batch_id');
            $table->unsignedBigInteger('academic_class_id')->nullable();
            $table->unsignedBigInteger('section_id')->nullable();
            $table->unsignedBigInteger('shift_id')->nullable();
            $table->integer('month');
            $table->integer('year');
            $table->decimal('due_amount', 10, 2);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('due_remaining', 10, 2)->default(0);
            $table->enum('status', ['paid', 'partial', 'unpaid'])->default('unpaid');
            $table->date('due_date');
            $table->date('paid_date')->nullable();
            $table->timestamps();

            $table->foreign('student_id', 'due_student_fk')->references('id')->on('student_basic_infos')->onDelete('cascade');
            $table->foreign('batch_id', 'due_batch_fk')->references('id')->on('batches')->onDelete('cascade');
            $table->foreign('academic_class_id', 'due_class_fk')->references('id')->on('academic_classes')->onDelete('set null');
            $table->foreign('section_id', 'due_section_fk')->references('id')->on('sections')->onDelete('set null');
            $table->foreign('shift_id', 'due_shift_fk')->references('id')->on('shifts')->onDelete('set null');

            $table->unique(['student_id', 'batch_id', 'month', 'year'], 'due_unique');
            $table->index(['month', 'year']);
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_monthly_dues');
    }
};
