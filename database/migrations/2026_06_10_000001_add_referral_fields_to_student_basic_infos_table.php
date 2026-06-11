<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_basic_infos', function (Blueprint $table) {
            $table->string('referral_code', 20)->nullable()->after('status');
            $table->unsignedBigInteger('referred_by_user_id')->nullable()->after('referral_code');
            $table->foreign('referred_by_user_id', 'fk_student_referred_by_user')
                ->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('student_basic_infos', function (Blueprint $table) {
            $table->dropForeign('fk_student_referred_by_user');
            $table->dropColumn(['referral_code', 'referred_by_user_id']);
        });
    }
};
