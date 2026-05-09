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
        Schema::table('teachers', function (Blueprint $table) {
            $table->string('father_name')->nullable()->after('name');
            $table->string('mother_name')->nullable()->after('father_name');
            $table->date('dob')->nullable()->after('mother_name');
        });

        Schema::create('teacher_qualifications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('teacher_id');
            $table->string('university');
            $table->string('department');
            $table->string('session');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('teacher_id', 'tq_teacher_fk')->references('id')->on('teachers')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->dropColumn(['father_name', 'mother_name', 'dob']);
        });

        Schema::dropIfExists('teacher_qualifications');
    }
};
