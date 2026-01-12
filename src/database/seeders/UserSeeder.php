<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // 管理者
        User::create([
            'name' => '管理者ユーザー',
            'email' => 'admin@example.com',
            'password' => Hash::make('adminpassword'),
            'is_admin' => 1,
        ]);

        // 一般ユーザー（確認用）
        User::create([
            'name' => '西　怜奈',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'is_admin' => 0,
        ]);
    }
}
