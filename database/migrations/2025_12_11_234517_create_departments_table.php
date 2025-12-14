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
             * 1 = عام
             * 2 = CS (علوم الحاسب)
             * 3 = IS (نظم المعلومات)
             * 4 = AI (ذكاء اصطناعى)
             * 5 = IT (تكنولوجيا معلومات)
             * */
            $table->unsignedTinyInteger('department_code');
            $table->timestamps();
        });

    }


    public function down(): void {

        Schema::dropIfExists('departments');
    
    }
};
