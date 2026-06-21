<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teacher_payment_transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('teacher_payment_transactions', 'cash_book_id')) {
                $table->unsignedBigInteger('cash_book_id')->nullable()->after('payment_method');

                $table->foreign('cash_book_id', 'fk_tpt_cash_book')
                    ->references('id')->on('cash_books')
                    ->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('teacher_payment_transactions', function (Blueprint $table) {
            $table->dropForeign('fk_tpt_cash_book');
            $table->dropColumn('cash_book_id');
        });
    }
};
