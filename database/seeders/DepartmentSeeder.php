<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder{
    public function run(): void
    {
        DB::table('departments')->insert([
            [
                'department_code' => 1, // عام
                'department_name' => 'General',
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'department_code' => 2, // CS
                'department_name' => 'CS',
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'department_code' => 3, // IS
                'department_name' => 'IS',
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'department_code' => 4, // AI
                'department_name' => 'AI',
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'department_code' => 5, // IT
                'department_name' => 'IT',
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
        ]);
    }
}
