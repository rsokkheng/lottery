<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // users — manager lookups and currency joins
        Schema::table('users', function (Blueprint $table) {
            if (!$this->hasIndex('users', 'users_manager_id_index')) {
                $table->index('manager_id', 'users_manager_id_index');
            }
        });

        // user_currencies — heavily used in currency filtering
        if (Schema::hasTable('user_currencies')) {
            Schema::table('user_currencies', function (Blueprint $table) {
                if (!$this->hasIndex('user_currencies', 'user_currencies_user_id_currency_index')) {
                    $table->index(['user_id', 'currency'], 'user_currencies_user_id_currency_index');
                }
            });
        }

        // bets — date range and user filters are the most frequent queries
        if (Schema::hasTable('bets')) {
            Schema::table('bets', function (Blueprint $table) {
                if (!$this->hasIndex('bets', 'bets_user_id_bet_date_index')) {
                    $table->index(['user_id', 'bet_date'], 'bets_user_id_bet_date_index');
                }
                if (!$this->hasIndex('bets', 'bets_bet_receipt_id_index')) {
                    $table->index('bet_receipt_id', 'bets_bet_receipt_id_index');
                }
                if (!$this->hasIndex('bets', 'bets_bet_schedule_id_index')) {
                    $table->index('bet_schedule_id', 'bets_bet_schedule_id_index');
                }
                if (!$this->hasIndex('bets', 'bets_bet_package_config_id_index')) {
                    $table->index('bet_package_config_id', 'bets_bet_package_config_id_index');
                }
            });
        }

        // bet_receipts — date + user_id used in win amount aggregations
        if (Schema::hasTable('bet_receipts')) {
            Schema::table('bet_receipts', function (Blueprint $table) {
                if (!$this->hasIndex('bet_receipts', 'bet_receipts_user_id_date_index')) {
                    $table->index(['user_id', 'date'], 'bet_receipts_user_id_date_index');
                }
            });
        }

        // bet_receipt_usds
        if (Schema::hasTable('bet_receipt_usds')) {
            Schema::table('bet_receipt_usds', function (Blueprint $table) {
                if (!$this->hasIndex('bet_receipt_usds', 'bet_receipt_usds_user_id_date_index')) {
                    $table->index(['user_id', 'date'], 'bet_receipt_usds_user_id_date_index');
                }
            });
        }

        // bet_numbers — joined with bet_winning in every report subquery
        if (Schema::hasTable('bet_numbers')) {
            Schema::table('bet_numbers', function (Blueprint $table) {
                if (!$this->hasIndex('bet_numbers', 'bet_numbers_bet_id_index')) {
                    $table->index('bet_id', 'bet_numbers_bet_id_index');
                }
            });
        }

        // bet_winning — joined on bet_number_id in every report subquery
        if (Schema::hasTable('bet_winning')) {
            Schema::table('bet_winning', function (Blueprint $table) {
                if (!$this->hasIndex('bet_winning', 'bet_winning_bet_number_id_index')) {
                    $table->index('bet_number_id', 'bet_winning_bet_number_id_index');
                }
                if (!$this->hasIndex('bet_winning', 'bet_winning_bet_receipt_id_created_at_index')) {
                    $table->index(['bet_receipt_id', 'created_at'], 'bet_winning_bet_receipt_id_created_at_index');
                }
            });
        }

        // bet_winning_usd
        if (Schema::hasTable('bet_winning_usd')) {
            Schema::table('bet_winning_usd', function (Blueprint $table) {
                if (!$this->hasIndex('bet_winning_usd', 'bet_winning_usd_bet_number_id_index')) {
                    $table->index('bet_number_id', 'bet_winning_usd_bet_number_id_index');
                }
                if (!$this->hasIndex('bet_winning_usd', 'bet_winning_usd_receipt_id_created_at_index')) {
                    $table->index(['bet_receipt_id', 'created_at'], 'bet_winning_usd_receipt_id_created_at_index');
                }
            });
        }

        // account_management — filtered by user_id + created_at date in winning record logic
        if (Schema::hasTable('account_management')) {
            Schema::table('account_management', function (Blueprint $table) {
                if (!$this->hasIndex('account_management', 'account_management_user_id_created_at_index')) {
                    $table->index(['user_id', 'created_at'], 'account_management_user_id_created_at_index');
                }
            });
        }

        // balance_reports — updateOrInsert by user_id + report_date
        if (Schema::hasTable('balance_reports')) {
            Schema::table('balance_reports', function (Blueprint $table) {
                if (!$this->hasIndex('balance_reports', 'balance_reports_user_id_report_date_index')) {
                    $table->index(['user_id', 'report_date'], 'balance_reports_user_id_report_date_index');
                }
            });
        }

        // lottery_results — draw_date + lottery_schedule_id used in winning record queries
        if (Schema::hasTable('lottery_results')) {
            Schema::table('lottery_results', function (Blueprint $table) {
                if (!$this->hasIndex('lottery_results', 'lottery_results_draw_date_schedule_id_index')) {
                    $table->index(['draw_date', 'lottery_schedule_id'], 'lottery_results_draw_date_schedule_id_index');
                }
            });
        }
    }

    public function down(): void
    {
        $indexes = [
            'users'            => ['users_manager_id_index'],
            'user_currencies'  => ['user_currencies_user_id_currency_index'],
            'bets'             => ['bets_user_id_bet_date_index', 'bets_bet_receipt_id_index', 'bets_bet_schedule_id_index', 'bets_bet_package_config_id_index'],
            'bet_receipts'     => ['bet_receipts_user_id_date_index'],
            'bet_receipt_usds' => ['bet_receipt_usds_user_id_date_index'],
            'bet_numbers'      => ['bet_numbers_bet_id_index'],
            'bet_winning'      => ['bet_winning_bet_number_id_index', 'bet_winning_bet_receipt_id_created_at_index'],
            'bet_winning_usd'  => ['bet_winning_usd_bet_number_id_index', 'bet_winning_usd_receipt_id_created_at_index'],
            'account_management' => ['account_management_user_id_created_at_index'],
            'balance_reports'  => ['balance_reports_user_id_report_date_index'],
            'lottery_results'  => ['lottery_results_draw_date_schedule_id_index'],
        ];

        foreach ($indexes as $table => $names) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $t) use ($names) {
                    foreach ($names as $name) {
                        if ($this->hasIndex($t->getTable(), $name)) {
                            $t->dropIndex($name);
                        }
                    }
                });
            }
        }
    }

    private function hasIndex(string $table, string $indexName): bool
    {
        return collect(\Illuminate\Support\Facades\DB::select("SHOW INDEX FROM `{$table}`"))
            ->contains('Key_name', $indexName);
    }
};
