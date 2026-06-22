<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('manager_bet_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('bet_system', ['vietnam', 'khmer']);
            $table->enum('currency', ['VND', 'USD']);
            $table->timestamps();
            $table->unique(['user_id', 'bet_system', 'currency'], 'mgr_bet_types_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manager_bet_types');
    }
};
