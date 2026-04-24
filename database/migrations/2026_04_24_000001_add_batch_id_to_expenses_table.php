<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBatchIdToExpensesTable extends Migration
{
    public function up()
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->unsignedBigInteger('batch_id')->nullable()->after('teacher_id');
            $table->foreign('batch_id', 'expense_batch_fk')->references('id')->on('batches')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign('expense_batch_fk');
            $table->dropColumn('batch_id');
        });
    }
}