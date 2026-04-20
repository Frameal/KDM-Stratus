<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Branch;
use App\Models\Pc;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. The Master Branch List
        $branches = [
            ['name' => 'Valenzuela', 'address' => 'Unit 2A H&L Santiago Building, Marulas (Main HQ)', 'total_pcs' => 68],
            ['name' => 'Anonas', 'address' => 'Anonas, Quezon City', 'total_pcs' => 65],
            ['name' => 'Antipolo', 'address' => 'Antipolo City, Rizal', 'total_pcs' => 45],
            ['name' => 'Bacoor', 'address' => 'Bacoor City, Cavite', 'total_pcs' => 75],
            ['name' => 'Baliwag', 'address' => 'Baliwag, Bulacan', 'total_pcs' => 52],
            ['name' => 'Batangas', 'address' => 'Batangas City', 'total_pcs' => 88],
            ['name' => 'Bicutan', 'address' => 'Bicutan, Taguig', 'total_pcs' => 60],
            ['name' => 'Cainta', 'address' => 'Cainta, Rizal', 'total_pcs' => 42],
            ['name' => 'Caloocan', 'address' => 'Caloocan City', 'total_pcs' => 95],
            ['name' => 'Comembo', 'address' => 'Comembo, Makati', 'total_pcs' => 40],
            ['name' => 'Dambana', 'address' => 'Dambana', 'total_pcs' => 48],
            ['name' => 'España', 'address' => 'España Blvd, Manila', 'total_pcs' => 100],
            ['name' => 'Evacom', 'address' => 'Evacom, Parañaque', 'total_pcs' => 55],
            ['name' => 'Evangelista', 'address' => 'Evangelista, Makati', 'total_pcs' => 62],
            ['name' => 'Gastambide', 'address' => 'Gastambide, Manila', 'total_pcs' => 70],
            ['name' => 'Guadalupe', 'address' => 'Guadalupe, Makati', 'total_pcs' => 58],
            ['name' => 'Las Pinas', 'address' => 'Las Piñas City', 'total_pcs' => 82],
            ['name' => 'Marikina', 'address' => 'Marikina City', 'total_pcs' => 92],
            ['name' => 'Morayta', 'address' => 'Morayta, Manila', 'total_pcs' => 85],
            ['name' => 'Muñoz', 'address' => 'Muñoz, Quezon City', 'total_pcs' => 50],
            ['name' => 'Novaliches', 'address' => 'Novaliches, Quezon City', 'total_pcs' => 68],
            ['name' => 'Pasig', 'address' => 'Pasig City', 'total_pcs' => 45],
            ['name' => 'San Bartolome', 'address' => 'San Bartolome, Novaliches', 'total_pcs' => 54],
            ['name' => 'SJDM', 'address' => 'San Jose del Monte, Bulacan', 'total_pcs' => 46],
            ['name' => 'Taft', 'address' => 'Taft Avenue, Manila', 'total_pcs' => 78],
            ['name' => 'Tagaytay', 'address' => 'Tagaytay City, Cavite', 'total_pcs' => 44],
            ['name' => 'Tandang Sora', 'address' => 'Tandang Sora, Quezon City', 'total_pcs' => 53],
            ['name' => 'Imus', 'address' => 'Imus City, Cavite', 'total_pcs' => 60],
        ];

        $pcsToInsert = [];

        // 2. Generate Branches and Queue PCs
        foreach ($branches as $branchData) {
            $branch = Branch::create([
                'name' => $branchData['name'],
                'address' => $branchData['address'],
            ]);

            for ($i = 1; $i <= $branchData['total_pcs']; $i++) {
                $status = 'free';
                $rand = rand(1, 100);
                if ($rand > 85) $status = 'occupied';
                elseif ($rand > 95) $status = 'broken';

                // We package them into an array instead of hitting the DB one by one
                $pcsToInsert[] = [
                    'branch_id' => $branch->id,
                    'pc_number' => 'PC-' . str_pad($i, 2, '0', STR_PAD_LEFT),
                    'status' => $status,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // 3. Bulk Insert all 1,761 PCs instantly
        Pc::insert($pcsToInsert);

        // 4. Create Executive Accounts
        User::create([
            'first_name' => 'Narpim',
            'last_name' => 'Executive',
            'username' => 'narpim_admin',
            'email' => 'narpim@kdm-stratus.com',
            'password' => Hash::make('admin123'),
            'role' => 'hq',
            'branch_id' => null,
            'contact_number' => '+639000000000',
            'dob' => '1990-01-01',
            'email_verified_at' => now(),
        ]);

        $imusBranch = Branch::where('name', 'Imus')->first();
        User::create([
            'first_name' => 'Imus',
            'last_name' => 'Manager',
            'username' => 'imus_admin',
            'email' => 'imus@kdm-stratus.com',
            'password' => Hash::make('branch123'),
            'role' => 'manager',
            'branch_id' => $imusBranch->id ?? null,
            'contact_number' => '+639111111111',
            'dob' => '1995-01-01',
            'email_verified_at' => now(),
        ]);
    }
}