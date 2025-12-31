<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExamSlot;
use App\Models\ExamDate;
use App\Models\SlotMember;
use App\Models\DepartmentLevel;
use App\Models\Festival;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ExamSlotController extends Controller
{
    
    public function create()
    {
        // 1. Get Assigned IDs to filter sidebar
        $assignedLevelIds = DB::table('slot_members')->pluck('department_level_id')->toArray();

        // 2. Get combinations + Course Count (Filtered by Level)
        $combinations = DB::table('department_level')
            ->join('departments', 'department_level.department_id', '=', 'departments.id')
            ->leftJoin('course_department', 'departments.id', '=', 'course_department.department_id')
            ->leftJoin('courses', function ($join) {
                $join->on('course_department.course_id', '=', 'courses.id')
                     ->on('courses.level', '=', 'department_level.level');
            })
            ->whereNotIn('department_level.id', count($assignedLevelIds) ? $assignedLevelIds : [0])
            ->select(
                'department_level.id',
                'departments.department_code',
                'departments.department_name',
                'department_level.level',
                DB::raw('COUNT(DISTINCT courses.id) as courses_count')
            )
            ->groupBy('department_level.id', 'departments.department_code', 'departments.department_name', 'department_level.level')
            ->get();

        $festivals  = Festival::orderBy('festival_date')->get();
        $liveGroups = ExamSlot::with(['examDates', 'members.department.courses'])->latest()->get();

        return view('ExamScheduling.createSlots', compact('combinations', 'festivals', 'liveGroups'));
    }

 public function store(Request $request)
{
    // 1. Standard Validation
    $request->validate([
        'slot_name'    => 'required|string',
        'start_time'   => 'required',
        'end_time'     => 'required|after:start_time',
        'exam_dates'   => 'required|array|min:1',
        'combinations' => 'required|array|min:1',
    ]);

    // 2. Custom Validation: Days vs Max Courses
    // Ensures no level has more exams than available days
    $maxCourses = $this->getMaxCourseCount($request->combinations);
    $totalDays  = count($request->exam_dates);

    if ($totalDays < $maxCourses) {
        return redirect()->back()->withInput()->withErrors([
            'exam_dates' => "Insufficient days! Max courses for a level is $maxCourses, but only $totalDays days provided."
        ]);
    }

    // 3. Database Transaction to ensure data integrity
    DB::transaction(function () use ($request) {
        // Create the main Exam Slot
        $slot = ExamSlot::create([
            'slot_name'  => $request->slot_name,
            'start_time' => $request->start_time,
            'end_time'   => $request->end_time,
        ]);

        // Create the associated Exam Dates (One-to-Many)
        foreach ($request->exam_dates as $date) {
            $slot->examDates()->create([
                'actual_date' => $date
            ]);
        }

        // Link the Department Levels (Many-to-Many)
        // We extract the IDs and use attach() to fill the 'slot_members' pivot table
        $levelIds = collect($request->combinations)->pluck('department_level_id')->toArray();
        
        // attach() simply adds the new links to the pivot table
        $slot->members()->attach($levelIds);
    });

    return redirect()->back()->with('success', 'Exam Slot successfully generated!');
}
   public function update(Request $request, ExamSlot $examSlot)
{
    $request->validate([
        'slot_name'    => 'required|string',
        'start_time'   => 'required',
        'end_time'     => 'required|after:start_time',
        'exam_dates'   => 'required|array|min:1',
        'combinations' => 'required|array|min:1',
    ]);

    // 1. Validation Logic: Ensure we have enough days for the level with most courses
    $maxCourses = $this->getMaxCourseCount($request->combinations);
    $totalDays  = count($request->exam_dates);

    if ($totalDays < $maxCourses) {
        return redirect()->back()->withInput()->withErrors([
            'exam_dates' => "Insufficient days! Max courses for a level is $maxCourses, but only $totalDays days provided."
        ]);
    }

    DB::transaction(function () use ($request, $examSlot) {
        // 2. Update the main Slot details
        $examSlot->update([
            'slot_name'  => $request->slot_name,
            'start_time' => $request->start_time,
            'end_time'   => $request->end_time,
        ]);

        // 3. Update Exam Dates (HasMany Relationship)
        // We delete old dates and create new ones.
        $examSlot->examDates()->delete();
        foreach ($request->exam_dates as $date) {
            $examSlot->examDates()->create(['actual_date' => $date]);
        }

        // 4. Update Members/Department Levels (Many-to-Many Relationship)
        // Extract the IDs from the incoming combinations array
        $levelIds = collect($request->combinations)->pluck('department_level_id')->toArray();

        // Use sync() - This is the "Good DB" way.
        // It automatically deletes old pivot entries and adds the new ones.
        $examSlot->members()->sync($levelIds);
    });

    return redirect()->route('examSlots.create')->with('success', 'Slot updated successfully!');
}

    public function destroy(ExamSlot $examSlot)
    {
        $examSlot->delete();
        return redirect()->back()->with('success', 'Slot box removed.');
    }

    /**
     * Helper to find the bottleneck (Max courses in any selected level)
     */
    private function getMaxCourseCount($combinations)
   {
    $ids = collect($combinations)->pluck('department_level_id');
    
    return DepartmentLevel::whereIn('id', $ids)
        ->with('department.courses')
        ->get()
        ->map(function ($dl) {
            // Count courses matching this specific level
            return $dl->department->courses->where('level', $dl->level)->count();
        })
        ->max();
    }
}