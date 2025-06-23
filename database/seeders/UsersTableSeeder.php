<?php

namespace Database\Seeders;

use Hash;
use Illuminate\Database\Seeder;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        // Insert users
        $users = [
            [
                'name' => 'Member',
                'email' => 'member@gmail.com',
                'password' => Hash::make('member123'),
                'role' => 'member',
                'profile_photo' => 'images/profile-user/template_photo_profile.png'
            ],
            [
                'name'=> 'Admin 1',
                'email'=> 'admin1@gmail.com',
                'password'=> Hash::make('admin123'),
                'role'=> 'admin',
                'profile_photo' => 'images/profile-user/template_photo_profile.png'
            ],
            [
                'name'=> 'Super Admin',
                'email'=> 'super.admin@gmail.com',
                'password'=> Hash::make('superadmin123'),
                'role'=> 'super admin',
                'profile_photo' => 'images/profile-user/template_photo_profile.png'
            ]
        ];

        foreach ($users as $userData) {
            $user = User::create($userData);

            // Kalau role-nya 'member', buat juga relasi member kosong
            if ($user->role === 'member') {
                $user->member()->create();
            }
        }
    }
}
