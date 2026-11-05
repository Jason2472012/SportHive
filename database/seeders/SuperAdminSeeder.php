<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdmin = User::query()->firstOrNew([
            'email' => 'admin@example.com',
        ]);

        $superAdmin->forceFill([
            'name' => 'Super Admin',
            'role' => 'SUPER_ADMIN',
            'password_hash' => Hash::make('password'),
            'is_active' => true,
        ])->save();
    }
}
