<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepartmentLevel extends Model
{
    protected $table = 'department_level'; // Manual table name definition
    protected $fillable = ['department_id', 'level'];

    public function department()
    {
        return $this->belongsTo(Department::class); // Links to parent department
    }

    public function slots()
    {
        return $this->belongsToMany(ExamSlot::class, 'slot_members'); // Links levels to specific slots
    }
    public function examSlots()
    {
          return $this->belongsToMany(ExamSlot::class, 'slot_members')
                ->using(SlotMember::class)
                ->withTimestamps(); //
    }
    public function examSchedules()
    {
        return $this->hasMany(ExamSchedule::class, 'department_level_id');
    }

    public function examRoomAllocations()
    {
        return $this->hasMany(ExamRoomAllocation::class, 'department_level_id');
    }
}