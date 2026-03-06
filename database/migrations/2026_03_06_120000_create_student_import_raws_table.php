<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_import_raws', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('source_file');
            $table->string('sheet_name')->nullable();
            $table->unsignedInteger('row_index');
            $table->longText('row_data');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_import_raws');
    }
};

