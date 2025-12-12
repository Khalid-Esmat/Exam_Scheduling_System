<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void{

        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            /**
             * department_type:
             * 0 = عام
             * 1 = CS (علوم الحاسب)
             * 2 = IS (نظم المعلومات)
             */
            $table->unsignedTinyInteger('department_code');
            $table->timestamps();
        });

    }


    public function down(): void {

        Schema::dropIfExists('departments');
    
    }
};
