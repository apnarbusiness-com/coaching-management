<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('earnings', function (Blueprint $table) {
            $table->index('earning_date', 'idx_earnings_earning_date');
            $table->index('student_id', 'idx_earnings_student_id');
            $table->index('earning_category_id', 'idx_earnings_category_id');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->index('expense_date', 'idx_expenses_expense_date');
            $table->index('expense_category_id', 'idx_expenses_category_id');
        });

        Schema::table('teachers_payments', function (Blueprint $table) {
            $table->index(['month', 'year', 'payment_status'], 'idx_teachers_payments_lookup');
            $table->index('teacher_id', 'idx_teachers_payments_teacher_id');
        });
    }

    public function down(): void
    {
        Schema::table('earnings', function (Blueprint $table) {
            $table->dropIndex('idx_earnings_earning_date');
            $table->dropIndex('idx_earnings_student_id');
            $table->dropIndex('idx_earnings_category_id');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropIndex('idx_expenses_expense_date');
            $table->dropIndex('idx_expenses_category_id');
        });

        Schema::table('teachers_payments', function (Blueprint $table) {
            $table->dropIndex('idx_teachers_payments_lookup');
            $table->dropIndex('idx_teachers_payments_teacher_id');
        });
    }
};
