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
            ['name' => 'Administrator', 'username' => 'admin',  'email' => 'admin@fridaystore.com', 'role' => 'admin'],
            ['name' => 'Kasir Satu',    'username' => 'kasir',  'email' => 'kasir@fridaystore.com', 'role' => 'kasir'],
            ['name' => 'Owner Toko',    'username' => 'owner',  'email' => 'owner@fridaystore.com', 'role' => 'owner'],
        ];

        foreach ($users as $data) {
            $user = User::create([
                'name'      => $data['name'],
                'username'  => $data['username'],
                'email'     => $data['email'],
                'password'  => Hash::make('password'),
                'is_active' => true,
            ]);

            $user->assignRole($data['role']);
        }
    }
}
