<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\Course;
use Illuminate\Support\Facades\DB;

class StudentCourseSeeder extends Seeder
{
    public function run(): void
    {
        $students = Student::all();
        $courses = Course::all();

        if ($courses->isEmpty()) {
            $this->command->warn("No courses found. Please run CourseSeeder first!");
            return;
        }

        foreach ($students as $student) {
            // Pick a random number of courses for this student (e.g., 3 to 6)
            $randomCourses = $courses->random(rand(3, 6));

            foreach ($randomCourses as $course) {
                // Insert into the pivot table
                DB::table('student_course')->insert([
                    'student_id' => $student->id,
                    'course_id' => $course->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}