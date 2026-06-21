<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('earning_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_no');
            $table->unsignedBigInteger('student_id')->nullable();
            $table->decimal('total_amount', 15, 2);
            $table->string('payment_method')->nullable();
            $table->unsignedBigInteger('cash_book_id')->nullable();
            $table->date('payment_date');
            $table->integer('total_items')->default(0);
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('student_id')->references('id')->on('student_basic_infos')->onDelete('set null');
            $table->foreign('cash_book_id')->references('id')->on('cash_books')->onDelete('set null');
            $table->foreign('created_by_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('earning_transactions');
    }
};
