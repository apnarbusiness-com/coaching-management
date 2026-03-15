<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsStudentConnectedToEarningCategoriesTable extends Migration
{
    public function up()
    {
        Schema::table('earning_categories', function (Blueprint $table) {
            $table->boolean('is_student_connected')->default(false);
        });
    }
}
