<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReferralCampaignsTable extends Migration
{
    public function up()
    {
        Schema::create('referral_campaigns', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->decimal('reward_amount', 15, 2)->default(0);
            $table->text('description')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('referral_campaigns');
    }
}
