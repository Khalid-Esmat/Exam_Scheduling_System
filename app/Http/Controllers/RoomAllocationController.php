<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExamSlot;
use App\Models\Room;
use App\Models\ExamRoomAllocation;
use Illuminate\Support\Facades\DB;

class RoomAllocationController extends Controller
{
 public function show($slotId)
{
    // 1. Eager load 'members.department'
    $slot = ExamSlot::with(['members.department'])->findOrFail($slotId);
    
    // 2. Get available rooms
    $rooms = Room::where('is_available', true)->get();

    foreach($slot->members as $member) {
        
        // 3. Calculate TOTAL unique students for this Dept/Level
        // CORRECTED LOGIC: 
        // Find ALL students (regardless of their own level) who have registered 
        // for any course that belongs to this specific Department and Level.
        
        $member->student_count = DB::table('student_course')
            // Join courses to check the course's level
            ->join('courses', 'student_course.course_id', '=', 'courses.id')
            // Join course_department to check which department the course belongs to
            ->join('course_department', 'courses.id', '=', 'course_department.course_id')
            // Filter: Course Level matches the Batch Level
            ->where('courses.level', $member->level)
            // Filter: Course Department matches the Batch Department
            ->where('course_department.department_id', $member->department_id)
            // Count unique students (one seat per student, even if they have 2 exams in this batch)
            ->distinct('student_course.student_id')
            ->count('student_course.student_id');
        
        // 4. Get ALREADY allocated count for this specific slot and level
        $member->allocated_count = ExamRoomAllocation::where('exam_slot_id', $slotId)
            ->where('department_level_id', $member->id) 
            ->sum('allocated_students');
            
        // 5. Calculate remaining students to be distributed
        $member->remaining = max(0, $member->student_count - $member->allocated_count);
    }

    return view('ExamScheduling.roomsAllocation', compact('slot', 'rooms'));
}
    public function store(Request $request, $slotId)
    {
        $request->validate([
            'department_level_id' => 'required|exists:department_level,id',
            'allocations.*.room_id' => 'required|exists:rooms,id',
            'allocations.*.count' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request, $slotId) {
            foreach($request->allocations as $alloc) {
                $room = Room::findOrFail($alloc['room_id']);
                
                // Safety Check: Is the room already over-capacity for THIS slot?
                $currentRoomLoad = ExamRoomAllocation::where('exam_slot_id', $slotId)
                    ->where('room_id', $alloc['room_id'])
                    ->sum('allocated_students');

                if (($currentRoomLoad + $alloc['count']) > $room->capacity) {
                    throw new \Exception("سعة القاعة {$room->room_name} لا تكفي!");
                }

                ExamRoomAllocation::create([
                    'exam_slot_id' => $slotId,
                    'department_level_id' => $request->department_level_id,
                    'room_id' => $alloc['room_id'],
                    'allocated_students' => $alloc['count']
                ]);
            }
        });

        return back()->with('success', 'تم توزيع القاعات بنجاح');
    }

    public function updateRoomCapacity(Request $request, Room $room)
    {
        $request->validate(['capacity' => 'required|integer|min:1']);
    
        $room->update(['capacity' => $request->capacity]);

        return response()->json(['success' => true, 'new_capacity' => $room->capacity]);
    }
}