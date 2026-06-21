<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('earnings')
            ->whereNull('earning_transaction_id')
            ->whereNull('deleted_at')
            ->orderBy('id')
            ->chunk(100, function ($earnings) {
                foreach ($earnings as $earning) {
                    $txId = DB::table('earning_transactions')->insertGetId([
                        'receipt_no' => $earning->earning_reference ?? ('REC-' . date('Y', strtotime($earning->earning_date ?? now())) . '-' . str_pad($earning->id, 3, '0', STR_PAD_LEFT)),
                        'student_id' => $earning->student_id,
                        'total_amount' => $earning->amount,
                        'payment_method' => $earning->payment_method,
                        'cash_book_id' => $earning->cash_book_id,
                        'payment_date' => $earning->earning_date ? date('Y-m-d', strtotime($earning->earning_date)) : now()->format('Y-m-d'),
                        'total_items' => 1,
                        'created_by_id' => $earning->created_by_id,
                        'created_at' => $earning->created_at ?? now(),
                        'updated_at' => $earning->updated_at ?? now(),
                    ]);

                    DB::table('earnings')
                        ->where('id', $earning->id)
                        ->update(['earning_transaction_id' => $txId]);
                }
            });
    }

    public function down(): void
    {
        DB::table('earnings')->whereNotNull('earning_transaction_id')->update(['earning_transaction_id' => null]);
        DB::table('earning_transactions')->truncate();
    }
};
