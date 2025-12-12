<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder{
    public function run(): void
    {
        DB::table('departments')->insert([
            [
                'department_code' => 0, // عام
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'department_code' => 1, // CS
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'department_code' => 2, // IS
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'department_code' => 3, // IT
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'department_code' => 4, // AI
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
        ]);
    }
}
