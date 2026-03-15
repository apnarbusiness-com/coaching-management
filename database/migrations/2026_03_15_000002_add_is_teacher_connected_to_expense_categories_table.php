<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsTeacherConnectedToExpenseCategoriesTable extends Migration
{
    public function up()
    {
        Schema::table('expense_categories', function (Blueprint $table) {
            $table->boolean('is_teacher_connected')->default(false);
        });
    }
}
