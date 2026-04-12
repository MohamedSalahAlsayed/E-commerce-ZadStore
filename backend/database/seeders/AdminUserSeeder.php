<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@zadstore.com'],
            [
                'name' => 'Admin ZadStore',
                'password' => Hash::make('admin123456'),
                'phone' => '01000000000',
                'is_active' => true,
                'role' => 'admin',
            ]
        );
        
        $this->command->info('Admin account created/updated: admin@zadstore.com / admin123456');
    }
}
