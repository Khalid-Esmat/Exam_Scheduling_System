<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{

        protected $fillable = ['department_code'];

        public function getDepartmentNameAttribute()
        {
                $map = [
                        1=> 'عام',
                        2 => 'علوم الحاسب',
                        3 => 'نظم معلومات',
                        4 => 'ذكاء اصطناعي',
                        5=> 'تكنولوجيا معلومات',
                ];

                // حاول قراءة أكثر من اسم عمود محتمل 
                $key = $this->department ?? $this->department_code ?? $this->id ?? null;

                return $map[$key] ?? 'Unknown';
        }



        // علاقة Many-to-Many مع Course
        public function courses()
        {
                return $this->belongsToMany(Course::class, 'course_department');
        }



}
