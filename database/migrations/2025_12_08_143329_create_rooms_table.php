<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    

    public function up(): void{

        Schema::create('rooms', function (Blueprint $table) {

            $table->id();
            $table->string('room_name')->unique();
            $table->unsignedSmallInteger('capacity');
            // 1=available, 2=maintenance, 3=closed
            $table->unsignedTinyInteger('status')->default(1);
            $table->string('location');
            $table->boolean('is_available')->default(true);
            $table->timestamps();

        });

    }

  
    public function down(): void {

        Schema::dropIfExists('rooms');
    }

};
