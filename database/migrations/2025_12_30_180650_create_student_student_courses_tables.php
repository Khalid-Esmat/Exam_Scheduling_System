<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Student Profiles Table
        // We remove first/second/last name because they exist in the 'users' table.
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            // Link to the Auth User table (where the name and email are)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Academic specific data from the first DB
            $table->integer('level');
            $table->foreignId('department_id')->constrained('departments')->onDelete('cascade');
            $table->timestamps();
        });

        // 2. Student-Course Enrollment (Many-to-Many)
        // This is the core feature for conflict detection.
        Schema::create('student_course', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            
            // Ensure a student can't enroll in the same course twice
            $table->unique(['student_id', 'course_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_course');
        Schema::dropIfExists('students');
    }
};