<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamDate extends Model
{
    // Define the columns that are allowed to be filled in bulk
    protected $fillable = [
        'exam_slot_id', 
        'actual_date'
    ];

    protected $casts = [
        'actual_date' => 'date', // This turns the string into a Carbon object
    ];


    /**
     * Relationship back to the ExamSlot
     */
    public function slot()
    {
        return $this->belongsTo(ExamSlot::class, 'exam_slot_id');
    }

    /**
     * Relationship to the specific schedules
     */
    public function schedules()
    {
        return $this->hasMany(ExamSchedule::class);
    }
}