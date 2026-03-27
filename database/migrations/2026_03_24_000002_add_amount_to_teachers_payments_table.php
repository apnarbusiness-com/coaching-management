<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teachers_payments', function (Blueprint $table) {
            $table->decimal('amount', 15, 2)->nullable()->after('payment_details');
        });
    }

    public function down(): void
    {
        Schema::table('teachers_payments', function (Blueprint $table) {
            $table->dropColumn('amount');
        });
    }
};
