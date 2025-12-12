<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model{

    protected $fillable = ['course_name', 'course_code', 'level', 'credit_hours','semester'];


    public function getLevelNameAttribute()
    {
        return [
            1 => ' الأولى',
            2 => ' الثانية',
            3 => ' الثالثة',
            4 => ' الرابعة',
        ][$this->level] ?? ' Unknown';
    }


    public function getSemesterNameAttribute()
    {
        return [
            1 => 'الأول',
            2 => 'الثاني',
        ][$this->semester] ?? ' Unknown';
    }

    public function getCreditHoursNameAttribute()
    {
        return [
            1 => 'ساعة ',
            2 => 'ساعتين',
            3 => '٣ ساعات',
        ][$this->credit_hours] ?? 'غير معروف';
    }
 
    
    // علاقة Many-to-Many مع departments
     public function departments()
    {
        return $this->belongsToMany(Department::class, 'course_department');
    }

    
}
