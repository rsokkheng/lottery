<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $guard = 'web';

        // 1. Add agent role if not exists
        if (!DB::table('roles')->where('name', 'agent')->where('guard_name', $guard)->exists()) {
            DB::table('roles')->insert([
                'name'       => 'agent',
                'guard_name' => $guard,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $agentRole = DB::table('roles')->where('name', 'agent')->where('guard_name', $guard)->first();

        // 2. Migrate all users with senior / manager / share_master → agent
        $oldRoles = DB::table('roles')
            ->whereIn('name', ['senior', 'manager', 'share_master'])
            ->where('guard_name', $guard)
            ->pluck('id');

        if ($oldRoles->isNotEmpty()) {
            // Find all model_has_roles entries for old roles
            $affectedUserIds = DB::table('model_has_roles')
                ->whereIn('role_id', $oldRoles)
                ->where('model_type', 'App\\Models\\User')
                ->pluck('model_id')
                ->unique();

            // Remove old role assignments for these users
            DB::table('model_has_roles')
                ->whereIn('role_id', $oldRoles)
                ->where('model_type', 'App\\Models\\User')
                ->delete();

            // Assign agent role to those users (avoid duplicates)
            $existingAgentUsers = DB::table('model_has_roles')
                ->where('role_id', $agentRole->id)
                ->where('model_type', 'App\\Models\\User')
                ->pluck('model_id')
                ->toArray();

            $inserts = $affectedUserIds
                ->reject(fn($id) => in_array($id, $existingAgentUsers))
                ->map(fn($id) => [
                    'role_id'    => $agentRole->id,
                    'model_type' => 'App\\Models\\User',
                    'model_id'   => $id,
                ])
                ->values()
                ->all();

            if (!empty($inserts)) {
                DB::table('model_has_roles')->insert($inserts);
            }

            // 3. Remove old roles
            DB::table('roles')
                ->whereIn('name', ['senior', 'manager', 'share_master'])
                ->where('guard_name', $guard)
                ->delete();
        }
    }

    public function down(): void
    {
        $guard = 'web';

        foreach (['senior', 'manager', 'share_master'] as $name) {
            if (!DB::table('roles')->where('name', $name)->where('guard_name', $guard)->exists()) {
                DB::table('roles')->insert([
                    'name'       => $name,
                    'guard_name' => $guard,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        DB::table('roles')->where('name', 'agent')->where('guard_name', $guard)->delete();
    }
};
