<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add missing columns to existing bet_receipts table
        Schema::table('bet_receipts', function (Blueprint $table) {
            if (!Schema::hasColumn('bet_receipts', 'receipt_no')) {
                $table->string('receipt_no')->nullable()->after('id');
            }
            if (!Schema::hasColumn('bet_receipts', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('receipt_no');
            }
            if (!Schema::hasColumn('bet_receipts', 'date')) {
                $table->date('date')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('bet_receipts', 'currency')) {
                $table->string('currency')->default('VND')->after('date');
            }
            if (!Schema::hasColumn('bet_receipts', 'total_amount')) {
                $table->decimal('total_amount', 15, 2)->default(0)->after('currency');
            }
            if (!Schema::hasColumn('bet_receipts', 'commission')) {
                $table->decimal('commission', 15, 2)->default(0)->after('total_amount');
            }
            if (!Schema::hasColumn('bet_receipts', 'net_amount')) {
                $table->decimal('net_amount', 15, 2)->default(0)->after('commission');
            }
            if (!Schema::hasColumn('bet_receipts', 'compensate')) {
                $table->decimal('compensate', 15, 2)->default(0)->after('net_amount');
            }
            if (!Schema::hasColumn('bet_receipts', 'created_by')) {
                $table->string('created_by')->nullable()->after('compensate');
            }
            if (!Schema::hasColumn('bet_receipts', 'updated_by')) {
                $table->string('updated_by')->nullable()->after('created_by');
            }
            if (!Schema::hasColumn('bet_receipts', 'record_status_id')) {
                $table->tinyInteger('record_status_id')->default(1)->after('updated_by');
            }
        });

        // Create bets table (VND)
        if (!Schema::hasTable('bets')) {
            Schema::create('bets', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('bet_receipt_id')->nullable();
                $table->unsignedBigInteger('company_id')->nullable();
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
        }

        // Create bet_numbers table (VND — uses ab_amount/roll7 instead of KH's c/d/abcd/roll2)
        if (!Schema::hasTable('bet_numbers')) {
            Schema::create('bet_numbers', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('bet_id')->nullable();
                $table->string('original_number')->nullable();
                $table->string('generated_number')->nullable();
                $table->tinyInteger('digit_length')->nullable();
                $table->decimal('rate', 8, 2)->default(0);
                $table->decimal('total_amount', 15, 2)->default(0);
                $table->decimal('a_amount', 15, 2)->default(0);
                $table->decimal('b_amount', 15, 2)->default(0);
                $table->decimal('ab_amount', 15, 2)->default(0);
                $table->decimal('roll_amount', 15, 2)->default(0);
                $table->decimal('roll7_amount', 15, 2)->default(0);
                $table->decimal('roll_parlay_amount', 15, 2)->default(0);
                $table->tinyInteger('a_check')->default(0);
                $table->tinyInteger('b_check')->default(0);
                $table->tinyInteger('ab_check')->default(0);
                $table->tinyInteger('roll_check')->default(0);
                $table->tinyInteger('roll7_check')->default(0);
                $table->tinyInteger('roll_parlay_check')->default(0);
                $table->string('created_by')->nullable();
                $table->string('updated_by')->nullable();
                $table->tinyInteger('record_status_id')->default(1);
                $table->timestamps();
            });
        }

        // Create bet_winning table (VND)
        if (!Schema::hasTable('bet_winning')) {
            Schema::create('bet_winning', function (Blueprint $table) {
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
        }

        // Create bet_winning_records table (VND)
        if (!Schema::hasTable('bet_winning_records')) {
            Schema::create('bet_winning_records', function (Blueprint $table) {
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

        // Create bet_receipt_usd table (USD receipt)
        if (!Schema::hasTable('bet_receipt_usd')) {
            Schema::create('bet_receipt_usd', function (Blueprint $table) {
                $table->id();
                $table->string('receipt_no')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->date('date')->nullable();
                $table->string('currency')->default('USD');
                $table->decimal('total_amount', 15, 2)->default(0);
                $table->decimal('commission', 15, 2)->default(0);
                $table->decimal('net_amount', 15, 2)->default(0);
                $table->decimal('compensate', 15, 2)->default(0);
                $table->string('created_by')->nullable();
                $table->string('updated_by')->nullable();
                $table->tinyInteger('record_status_id')->default(1);
                $table->timestamps();
            });
        }

        // Create bet_usd table (USD bet)
        if (!Schema::hasTable('bet_usd')) {
            Schema::create('bet_usd', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('bet_receipt_id')->nullable();
                $table->unsignedBigInteger('company_id')->nullable();
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
        }

        // Create bet_number_usd table (USD — same VND-style columns)
        if (!Schema::hasTable('bet_number_usd')) {
            Schema::create('bet_number_usd', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('bet_id')->nullable();
                $table->string('original_number')->nullable();
                $table->string('generated_number')->nullable();
                $table->tinyInteger('digit_length')->nullable();
                $table->decimal('rate', 8, 2)->default(0);
                $table->decimal('total_amount', 15, 2)->default(0);
                $table->decimal('a_amount', 15, 2)->default(0);
                $table->decimal('b_amount', 15, 2)->default(0);
                $table->decimal('ab_amount', 15, 2)->default(0);
                $table->decimal('roll_amount', 15, 2)->default(0);
                $table->decimal('roll7_amount', 15, 2)->default(0);
                $table->decimal('roll_parlay_amount', 15, 2)->default(0);
                $table->tinyInteger('a_check')->default(0);
                $table->tinyInteger('b_check')->default(0);
                $table->tinyInteger('ab_check')->default(0);
                $table->tinyInteger('roll_check')->default(0);
                $table->tinyInteger('roll7_check')->default(0);
                $table->tinyInteger('roll_parlay_check')->default(0);
                $table->string('created_by')->nullable();
                $table->string('updated_by')->nullable();
                $table->tinyInteger('record_status_id')->default(1);
                $table->timestamps();
            });
        }

        // Create bet_winning_usd table
        if (!Schema::hasTable('bet_winning_usd')) {
            Schema::create('bet_winning_usd', function (Blueprint $table) {
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
        }

        // Create bet_winning_record_usd table
        if (!Schema::hasTable('bet_winning_record_usd')) {
            Schema::create('bet_winning_record_usd', function (Blueprint $table) {
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
    }

    public function down(): void
    {
        Schema::dropIfExists('bet_winning_record_usd');
        Schema::dropIfExists('bet_winning_usd');
        Schema::dropIfExists('bet_number_usd');
        Schema::dropIfExists('bet_usd');
        Schema::dropIfExists('bet_receipt_usd');
        Schema::dropIfExists('bet_winning_records');
        Schema::dropIfExists('bet_winning');
        Schema::dropIfExists('bet_numbers');
        Schema::dropIfExists('bets');
    }
};
