<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_import_raws', function (Blueprint $table) {
            $table->boolean('is_processed')->default(false)->after('row_data');
            $table->timestamp('processed_at')->nullable()->after('is_processed');
            $table->string('processed_status')->nullable()->after('processed_at');
            $table->text('processed_note')->nullable()->after('processed_status');
        });
    }

    public function down(): void
    {
        Schema::table('student_import_raws', function (Blueprint $table) {
            $table->dropColumn([
                'is_processed',
                'processed_at',
                'processed_status',
                'processed_note',
            ]);
        });
    }
};

