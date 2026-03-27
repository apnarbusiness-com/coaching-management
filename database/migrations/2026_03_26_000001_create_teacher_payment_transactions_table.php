<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('teachers_payment_id');
            $table->decimal('amount', 15, 2);
            $table->date('payment_date');
            $table->string('payment_method');
            $table->string('reference')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('received_by')->nullable();
            $table->unsignedBigInteger('created_by_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('teachers_payment_id', 'tpt_payment_fk')
                ->references('id')
                ->on('teachers_payments')
                ->onDelete('cascade');

            $table->foreign('created_by_id', 'tpt_created_fk')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_payment_transactions');
    }
};
