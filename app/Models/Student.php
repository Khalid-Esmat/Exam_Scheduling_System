<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = ['user_id', 'department_id', 'level', 'student_code']; // Add student_code to DB if needed

    // Link to User table (for Name/Email)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Link to Department
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    // Link to Courses (Many-to-Many)
    public function courses()
    {
          return $this->belongsToMany(Course::class, 'student_course')
                      ->using(StudentCourse::class)
                      ->withTimestamps(); //
    }



    // Main method to get the schedule
public function getMyExamSchedule()
    {
        $myCourses = $this->courses; 
        $scheduleDetails = [];

        foreach ($myCourses as $course) {
            // 1. Get the Schedule for the course
            // We use the Student's Department ID to pick the right batch (e.g. CS vs IS),
            // but we accept WHATEVER Level the exam is scheduled for.
            $schedules = ExamSchedule::where('course_id', $course->id)
                                ->with(['examDate', 'examSlot', 'departmentLevel']) 
                                ->get();

            // Try to match the student's department, otherwise take the first available schedule
            $examSchedule = $schedules->first(function($s) {
                 return $s->departmentLevel->department_id == $this->department_id;
            }) ?? $schedules->first();

            if ($examSchedule) {
                // 2. CRITICAL STEP: Use the Schedule's Department/Level, NOT the Student's
                // This ensures if a Level 4 student takes a Level 2 exam, 
                // we look for the Level 2 room.
                $targetDeptLevelId = $examSchedule->department_level_id;
                $targetSlotId = $examSchedule->exam_slot_id;

                $allocations = ExamRoomAllocation::where('exam_slot_id', $targetSlotId)
                                ->where('department_level_id', $targetDeptLevelId)
                                ->with('room')
                                ->get();

                $roomList = $allocations->map(function ($allocation) {
                    return $allocation->room ? $allocation->room->room_name : null;
                })->filter()->values()->toArray();

                $scheduleDetails[] = [
                    'course_code'  => $course->course_code,
                    'course_name'  => $course->course_name,
                    'date'         => $examSchedule->examDate->actual_date ?? 'N/A',
                    'time'         => ($examSchedule->examSlot->start_time ?? 'N/A') . ' - ' . ($examSchedule->examSlot->end_time ?? 'N/A'),
                    'rooms'        => empty($roomList) ? 'Unassigned' : implode(', ', $roomList),
                ];
            } else {
                $scheduleDetails[] = [
                    'course_code'  => $course->course_code,
                    'course_name'  => $course->course_name,
                    'date'         => 'Not Scheduled',
                    'time'         => '-',
                    'rooms'        => '-',
                ];
            }
        }

        return collect($scheduleDetails);
    }
}
