<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentLevelSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            // General Department (ID: 1) for Levels 1 & 2
            ['department_id' => 1, 'level' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['department_id' => 1, 'level' => 2, 'created_at' => now(), 'updated_at' => now()],
            
            // CS Department (ID: 2) for Levels 3 & 4
            ['department_id' => 2, 'level' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['department_id' => 2, 'level' => 4, 'created_at' => now(), 'updated_at' => now()],
            
            // IS Department (ID: 3) for Levels 3 & 4
            ['department_id' => 3, 'level' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['department_id' => 3, 'level' => 4, 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('department_level')->insert($data);
    }
}
