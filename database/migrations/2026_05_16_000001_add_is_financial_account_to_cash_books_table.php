<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cash_books', function (Blueprint $table) {
            $table->boolean('is_financial_account')->default(false)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('cash_books', function (Blueprint $table) {
            $table->dropColumn('is_financial_account');
        });
    }
};
