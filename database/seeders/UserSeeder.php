<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sample Users
        $users = [
            [
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'email' => 'admin@admin.com',
                'email_verified_at' => now(),
                'password' => '12345678',
                'role' => 'Super Admin',
            ],
            [
                'first_name' => 'Admin',
                'last_name' => 'User',
                'email' => 'admin@gmail.com',
                'email_verified_at' => now(),
                'password' => '12345678',
                'role' => 'Admin',
            ],
            [
                'first_name' => 'User',
                'last_name' => 'Test',
                'email' => 'user@user.com',
                'email_verified_at' => now(),
                'password' => '12345678',
                'role' => 'User',
            ],
        ];

        // Insert into database
        foreach ($users as $user) {
            $user = User::create([
                'first_name' => $user['first_name'],
                'last_name' => $user['last_name'],
                'email' => $user['email'],
                'email_verified_at' => $user['email_verified_at'],
                'password' => Hash::make($user['password']),
                'role' => $user['role'],
            ]);
        }

    }
}
