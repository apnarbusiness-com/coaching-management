<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentAdmissionApplicationsTable extends Migration
{
    public function up()
    {
        Schema::create('student_admission_applications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('admission_date')->nullable();
            $table->string('admission_id_no')->nullable();
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('gender')->nullable();
            $table->date('dob')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('email')->nullable();

            $table->string('fathers_name')->nullable();
            $table->string('mothers_name')->nullable();
            $table->string('guardian_name')->nullable();
            $table->string('guardian_relation')->nullable();
            $table->string('guardian_contact_number')->nullable();
            $table->string('guardian_email')->nullable();
            $table->string('student_birth_no')->nullable();
            $table->string('student_blood_group')->nullable();
            $table->text('address')->nullable();

            $table->string('village')->nullable();
            $table->string('post_office')->nullable();
            $table->string('school_name')->nullable();
            $table->string('class_name')->nullable();
            $table->string('class_roll')->nullable();
            $table->string('batch_name')->nullable();
            $table->json('subjects')->nullable();
            $table->string('photo_path')->nullable();

            $table->string('status')->default('pending');
            $table->unsignedBigInteger('student_id')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->foreign('student_id', 'student_admission_student_fk')
                ->references('id')
                ->on('student_basic_infos')
                ->nullOnDelete();

            $table->foreign('approved_by', 'student_admission_approved_by_fk')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('student_admission_applications');
    }
}
