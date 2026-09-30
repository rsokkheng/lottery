<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Receipts sent from a phone, waiting to be printed by the Mini POS station
        Schema::create('pos_print_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('receipt_no');
            $table->string('currency', 3);
            $table->boolean('is_reprint')->default(false);
            $table->string('status', 20)->default('pending'); // pending | printed
            $table->timestamp('printed_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_print_jobs');
    }
};
