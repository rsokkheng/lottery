<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bet_number_kh', function (Blueprint $table) {
            if (!Schema::hasColumn('bet_number_kh', 'c_amount')) {
                $table->decimal('c_amount', 15, 2)->default(0)->after('b_amount');
            }
            if (!Schema::hasColumn('bet_number_kh', 'd_amount')) {
                $table->decimal('d_amount', 15, 2)->default(0)->after('c_amount');
            }
            if (!Schema::hasColumn('bet_number_kh', 'c_check')) {
                $table->tinyInteger('c_check')->default(0)->after('b_check');
            }
            if (!Schema::hasColumn('bet_number_kh', 'd_check')) {
                $table->tinyInteger('d_check')->default(0)->after('c_check');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bet_number_kh', function (Blueprint $table) {
            $table->dropColumn(['c_amount', 'd_amount', 'c_check', 'd_check']);
        });
    }
};
