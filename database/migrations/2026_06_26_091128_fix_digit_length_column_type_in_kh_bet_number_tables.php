<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    // digit_length stores both numeric values ('2','3','4') and roll-parlay codes ('RP2','RP3')
    // tinyint cannot hold string values — change both KH tables to varchar(10)

    public function up(): void
    {
        DB::statement('ALTER TABLE bet_number_kh_usd MODIFY COLUMN digit_length VARCHAR(10) NULL');
        DB::statement('ALTER TABLE bet_number_kh_vnd MODIFY COLUMN digit_length VARCHAR(10) NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE bet_number_kh_usd MODIFY COLUMN digit_length TINYINT NULL');
        DB::statement('ALTER TABLE bet_number_kh_vnd MODIFY COLUMN digit_length TINYINT NULL');
    }
};
