<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvigilationAssignment extends Model
{
    protected $fillable = ['allocation_id', 'exam_date_id', 'invigilator_id'];

    public function allocation()
    {
        // Links to the room and student count
        return $this->belongsTo(ExamRoomAllocation::class, 'allocation_id');
    }

    public function examDate()
    {
        return $this->belongsTo(ExamDate::class);
    }

    public function invigilator()
    {
        return $this->belongsTo(Invigilator::class);
    }
}