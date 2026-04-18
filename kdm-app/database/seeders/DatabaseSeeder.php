<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Branch;
use App\Models\Pc;
use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create the 28 Branches
        $branches = [
            ['name' => 'Valenzuela', 'address' => 'Unit 2A H&L Santiago Building, Marulas (Main HQ)', 'operating_hours' => '24/7 Operations', 'total_pcs' => 90],
            ['name' => 'Anonas', 'address' => 'Anonas, Quezon City', 'operating_hours' => '24/7 Operations', 'total_pcs' => 50],
            ['name' => 'Antipolo', 'address' => 'Antipolo City, Rizal', 'operating_hours' => '24/7 Operations', 'total_pcs' => 50],
            ['name' => 'Bacoor', 'address' => 'Bacoor City, Cavite', 'operating_hours' => '24/7 Operations', 'total_pcs' => 75],
            ['name' => 'Baliwag', 'address' => 'Baliwag, Bulacan', 'operating_hours' => '24/7 Operations', 'total_pcs' => 50],
            ['name' => 'Batangas', 'address' => 'Batangas City', 'operating_hours' => '24/7 Operations', 'total_pcs' => 50],
            ['name' => 'Bicutan', 'address' => 'Bicutan, Taguig', 'operating_hours' => '24/7 Operations', 'total_pcs' => 50],
            ['name' => 'Cainta', 'address' => 'Cainta, Rizal', 'operating_hours' => '24/7 Operations', 'total_pcs' => 50],
            ['name' => 'Caloocan', 'address' => 'Caloocan City', 'operating_hours' => '24/7 Operations', 'total_pcs' => 50],
            ['name' => 'Comembo', 'address' => 'Comembo, Makati', 'operating_hours' => '24/7 Operations', 'total_pcs' => 40],
            ['name' => 'Dambana', 'address' => 'Dambana', 'operating_hours' => '24/7 Operations', 'total_pcs' => 50],
            ['name' => 'España', 'address' => 'España Blvd, Manila', 'operating_hours' => '24/7 Operations', 'total_pcs' => 120],
            ['name' => 'Evacom', 'address' => 'Evacom, Parañaque', 'operating_hours' => '24/7 Operations', 'total_pcs' => 50],
            ['name' => 'Evangelista', 'address' => 'Evangelista, Makati', 'operating_hours' => '24/7 Operations', 'total_pcs' => 50],
            ['name' => 'Gastambide', 'address' => 'Gastambide, Manila', 'operating_hours' => '24/7 Operations', 'total_pcs' => 50],
            ['name' => 'Guadalupe', 'address' => 'Guadalupe, Makati', 'operating_hours' => '24/7 Operations', 'total_pcs' => 50],
            ['name' => 'Las Pinas', 'address' => 'Las Piñas City', 'operating_hours' => '24/7 Operations', 'total_pcs' => 50],
            ['name' => 'Marikina', 'address' => 'Marikina City', 'operating_hours' => '24/7 Operations', 'total_pcs' => 100],
            ['name' => 'Morayta', 'address' => 'Morayta, Manila', 'operating_hours' => '24/7 Operations', 'total_pcs' => 50],
            ['name' => 'Muñoz', 'address' => 'Muñoz, Quezon City', 'operating_hours' => '24/7 Operations', 'total_pcs' => 50],
            ['name' => 'Novaliches', 'address' => 'Novaliches, Quezon City', 'operating_hours' => '24/7 Operations', 'total_pcs' => 50],
            ['name' => 'Pasig', 'address' => 'Pasig City', 'operating_hours' => '24/7 Operations', 'total_pcs' => 45],
            ['name' => 'San Bartolome', 'address' => 'San Bartolome, Novaliches', 'operating_hours' => '24/7 Operations', 'total_pcs' => 50],
            ['name' => 'SJDM', 'address' => 'San Jose del Monte, Bulacan', 'operating_hours' => '24/7 Operations', 'total_pcs' => 50],
            ['name' => 'Taft', 'address' => 'Taft Avenue, Manila', 'operating_hours' => '24/7 Operations', 'total_pcs' => 50],
            ['name' => 'Tagaytay', 'address' => 'Tagaytay City, Cavite', 'operating_hours' => '24/7 Operations', 'total_pcs' => 50],
            ['name' => 'Tandang Sora', 'address' => 'Tandang Sora, Quezon City', 'operating_hours' => '24/7 Operations', 'total_pcs' => 50],
            ['name' => 'Imus', 'address' => 'Imus City, Cavite', 'operating_hours' => '24/7 Operations', 'total_pcs' => 60],
        ];

        foreach ($branches as $branch) {
            Branch::create($branch);
        }

        // 2. Generate 10 PCs for Valenzuela (Branch ID 1)
        for ($i = 1; $i <= 10; $i++) {
            Pc::create([
                'branch_id' => 1,
                'pc_number' => 'PC-' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'status' => $i % 4 == 0 ? 'occupied' : 'free',
            ]);
        }

        // 3. Generate 10 PCs for Imus (Branch ID 28)
        for ($i = 1; $i <= 10; $i++) {
            Pc::create([
                'branch_id' => 28,
                'pc_number' => 'PC-' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'status' => $i == 5 ? 'broken' : 'free',
            ]);
        }

        // 4. Create the Executive Admin (Plain-text password)
        Admin::create([
            'name' => 'Clark Karoll Landrito',
            'username' => 'narpim_admin',
            'password' => 'admin123', 
            'role' => 'hq',
            'branch_id' => 1,
        ]);

        // 5. Create a Test Branch Manager (Plain-text password)
        Admin::create([
            'name' => 'Imus Branch Manager',
            'username' => 'imus_admin',
            'password' => 'branch123', 
            'role' => 'branch',
            'branch_id' => 28,
        ]);
    }
}