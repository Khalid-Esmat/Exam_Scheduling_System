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

         // Add this missing relationship
    public function departmentLevels()
    {
        return $this->hasMany(DepartmentLevel::class);
    }
        // Get schedule for a specific level (e.g., Level 3)
    public function getScheduleForLevel($levelNumber)
    {
        // 1. Find the specific DepartmentLevel record
        $deptLevel = $this->departmentLevels()
            ->where('level', $levelNumber)
            ->with([
                'examSchedules.course',
                'examSchedules.examDate',
                'examSchedules.examSlot',
                'examRoomAllocations.room'
            ])
            ->first();

        if (!$deptLevel) {
            return "Level $levelNumber not found in " . $this->department_name;
        }

        // 2. Map Room Allocations by Slot ID for easy lookup
        // Key: Slot ID, Value: List of Allocations
        $allocationsBySlot = $deptLevel->examRoomAllocations->groupBy('exam_slot_id');

        // 3. Process the schedules
        $results = $deptLevel->examSchedules->map(function ($schedule) use ($deptLevel, $allocationsBySlot) {
            
            // Find rooms for this specific schedule's slot
            $slotId = $schedule->exam_slot_id;
            $rooms = [];

            if (isset($allocationsBySlot[$slotId])) {
                foreach ($allocationsBySlot[$slotId] as $allocation) {
                    if ($allocation->room) {
                        $rooms[] = $allocation->room->room_name;
                    }
                }
            }

            return [
                'department'  => $this->department_name,
                'level'       => $deptLevel->level,
                'course_id'   => $schedule->course->id ?? null,
                'course_code' => $schedule->course->course_code ?? 'N/A',
                'course_name' => $schedule->course->course_name ?? 'N/A',
                'date'        => $schedule->examDate->actual_date ?? 'N/A',
                'start_time'  => $schedule->examSlot->start_time ?? 'N/A',
                'end_time'    => $schedule->examSlot->end_time ?? 'N/A',
                'rooms'       => empty($rooms) ? 'Unassigned' : implode(', ', $rooms),
            ];
        });

        return $results;
    }
}
