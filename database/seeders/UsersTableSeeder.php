<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('adminpasscode'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Teacher One',
            'email' => 'teacher@example.com',
            'password' => Hash::make('teacherpass'),
            'role' => 'teacher',
        ]);

        User::create([
            'name' => 'Parent One',
            'email' => 'parent@example.com',
            'password' => Hash::make('parentpass'),
            'role' => 'parent',
        ]);
    }
}
