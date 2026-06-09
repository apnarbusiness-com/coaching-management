<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReferralFieldsToAdmissionApplications extends Migration
{
    public function up()
    {
        Schema::table('student_admission_applications', function (Blueprint $table) {
            $table->string('referral_code', 20)->nullable()->after('notes');
            $table->unsignedBigInteger('referred_by_user_id')->nullable()->after('referral_code');
            $table->foreign('referred_by_user_id', 'fk_referred_by_user')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('student_admission_applications', function (Blueprint $table) {
            $table->dropForeign('fk_referred_by_user');
            $table->dropColumn(['referral_code', 'referred_by_user_id']);
        });
    }
}
