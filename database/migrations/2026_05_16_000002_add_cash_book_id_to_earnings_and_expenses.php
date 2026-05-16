<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('earnings', function (Blueprint $table) {
            $table->foreignId('cash_book_id')->nullable()->constrained('cash_books')->nullOnDelete()->after('batch_id');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->foreignId('cash_book_id')->nullable()->constrained('cash_books')->nullOnDelete()->after('batch_id');
        });
    }

    public function down(): void
    {
        Schema::table('earnings', function (Blueprint $table) {
            $table->dropForeign(['cash_book_id']);
            $table->dropColumn('cash_book_id');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign(['cash_book_id']);
            $table->dropColumn('cash_book_id');
        });
    }
};
