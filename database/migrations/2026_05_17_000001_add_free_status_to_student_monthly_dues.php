<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE student_monthly_dues MODIFY COLUMN status ENUM('paid', 'partial', 'unpaid', 'free') NOT NULL DEFAULT 'unpaid'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE student_monthly_dues MODIFY COLUMN status ENUM('paid', 'partial', 'unpaid') NOT NULL DEFAULT 'unpaid'");
    }
};
