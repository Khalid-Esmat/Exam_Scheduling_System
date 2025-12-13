<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use function Laravel\Prompts\table;

return new class extends Migration{

  
    public function up(): void{

        Schema::create('invigilators', function (Blueprint $table) {
            $table->id();
            $table->string('phone',length:11);
            $table->string('job');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete()->unique();
            $table->timestamps();
        });
    
    }


    public function down(): void{

        Schema::dropIfExists('invigilators');

    }
};
