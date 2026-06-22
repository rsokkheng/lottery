<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bet_lottery_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->nullable();          // province/schedule code
            $table->string('draw_day', 20)->nullable();      // day of draw
            $table->time('draw_time')->nullable();           // draw time e.g. 16:30:00
            $table->time('time_close')->nullable();          // bet closing time
            $table->string('region_slug', 100)->nullable(); // mien-nam, mien-trung, mien-bac
            $table->string('province_en', 100)->nullable(); // province name in English
            $table->unsignedBigInteger('company_id')->nullable();
            $table->tinyInteger('is_active')->default(1);
            $table->timestamps();
        });

        Schema::create('bet_lottery_results', function (Blueprint $table) {
            $table->bigIncrements('result_id');
            $table->date('draw_date')->nullable();
            $table->string('province_code', 50)->nullable();
            $table->string('prize_level', 50)->nullable();
            $table->string('winning_number', 50)->nullable();
            $table->unsignedTinyInteger('result_order')->default(1);
            $table->unsignedBigInteger('lottery_schedule_id')->nullable();
            $table->foreign('lottery_schedule_id')
                  ->references('id')->on('bet_lottery_schedules')
                  ->onDelete('set null');
            $table->timestamps();

            // Unique key used by upsert()
            $table->unique(
                ['draw_date', 'province_code', 'prize_level', 'result_order', 'lottery_schedule_id'],
                'bet_results_upsert_unique'
            );
            $table->index(['draw_date', 'lottery_schedule_id'], 'bet_results_date_schedule_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bet_lottery_results');
        Schema::dropIfExists('bet_lottery_schedules');
    }
};
