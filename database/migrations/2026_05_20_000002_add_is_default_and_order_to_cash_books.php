<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cash_books', function (Blueprint $table) {
            $table->boolean('is_default')->default(false)->after('is_financial_account');
            $table->integer('order')->default(0)->after('is_default');
        });
    }

    public function down(): void
    {
        Schema::table('cash_books', function (Blueprint $table) {
            $table->dropColumn(['is_default', 'order']);
        });
    }
};
