<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('balance_reports');
    }

    public function down(): void
    {
        // Intentionally not recreating — this table has been removed.
    }
};
