<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('credit_transaction_vnd', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->enum('type', ['deposit', 'withdraw', 'bet_debit', 'win_credit', 'adjustment']);
            $table->decimal('amount', 15, 2);
            $table->decimal('balance_before', 15, 2);
            $table->decimal('balance_after', 15, 2);
            $table->string('note')->nullable();
            $table->date('bet_date')->nullable();
            $table->integer('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('credit_transaction_usd', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->enum('type', ['deposit', 'withdraw', 'bet_debit', 'win_credit', 'adjustment']);
            $table->decimal('amount', 15, 2);
            $table->decimal('balance_before', 15, 2);
            $table->decimal('balance_after', 15, 2);
            $table->string('note')->nullable();
            $table->date('bet_date')->nullable();
            $table->integer('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('credit_transaction_vnd');
        Schema::dropIfExists('credit_transaction_usd');
    }
};
