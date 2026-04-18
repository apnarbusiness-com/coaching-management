<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_books', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->decimal('amount', 15, 2)->default(0);
            $table->string('image')->nullable();
            $table->longText('note')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('cash_book_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cash_book_id')->constrained('cash_books')->onDelete('cascade');
            $table->decimal('old_amount', 15, 2)->nullable();
            $table->decimal('new_amount', 15, 2)->nullable();
            $table->string('action_type')->default('update');
            $table->longText('note')->nullable();
            $table->foreignId('created_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_book_transactions');
        Schema::dropIfExists('cash_books');
    }
};