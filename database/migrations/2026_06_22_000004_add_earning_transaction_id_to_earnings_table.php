<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('earnings', function (Blueprint $table) {
            if (!Schema::hasColumn('earnings', 'earning_transaction_id')) {
                $table->unsignedBigInteger('earning_transaction_id')->nullable()->after('id');

                $table->foreign('earning_transaction_id', 'fk_earnings_transaction')
                    ->references('id')->on('earning_transactions')
                    ->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('earnings', function (Blueprint $table) {
            $table->dropForeign('fk_earnings_transaction');
            $table->dropColumn('earning_transaction_id');
        });
    }
};
