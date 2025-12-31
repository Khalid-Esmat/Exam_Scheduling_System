<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invigilation_assignments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('allocation_id')->constrained('exam_room_allocations')->onDelete('cascade');
    $table->foreignId('exam_date_id')->constrained('exam_dates')->onDelete('cascade');
    $table->foreignId('invigilator_id')->constrained('invigilators')->onDelete('cascade');
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invigilation_assignment');
    }
};
