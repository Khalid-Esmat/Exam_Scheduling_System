<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CourseDepartmentSeeder extends Seeder
{
    public function run(): void  {
        
        $data = [

            // ================================
            // Level 1 – قسم عام
            // ================================
            ['course_code' => 'MA111', 'department' => 0],
            ['course_code' => 'HU151', 'department' => 0],
            ['course_code' => 'HU111', 'department' => 0],
            ['course_code' => 'CS121', 'department' => 0],
            ['course_code' => 'EE101', 'department' => 0],
            ['course_code' => 'PH201', 'department' => 0],
            ['course_code' => 'HU113', 'department' => 0],

            // ================================
            // Level 2 – قسم عام
            // ================================
            ['course_code' => 'CS241', 'department' => 0],
            ['course_code' => 'MA222', 'department' => 0],
            ['course_code' => 'HU112', 'department' => 0],
            ['course_code' => 'HU231', 'department' => 0],
            ['course_code' => 'EE201', 'department' => 0],
            ['course_code' => 'MA231', 'department' => 0],
            ['course_code' => 'IS211', 'department' => 0],

            // ================================
            // Level 3 – CS ONLY
            // ================================
            ['course_code' => 'CS312', 'department' => 1],
            ['course_code' => 'CS322', 'department' => 1],
            ['course_code' => 'CS351', 'department' => 1],

            // ================================
            // Level 3 – IS ONLY
            // ================================
            ['course_code' => 'IS311', 'department' => 2],
            ['course_code' => 'IS312', 'department' => 2],
            ['course_code' => 'IS301', 'department' => 2],
            ['course_code' => 'IS331', 'department' => 2],

            // ================================
            // Level 3 – مشتركة CS + IS
            // ================================
            ['course_code' => 'CS321', 'department' => 1],
            ['course_code' => 'CS321', 'department' => 2],

            ['course_code' => 'CS311', 'department' => 1],
            ['course_code' => 'CS311', 'department' => 2],

            ['course_code' => 'CS391', 'department' => 1],
            ['course_code' => 'CS391', 'department' => 2],

            // ================================
            // Level 4 – CS ONLY
            // ================================
            ['course_code' => 'CS453', 'department' => 1],
            ['course_code' => 'CS431', 'department' => 1],
            ['course_code' => 'CS471', 'department' => 1],
            ['course_code' => 'CS401', 'department' => 1],

            // ================================
            // Level 4 – IS ONLY
            // ================================
            ['course_code' => 'IS412', 'department' => 2],
            ['course_code' => 'IS431', 'department' => 2],
            ['course_code' => 'IS447', 'department' => 2],
            ['course_code' => 'IS411', 'department' => 2],

            // ================================
            // Level 4 – مشتركة CS + IS
            // ================================
            ['course_code' => 'AI413', 'department' => 1],
            ['course_code' => 'AI413', 'department' => 2],
        ];

        foreach ($data as $item) {
            $courseId = DB::table('courses')
                ->where('course_code', $item['course_code'])
                ->value('id');

            $departmentId = DB::table('departments')
                ->where('department_code', $item['department'])
                ->value('id');

            if ($courseId && $departmentId) {
                DB::table('course_department')->insertOrIgnore([
                    'course_id' => $courseId,
                    'department_id' => $departmentId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
