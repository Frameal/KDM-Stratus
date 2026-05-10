<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Support\Facades\Hash;

class ManagerSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create the Executive Admin (Narpim)
        User::updateOrCreate(
            ['username' => 'narpim_admin'],
            [
                'first_name' => 'Executive',
                'last_name' => 'Director',
                'email' => 'hq@kdmstratus.local',
                'password' => Hash::make('narpim123'),
                'role' => 'hq',
                'branch_id' => null, // HQ oversees all branches
                'contact_number' => '09000000000',
                'email_verified_at' => now(),
            ]
        );

        // 2. Create the Branch Managers
        $branches = Branch::all();

        foreach ($branches as $branch) {
            // Converts "Imus Cavite" to "imus_cavite_admin"
            $username = strtolower(str_replace(' ', '_', $branch->name)) . '_admin';

            User::updateOrCreate(
                ['username' => $username],
                [
                    'first_name' => $branch->name,
                    'last_name' => 'Manager',
                    'email' => $username . '@kdmstratus.local',
                    'password' => Hash::make('branch123'),
                    'role' => 'manager',
                    'branch_id' => $branch->id,
                    'contact_number' => '09111111111', // Added the missing field!
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}