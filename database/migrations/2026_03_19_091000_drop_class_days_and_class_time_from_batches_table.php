<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('batches', function (Blueprint $table) {
            $table->dropColumn(['class_days', 'class_time']);
        });
    }

    public function down(): void
    {
        Schema::table('batches', function (Blueprint $table) {
            $table->json('class_days')->nullable();
            $table->time('class_time')->nullable();
        });
    }
};
