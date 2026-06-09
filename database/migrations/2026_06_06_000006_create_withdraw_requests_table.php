<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWithdrawRequestsTable extends Migration
{
    public function up()
    {
        Schema::create('withdraw_requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('wallet_id');
            $table->unsignedBigInteger('user_id');
            $table->decimal('amount', 15, 2);
            $table->string('phone')->nullable();
            $table->string('payment_method')->default('bkash');
            $table->string('account_number')->nullable();
            $table->text('admin_notes')->nullable();
            $table->string('status')->default('pending');
            $table->unsignedBigInteger('processed_by')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->foreign('wallet_id')->references('id')->on('wallets')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('processed_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('withdraw_requests');
    }
}
