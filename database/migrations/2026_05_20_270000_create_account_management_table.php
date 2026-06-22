<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('account_management')) {
            Schema::create('account_management', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('name_user')->nullable();
                $table->decimal('available_credit', 15, 2)->default(0);
                $table->decimal('bet_credit', 15, 2)->default(0);
                $table->decimal('cash_balance', 15, 2)->default(0);
                $table->string('currency', 10)->nullable();
                $table->string('created_by')->nullable();
                $table->string('updated_by')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('account_management');
    }
};
