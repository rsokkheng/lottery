<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bet_receipt_kh', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_no')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->date('date')->nullable();
            $table->string('currency')->default('VND');
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('commission', 15, 2)->default(0);
            $table->decimal('net_amount', 15, 2)->default(0);
            $table->decimal('compensate', 15, 2)->default(0);
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->tinyInteger('record_status_id')->default(1);
            $table->timestamps();
        });

        Schema::create('bet_kh', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->unsignedBigInteger('bet_receipt_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('bet_schedule_id')->nullable();
            $table->unsignedBigInteger('bet_package_config_id')->nullable();
            $table->string('number_format')->nullable();
            $table->string('digit_format')->nullable();
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->dateTime('bet_date')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->string('draw_time')->nullable();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->tinyInteger('record_status_id')->default(1);
            $table->timestamps();
        });

        Schema::create('bet_number_kh', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bet_id')->nullable();
            $table->string('original_number')->nullable();
            $table->string('generated_number')->nullable();
            $table->tinyInteger('digit_length')->nullable();
            $table->decimal('rate', 8, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('a_amount', 15, 2)->default(0);
            $table->decimal('b_amount', 15, 2)->default(0);
            $table->decimal('c_amount', 15, 2)->default(0);
            $table->decimal('d_amount', 15, 2)->default(0);
            $table->decimal('abcd_amount', 15, 2)->default(0);
            $table->decimal('roll_amount', 15, 2)->default(0);
            $table->decimal('roll2_amount', 15, 2)->default(0);
            $table->decimal('roll_parlay_amount', 15, 2)->default(0);
            $table->tinyInteger('a_check')->default(0);
            $table->tinyInteger('b_check')->default(0);
            $table->tinyInteger('c_check')->default(0);
            $table->tinyInteger('d_check')->default(0);
            $table->tinyInteger('abcd_check')->default(0);
            $table->tinyInteger('roll_check')->default(0);
            $table->tinyInteger('roll2_check')->default(0);
            $table->tinyInteger('roll_parlay_check')->default(0);
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->tinyInteger('record_status_id')->default(1);
            $table->timestamps();
        });

        Schema::create('bet_winning_kh', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bet_receipt_id')->nullable();
            $table->unsignedBigInteger('bet_id')->nullable();
            $table->unsignedBigInteger('bet_number_id')->nullable();
            $table->decimal('win_amount', 15, 2)->default(0);
            $table->tinyInteger('paid_status')->default(1);
            $table->dateTime('paid_at')->nullable();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();
        });

        Schema::create('bet_winning_record_kh', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bet_number_id')->nullable();
            $table->unsignedBigInteger('result_id')->nullable();
            $table->string('win_number')->nullable();
            $table->unsignedBigInteger('bet_winning_id')->nullable();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->tinyInteger('record_status_id')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bet_winning_record_kh');
        Schema::dropIfExists('bet_winning_kh');
        Schema::dropIfExists('bet_number_kh');
        Schema::dropIfExists('bet_kh');
        Schema::dropIfExists('bet_receipt_kh');
    }
};
