<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('dashboard_widget_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->string('widget_key');
            $table->boolean('is_visible')->default(true);
            $table->timestamps();

            $table->unique(['role_id', 'widget_key']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('dashboard_widget_configs');
    }
};