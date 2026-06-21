<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            if (!Schema::hasColumn('expenses', 'teachers_payment_id')) {
                $table->unsignedBigInteger('teachers_payment_id')->nullable()->after('batch_id');
                $table->unsignedBigInteger('teacher_payment_transaction_id')->nullable()->after('teachers_payment_id');

                $table->foreign('teachers_payment_id', 'fk_exp_teachers_payment')
                    ->references('id')->on('teachers_payments')
                    ->onDelete('set null');

                $table->foreign('teacher_payment_transaction_id', 'fk_exp_teacher_payment_transaction')
                    ->references('id')->on('teacher_payment_transactions')
                    ->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign('fk_exp_teachers_payment');
            $table->dropForeign('fk_exp_teacher_payment_transaction');
            $table->dropColumn(['teachers_payment_id', 'teacher_payment_transaction_id']);
        });
    }
};
