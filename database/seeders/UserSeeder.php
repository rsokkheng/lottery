<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = \App\Models\User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name'             => 'Admin',
                'email'            => 'admin@lottery.com',
                'password'         => bcrypt('admin@123'),
                'phonenumber'      => '012000000',
                'record_status_id' => 1,
                'is_active'        => 1,
                'created_by'       => 1,
            ]
        );

        if (!$admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }
    }
}
