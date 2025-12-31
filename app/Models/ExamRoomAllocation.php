<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamRoomAllocation extends Model
{
    protected $fillable = ['exam_slot_id', 'department_level_id', 'room_id', 'allocated_students'];

    public function room()
    {
        return $this->belongsTo(Room::class); // Which physical room is used?
    }

    public function invigilations()
    {
        return $this->hasMany(InvigilationAssignment::class, 'allocation_id'); // Who is watching the exam?
    }
}