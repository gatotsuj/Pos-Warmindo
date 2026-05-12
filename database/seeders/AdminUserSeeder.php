<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'tenant_id' => 1,
            'name' => 'Administrator',
            'email' => 'admin@pos.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_ADMIN,
            'email_verified_at' => now(),
        ]);

        User::create([
            'tenant_id' => 1,
            'name' => 'Cashier 1',
            'email' => 'cashier@pos.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_CASHIER,
            'email_verified_at' => now(),
        ]);

        User::create([
            'tenant_id' => null,
            'name' => 'Super Admin',
            'email' => 'superadmin@pos.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_SUPERADMIN,
            'email_verified_at' => now(),
        ]);

        $this->command->info('Users created successfully!');
        $this->command->info('Admin: admin@pos.com / password');
        $this->command->info('Cashier: cashier@pos.com / password');
        $this->command->info('Superadmin: superadmin@pos.com / password');
    }
}
