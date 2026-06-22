<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add bet_system + currency directly to users
        Schema::table('users', function (Blueprint $table) {
            $table->string('bet_system', 20)->nullable()->after('master_id'); // vietnam | khmer
            $table->string('currency', 10)->nullable()->after('bet_system');  // VND | USD
        });

        // 2. Migrate agents: copy from manager_bet_types
        if (Schema::hasTable('manager_bet_types')) {
            $agents = DB::table('users')
                ->join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                ->where('roles.name', 'agent')
                ->pluck('users.id');

            foreach ($agents as $agentId) {
                $bt = DB::table('manager_bet_types')->where('user_id', $agentId)->orderBy('id')->first();
                if ($bt) {
                    DB::table('users')->where('id', $agentId)->update([
                        'bet_system' => $bt->bet_system,
                        'currency'   => $bt->currency,
                    ]);
                }
            }
        }

        // 3. Migrate members: inherit from their agent (manager_id)
        $members = DB::table('users')
            ->join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('roles.name', 'member')
            ->select('users.id', 'users.manager_id')
            ->get();

        foreach ($members as $member) {
            if ($member->manager_id) {
                $agent = DB::table('users')->where('id', $member->manager_id)->first();
                if ($agent && $agent->bet_system) {
                    DB::table('users')->where('id', $member->id)->update([
                        'bet_system' => $agent->bet_system,
                        'currency'   => $agent->currency,
                    ]);
                    continue;
                }
            }

            // Fallback: use user_currencies table if no agent bet_system
            if (Schema::hasTable('user_currencies')) {
                $uc = DB::table('user_currencies')->where('user_id', $member->id)->first();
                if ($uc) {
                    $betSystem = ($uc->currency === 'KHR') ? 'khmer' : 'vietnam';
                    $currency  = ($uc->currency === 'KHR') ? 'VND' : $uc->currency;
                    DB::table('users')->where('id', $member->id)->update([
                        'bet_system' => $betSystem,
                        'currency'   => $currency,
                    ]);
                }
            }
        }

        // 4. Drop old tables
        Schema::dropIfExists('manager_bet_types');
        Schema::dropIfExists('user_currencies');
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['bet_system', 'currency']);
        });

        // Recreate user_currencies
        Schema::create('user_currencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('currency', 10)->nullable();
            $table->tinyInteger('record_status_id')->default(1);
            $table->timestamps();
        });

        // Recreate manager_bet_types
        Schema::create('manager_bet_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('bet_system', 20);
            $table->string('currency', 10);
            $table->timestamps();
        });
    }
};
