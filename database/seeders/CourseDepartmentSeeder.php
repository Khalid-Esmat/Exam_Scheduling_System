<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CourseDepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $data = [

            // ================================
            // Level 1 – قسم عام (0 ➜ 1)
            // ================================
            ['course_code' => 'MA111', 'department_id' => 1],
            ['course_code' => 'HU151', 'department_id' => 1],
            ['course_code' => 'HU111', 'department_id' => 1],
            ['course_code' => 'CS121', 'department_id' => 1],
            ['course_code' => 'EE101', 'department_id' => 1],
            ['course_code' => 'PH201', 'department_id' => 1],
            ['course_code' => 'HU113', 'department_id' => 1],

            // ================================
            // Level 2 – قسم عام (0 ➜ 1)
            // ================================
            ['course_code' => 'CS241', 'department_id' => 1],
            ['course_code' => 'MA222', 'department_id' => 1],
            ['course_code' => 'HU112', 'department_id' => 1],
            ['course_code' => 'HU231', 'department_id' => 1],
            ['course_code' => 'EE201', 'department_id' => 1],
            ['course_code' => 'MA231', 'department_id' => 1],
            ['course_code' => 'IS211', 'department_id' => 1],

            // ================================
            // Level 3 – CS ONLY (1 ➜ 2)
            // ================================
            ['course_code' => 'CS312', 'department_id' => 2],
            ['course_code' => 'CS322', 'department_id' => 2],
            ['course_code' => 'CS351', 'department_id' => 2],

            // ================================
            // Level 3 – IS ONLY (2 ➜ 3)
            // ================================
            ['course_code' => 'IS311', 'department_id' => 3],
            ['course_code' => 'IS312', 'department_id' => 3],
            ['course_code' => 'IS301', 'department_id' => 3],
            ['course_code' => 'IS331', 'department_id' => 3],

            // ================================
            // Level 3 – مشتركة CS + IS
            // ================================
            ['course_code' => 'CS321', 'department_id' => 2],
            ['course_code' => 'CS321', 'department_id' => 3],

            ['course_code' => 'CS311', 'department_id' => 2],
            ['course_code' => 'CS311', 'department_id' => 3],

            ['course_code' => 'CS391', 'department_id' => 2],
            ['course_code' => 'CS391', 'department_id' => 3],

            // ================================
            // Level 4 – CS ONLY (1 ➜ 2)
            // ================================
            ['course_code' => 'CS453', 'department_id' => 2],
            ['course_code' => 'CS431', 'department_id' => 2],
            ['course_code' => 'CS471', 'department_id' => 2],
            ['course_code' => 'CS401', 'department_id' => 2],

            // ================================
            // Level 4 – IS ONLY (2 ➜ 3)
            // ================================
            ['course_code' => 'IS412', 'department_id' => 3],
            ['course_code' => 'IS431', 'department_id' => 3],
            ['course_code' => 'IS447', 'department_id' => 3],
            ['course_code' => 'IS411', 'department_id' => 3],

            // ================================
            // Level 4 – مشتركة CS + IS
            // ================================
            ['course_code' => 'AI413', 'department_id' => 2],
            ['course_code' => 'AI413', 'department_id' => 3],
        ];

        foreach ($data as $item) {

            $courseId = DB::table('courses')
                ->where('course_code', $item['course_code'])
                ->value('id');

            if ($courseId) {
                DB::table('course_department')->insertOrIgnore([
                    'course_id'     => $courseId,
                    'department_id' => $item['department_id'],
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }
        }
    }
}
