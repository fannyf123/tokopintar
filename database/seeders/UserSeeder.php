<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Administrator',
                'username' => 'admin',
                'email' => 'admin@tokopintar.test',
                'password' => 'admin123',
                'role' => User::ROLE_ADMIN,
            ],
            [
                'name' => 'Kasir Demo',
                'username' => 'kasir',
                'email' => 'kasir@tokopintar.test',
                'password' => 'kasir123',
                'role' => User::ROLE_KASIR,
            ],
            [
                'name' => 'Gudang Demo',
                'username' => 'gudang',
                'email' => 'gudang@tokopintar.test',
                'password' => 'gudang123',
                'role' => User::ROLE_GUDANG,
            ],
        ];

        foreach ($users as $u) {
            User::updateOrCreate(
                ['username' => $u['username']],
                [
                    'name' => $u['name'],
                    'email' => $u['email'],
                    'password' => Hash::make($u['password']),
                    'role' => $u['role'],
                    'aktif' => true,
                ],
            );
        }
    }
}
