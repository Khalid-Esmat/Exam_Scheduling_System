<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamSchedule extends Model
{
    protected $fillable = ['exam_slot_id', 'exam_date_id', 'course_id', 'department_level_id'];

    public function course()
    {
        return $this->belongsTo(Course::class); // Which subject is being tested?
    }

    public function examDate()
    {
        return $this->belongsTo(ExamDate::class); // Which day is the exam?
    }

    public function departmentLevel()
    {
        return $this->belongsTo(DepartmentLevel::class); // Which group of students?
    }
    public function examSlot()
    {
        return $this->belongsTo(ExamSlot::class);
    }
}