<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Branch;
use App\Models\Pc;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $kdmBranches = [
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
            ['name' => 'San Bartolome', 'address' => 'San Bartolome, Novaliches', 'total_pcs' => 54], // Fixed 'talipes' typo
            ['name' => 'SJDM', 'address' => 'San Jose del Monte, Bulacan', 'total_pcs' => 46],
            ['name' => 'Taft', 'address' => 'Taft Avenue, Manila', 'total_pcs' => 78],
            ['name' => 'Tagaytay', 'address' => 'Tagaytay City, Cavite', 'total_pcs' => 44],
            ['name' => 'Tandang Sora', 'address' => 'Tandang Sora, Quezon City', 'total_pcs' => 53],
            ['name' => 'Imus', 'address' => 'Imus City, Cavite', 'total_pcs' => 60],
        ];

        foreach ($kdmBranches as $data) {
            $branch = Branch::updateOrCreate(
                ['name' => $data['name']],
                ['address' => $data['address'], 'total_pcs' => $data['total_pcs']]
            );

            // Generate the live PCs in the database if they don't exist
            if ($branch->pcs()->count() === 0) {
                for ($i = 1; $i <= $data['total_pcs']; $i++) {
                    Pc::create([
                        'branch_id' => $branch->id,
                        'pc_number' => 'PC-' . str_pad($i, 2, '0', STR_PAD_LEFT),
                        'status' => 'free' // free, occupied, broken, reserved
                    ]);
                }
            }
        }

        // Run your Manager Seeder here too
        $this->call([ManagerSeeder::class]);
    }
}