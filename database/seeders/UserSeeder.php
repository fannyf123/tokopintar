<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Pemilik Toko',
                'email' => 'admin@tokopintar.test',
                'password' => Hash::make('123'),
            ],
        );
    }
}
