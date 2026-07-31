<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin — can manage users + activities
        User::updateOrCreate(
            ['email' => 'admin@npontu.local'],
            [
                'name' => 'Kwame Mensah',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'designation' => 'Systems Administrator',
                'phone' => '+233 20 000 0001',
            ]
        );

        // Lead — can create/edit activities + update status
        User::updateOrCreate(
            ['email' => 'lead@npontu.local'],
            [
                'name' => 'Abena Owusu',
                'password' => Hash::make('password'),
                'role' => 'lead',
                'designation' => 'Support Team Lead',
                'phone' => '+233 20 000 0002',
            ]
        );

        // Agent — can update status/remark only
        User::updateOrCreate(
            ['email' => 'agent@npontu.local'],
            [
                'name' => 'Kofi Asante',
                'password' => Hash::make('password'),
                'role' => 'agent',
                'designation' => 'Support Engineer',
                'phone' => '+233 20 000 0003',
            ]
        );

        if (User::count() <= 3) {
            User::factory()->agent()->count(3)->create();
        }
    }
}
