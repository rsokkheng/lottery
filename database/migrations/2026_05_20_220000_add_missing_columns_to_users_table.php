<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'username')) {
                $table->string('username')->unique()->nullable()->after('name');
            }
            if (!Schema::hasColumn('users', 'package_id')) {
                $table->unsignedBigInteger('package_id')->nullable()->after('id');
            }
            if (!Schema::hasColumn('users', 'manager_id')) {
                $table->unsignedBigInteger('manager_id')->nullable()->after('package_id');
            }
            if (!Schema::hasColumn('users', 'record_status_id')) {
                $table->tinyInteger('record_status_id')->default(1)->after('avatar');
            }
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->tinyInteger('is_active')->default(1)->after('record_status_id');
            }
            if (!Schema::hasColumn('users', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('is_active');
            }
            if (!Schema::hasColumn('users', 'updated_by')) {
                $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(array_filter([
                Schema::hasColumn('users', 'username')         ? 'username'         : null,
                Schema::hasColumn('users', 'package_id')       ? 'package_id'       : null,
                Schema::hasColumn('users', 'manager_id')       ? 'manager_id'       : null,
                Schema::hasColumn('users', 'record_status_id') ? 'record_status_id' : null,
                Schema::hasColumn('users', 'is_active')        ? 'is_active'        : null,
                Schema::hasColumn('users', 'created_by')       ? 'created_by'       : null,
                Schema::hasColumn('users', 'updated_by')       ? 'updated_by'       : null,
            ]));
        });
    }
};
