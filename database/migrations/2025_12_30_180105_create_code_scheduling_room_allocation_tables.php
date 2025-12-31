<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        // 1. The main Exam Schedule table
        Schema::create('exam_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_slot_id')->constrained('exam_slots')->onDelete('cascade');
            $table->foreignId('exam_date_id')->constrained('exam_dates')->onDelete('cascade');
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->foreignId('department_level_id')->constrained('department_level')->onDelete('cascade');
            $table->timestamps();
        });

        // 2. Room Allocations per Slot and Level
        Schema::create('exam_room_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_slot_id')->constrained('exam_slots')->onDelete('cascade');
            $table->foreignId('department_level_id')->constrained('department_level')->onDelete('cascade');
            $table->foreignId('room_id')->constrained('rooms')->onDelete('cascade');
            $table->integer('allocated_students'); // Capacity tracking
            $table->timestamps();
        });
        
        // 3. Holidays/Festivals to avoid conflicts
        Schema::create('festivals', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('festival_date');
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('festivals');
        Schema::dropIfExists('exam_room_allocations');
        Schema::dropIfExists('exam_schedules');
    }
};