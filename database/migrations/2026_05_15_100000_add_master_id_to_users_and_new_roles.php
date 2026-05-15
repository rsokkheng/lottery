<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('master_id')->nullable()->after('manager_id');
        });

        $guard = config('auth.defaults.guard', 'web');

        $roles = ['master', 'senior', 'share_master'];
        foreach ($roles as $name) {
            if (!DB::table('roles')->where('name', $name)->where('guard_name', $guard)->exists()) {
                DB::table('roles')->insert([
                    'name'       => $name,
                    'guard_name' => $guard,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('master_id');
        });

        DB::table('roles')->whereIn('name', ['master', 'senior', 'share_master'])->delete();
    }
};
