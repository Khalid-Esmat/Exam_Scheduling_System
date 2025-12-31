<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invigilator;
use App\Models\InvigilationAssignment;
use Illuminate\Support\Facades\DB;

class InvigilationController extends Controller
{
    // ==========================================
    // 1. View Assignments (Global View)
    // ==========================================
    public function indexGlobal()
    {
        // 1. Fetch Raw Data
        $rawAllocations = DB::table('exam_room_allocations')
            ->join('rooms', 'exam_room_allocations.room_id', '=', 'rooms.id')
            ->join('exam_schedules', function($join) {
                // Join strictly on Slot and Department Level to find which courses belong to this allocation
                $join->on('exam_room_allocations.exam_slot_id', '=', 'exam_schedules.exam_slot_id')
                     ->on('exam_room_allocations.department_level_id', '=', 'exam_schedules.department_level_id');
            })
            ->join('exam_dates', 'exam_schedules.exam_date_id', '=', 'exam_dates.id')
            ->join('courses', 'exam_schedules.course_id', '=', 'courses.id')
            ->join('exam_slots', 'exam_room_allocations.exam_slot_id', '=', 'exam_slots.id')
            ->join('department_level', 'exam_room_allocations.department_level_id', '=', 'department_level.id')
            ->join('departments', 'department_level.department_id', '=', 'departments.id')
            ->select(
                'exam_room_allocations.id as allocation_id',
                'rooms.room_name',
                'courses.course_name',
                'courses.course_code',
                'exam_dates.actual_date',
                'exam_dates.id as exam_date_id',
                'exam_slots.slot_name',
                'departments.department_code',
                'departments.department_name',
                'department_level.level',
                'exam_room_allocations.allocated_students as student_count'
            )
            ->orderBy('exam_dates.actual_date')
            ->orderBy('exam_slots.slot_name')
            ->get();
// dd($rawAllocations);
        // 2. Process Data: Group by Date FIRST, then by Allocation ID
        $allocations = $rawAllocations
            ->groupBy('actual_date') // 1. Separate by Day (e.g., 2025-12-30)
            ->map(function ($dateGroup) {
                
                // 2. Inside that day, group by Room Allocation to merge courses happening at the same time
                return $dateGroup->groupBy('allocation_id')->map(function ($rows) {
                    $first = $rows->first();
                    
                    // Merge Course Names (e.g. "Math 1, Physics 1")
                    $courseNames = $rows->pluck('course_name')->unique()->implode(', ');
                    $courseCodes = $rows->pluck('course_code')->unique()->implode(', ');

                    return (object) [
                        'allocation_id' => $first->allocation_id,
                        'exam_date_id'  => $first->exam_date_id, // Critical for saving
                        'room_name'     => $first->room_name,
                        'course_name'   => $courseNames, 
                        'course_code'   => $courseCodes,
                        'actual_date'   => $first->actual_date,
                        'slot_name'     => $first->slot_name,
                        'department_info' => $first->department_name . ' - L' . $first->level,
                        'student_count' => $first->student_count,
                    ];
                });
            });

        // 3. Fetch Invigilators (with User data for names)
        $invigilators = Invigilator::with('user')->get();

        // 4. Fetch Existing Assignments [allocation_id => [exam_date_id => [invigilator_ids]]]
        // We need to group deeply because one allocation applies to multiple dates
        $currentAssignments = DB::table('invigilation_assignments')
            ->get();
            
        // Reshape for easy access in Blade: $assignments[allocation_id][exam_date_id] = [1, 2, 3]
        $formattedAssignments = [];
        foreach($currentAssignments as $assign) {
            $formattedAssignments[$assign->allocation_id][$assign->exam_date_id][] = $assign->invigilator_id;
        }
        $currentAssignments = collect($formattedAssignments);

        return view('ExamScheduling.invigilatorsAssignment', compact('allocations', 'invigilators', 'currentAssignments'));
    }

    // ==========================================
    // 2. Save Assignments
    // ==========================================
   public function saveGlobal(Request $request)
    {
        $request->validate([
            'assignments' => 'array', 
        ]);

        DB::transaction(function () use ($request) {
            // [FIX] Use 'present' array to loop, ensuring we process empty selections too
            $present = $request->input('present', []);
            $assignments = $request->input('assignments', []);

            foreach ($present as $allocationId => $dates) {
                foreach ($dates as $examDateId => $val) {
                    
                    // 1. ALWAYS Delete old assignments for this specific Room/Date
                    // (This now runs even if $assignments has no data for this row)
                    DB::table('invigilation_assignments')
                        ->where('allocation_id', $allocationId)
                        ->where('exam_date_id', $examDateId)
                        ->delete();

                    // 2. Check if new Invigilators were selected
                    if (isset($assignments[$allocationId][$examDateId])) {
                        $invigilatorIds = $assignments[$allocationId][$examDateId];
                        
                        if (!empty($invigilatorIds)) {
                            $insertData = [];
                            foreach ($invigilatorIds as $invigilatorId) {
                                if (!empty($invigilatorId)) {
                                    $insertData[] = [
                                        'allocation_id' => $allocationId,
                                        'exam_date_id' => $examDateId,
                                        'invigilator_id' => $invigilatorId,
                                        'created_at' => now(),
                                        'updated_at' => now(),
                                    ];
                                }
                            }
                            
                            if (count($insertData) > 0) {
                                DB::table('invigilation_assignments')->insert($insertData);
                            }
                        }
                    }
                }
            }
        });

        return redirect()->back()->with('success', 'Assignments updated successfully.');
    }
}