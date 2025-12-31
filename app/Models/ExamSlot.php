<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamSlot extends Model
{
    protected $fillable = [
        'slot_name', 
        'start_time', 
        'end_time'
    ];
    public function examDates()
    {
        return $this->hasMany(ExamDate::class); // One slot has multiple available dates
    }

    public function schedules()
    {
        return $this->hasMany(ExamSchedule::class); // Links to actual scheduled exams
    }
    public function departmentLevels()
    {
          return $this->belongsToMany(DepartmentLevel::class, 'slot_members')
                       ->using(SlotMember::class)
                      ->withTimestamps(); //
    }
   
    public function members()
    {
       return $this->belongsToMany(DepartmentLevel::class, 'slot_members');
    }
}