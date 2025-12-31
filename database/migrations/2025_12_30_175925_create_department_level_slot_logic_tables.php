<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        // 1. Bridge for Department and Level
        Schema::create('department_level', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained('departments')->onDelete('cascade');
            $table->integer('level'); // e.g., 1, 2, 3, 4
            $table->timestamps();
        });

        // 2. Exam Slots (Groups like A or B)
        Schema::create('exam_slots', function (Blueprint $table) {
            $table->id();
            $table->string('slot_name'); // e.g., Group A
            $table->time('start_time');
            $table->time('end_time');
            $table->timestamps();
        });

        // 3. Exam Dates linked to Slots
        Schema::create('exam_dates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_slot_id')->constrained('exam_slots')->onDelete('cascade');
            $table->date('actual_date');
            $table->timestamps();
        });

        // 4. Assigning Department Levels to specific Slots
        Schema::create('slot_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_slot_id')->constrained('exam_slots')->onDelete('cascade');
            $table->foreignId('department_level_id')->constrained('department_level')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('slot_members');
        Schema::dropIfExists('exam_dates');
        Schema::dropIfExists('exam_slots');
        Schema::dropIfExists('department_level');
    }
};