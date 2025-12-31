<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class StudentCourse extends Pivot
{
    // Explicitly define the table name since it's a pivot
    protected $table = 'student_course';

    protected $fillable = ['student_id', 'course_id'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}