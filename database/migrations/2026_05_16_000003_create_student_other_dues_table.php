<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_other_dues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('student_basic_infos');
            $table->foreignId('earning_category_id')->constrained('earning_categories');
            $table->foreignId('batch_id')->nullable()->constrained('batches');
            $table->foreignId('subject_id')->nullable()->constrained('subjects');
            $table->string('title');
            $table->decimal('amount', 15, 2);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->string('academic_background')->nullable();
            $table->string('exam_year')->nullable();
            $table->text('details')->nullable();
            $table->date('due_date')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('payment_proof_details')->nullable();
            $table->string('paid_by')->nullable();
            $table->string('recieved_by')->nullable();
            $table->string('status')->default('unpaid');
            $table->foreignId('earning_id')->nullable()->constrained('earnings');
            $table->foreignId('created_by_id')->constrained('users');
            $table->foreignId('updated_by_id')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_other_dues');
    }
};
