<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('teachers')
            ->where('salary_type', 'fixed')
            ->update(['salary_type' => 'monthly_fixed']);

        DB::table('teachers')
            ->where('salary_type', 'variable')
            ->update(['salary_type' => 'batch_wise']);
    }

    public function down(): void
    {
        DB::table('teachers')
            ->where('salary_type', 'monthly_fixed')
            ->update(['salary_type' => 'fixed']);

        DB::table('teachers')
            ->where('salary_type', 'batch_wise')
            ->update(['salary_type' => 'variable']);
    }
};
