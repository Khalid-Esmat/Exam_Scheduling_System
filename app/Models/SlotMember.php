<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class SlotMember extends Pivot
{
    // Explicitly define the table name as per your migration
    protected $table = 'slot_members';

    protected $fillable = ['exam_slot_id', 'department_level_id'];

    public function slot()
    {
        return $this->belongsTo(ExamSlot::class, 'exam_slot_id');
    }

    public function departmentLevel()
    {
        return $this->belongsTo(DepartmentLevel::class, 'department_level_id');
    }
}