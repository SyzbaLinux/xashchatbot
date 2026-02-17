<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admins = [
            [
                'name'              => 'Super Admin',
                'email'             => 'admin@xash.co.zw',
                'phone_number'      => '+263771000001',
                'role'              => 'admin',
                'password'          => Hash::make('Admin@1234!'),
                'email_verified_at' => now(),
            ],
            [
                'name'              => 'Emmanuel',
                'email'             => 'emmanuel@xash.co.zw',
                'phone_number'      => '+263771000002',
                'role'              => 'admin',
                'password'          => Hash::make('Admin@1234!'),
                'email_verified_at' => now(),
            ],
        ];

        foreach ($admins as $admin) {
            User::updateOrCreate(
                ['email' => $admin['email']],
                $admin
            );
        }
    }
}
