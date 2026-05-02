<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'id'       => Str::uuid(),
            'username' => 'admin',
            'email'    => 'admin@email.com',
            'password' => Hash::make('admin123'),
            'role'     => 'admin',
        ]);
    }
}