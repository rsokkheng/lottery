<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Rename account_kh to account_kh_vnd
        Schema::rename('account_kh', 'account_kh_vnd');

        // Create account_kh_usd
        Schema::create('account_kh_usd', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->decimal('credit_balance', 15, 2)->default(0);
            $table->integer('record_status_id')->default(1);
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();

            $table->unique('user_id');
        });

        // Rename credit_transaction_kh to credit_transaction_kh_vnd
        Schema::rename('credit_transaction_kh', 'credit_transaction_kh_vnd');

        // Create credit_transaction_kh_usd
        Schema::create('credit_transaction_kh_usd', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('type'); // deposit, withdraw, bet_debit, win_credit, adjustment
            $table->decimal('amount', 15, 2);
            $table->decimal('balance_before', 15, 2)->default(0);
            $table->decimal('balance_after', 15, 2)->default(0);
            $table->string('note')->nullable();
            $table->date('bet_date')->nullable();
            $table->integer('created_by')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['type', 'bet_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_transaction_kh_usd');
        Schema::rename('credit_transaction_kh_vnd', 'credit_transaction_kh');
        Schema::dropIfExists('account_kh_usd');
        Schema::rename('account_kh_vnd', 'account_kh');
    }
};
