<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('student_basic_infos', function (Blueprint $table) {
            $table->decimal('monthly_discount', 10, 2)->default(0)->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_basic_infos', function (Blueprint $table) {
            $table->dropColumn('monthly_discount');
        });
    }
};
