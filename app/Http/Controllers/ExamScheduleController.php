<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExamSlot;
use App\Models\DepartmentLevel;
use Illuminate\Support\Facades\DB;

class ExamScheduleController extends Controller
{
  
    /**
     * Helper: The Core Conflict Logic
     */
    private function hasConflict($courseA, $courseB)
    {
        if ($courseA->id === $courseB->id) return true; 
        if ($courseA->dept_level_context === $courseB->dept_level_context) return true;
        return false;
    }

    /**
     * Helper: Build the Conflict Graph
     */
    private function buildConflictGraph($courses)
    {
        $graph = [];
        foreach ($courses as $course) {
            $graph[$course->composite_key] = [
                'course_obj' => $course,
                'degree' => 0,
                'conflicts' => []
            ];
        }

        $keys = array_keys($graph);
        $count = count($keys);

        for ($i = 0; $i < $count; $i++) {
            for ($j = $i + 1; $j < $count; $j++) {
                $keyA = $keys[$i];
                $keyB = $keys[$j];
                $courseA = $graph[$keyA]['course_obj'];
                $courseB = $graph[$keyB]['course_obj'];

                if ($this->hasConflict($courseA, $courseB)) {
                    $graph[$keyA]['conflicts'][] = $keyB;
                    $graph[$keyB]['conflicts'][] = $keyA;
                    $graph[$keyA]['degree']++;
                    $graph[$keyB]['degree']++;
                }
            }
        }
        return $graph;
    }

    // ==========================================
    // 1. View All Slots (Dashboard)
    // ==========================================
    public function indexAll()
    {
        // FIX: Removed 'departmentLevel'
        $slots = ExamSlot::with(['examDates', 'members.department.courses'])->latest()->get();

        foreach ($slots as $slot) {
            $count = 0;
            foreach ($slot->members as $member) {
                // $member IS the DepartmentLevel model
                if ($member->department) {
                    $count += $member->department->courses
                        ->where('level', $member->level) // Direct access
                        ->count();
                }
            }
            $slot->total_courses_count = $count;
            $slot->is_scheduled = DB::table('exam_schedules')->where('exam_slot_id', $slot->id)->exists();
        }

        return view('ExamScheduling.CreateExamSchedule.index', compact('slots'));
    }

    // ==========================================
    // 2. Manual Mode
    // ==========================================
    // ==========================================
    // 2. Manual Mode
    // ==========================================
    public function manualMode(Request $request, $slotId) // <--- Add Request $request
    {
        $slot = ExamSlot::with(['examDates', 'members.department.courses'])->findOrFail($slotId);
        $dates = $slot->examDates;

        // ... [Keep the Course Preparation & Graph Building logic exactly as before] ...
        // ... (This part builds $groupedCourses) ...
        // [Copy the graph building code from previous response if needed, 
        //  but the key change is at the bottom]

        $rawCourses = collect();
        foreach ($slot->members as $member) {
            $targetLevel = $member->level;
            $deptLevelId = $member->id;
            if (!$member->department) continue;
            $deptCourses = $member->department->courses
                ->filter(function($c) use ($targetLevel) {
                    return (int)$c->level === (int)$targetLevel;
                });
            foreach($deptCourses as $c) {
                $courseInstance = clone $c;
                $courseInstance->dept_level_context = $deptLevelId;
                $courseInstance->composite_key = $c->id . '_' . $deptLevelId;
                $rawCourses->push($courseInstance);
            }
        }
        $graph = $this->buildConflictGraph($rawCourses);
        $palette = ['#ef4444', '#f97316', '#f59e0b', '#84cc16', '#10b981', '#06b6d4', '#3b82f6', '#6366f1', '#8b5cf6', '#d946ef', '#f43f5e', '#881337'];
        $groupedCourses = [];

        foreach ($graph as $compositeKey => $node) {
            $courseObj = $node['course_obj'];
            $deptLevelId = $courseObj->dept_level_context;
            $identityColor = $palette[crc32($courseObj->course_code) % count($palette)];

            if (!isset($groupedCourses[$deptLevelId])) {
                $deptLevel = DepartmentLevel::with('department')->find($deptLevelId);
                $groupedCourses[$deptLevelId] = [
                    'title' => ($deptLevel->department->department_name ?? 'N/A') . ' - المستوى ' . $deptLevel->level,
                    'courses' => []
                ];
            }
            $groupedCourses[$deptLevelId]['courses'][$compositeKey] = [
                'id' => $courseObj->id,
                'composite_key' => $compositeKey, 
                'name' => $courseObj->course_name,
                'code' => $courseObj->course_code,
                'degree' => $node['degree'],
                'conflicts' => array_map(function($k) use ($graph) { return $graph[$k]['course_obj']->id; }, $node['conflicts']),
                'identity_color' => $identityColor,
                'conflict_colors' => array_map(function($k) use ($graph, $palette) {
                    $o = $graph[$k]['course_obj'];
                    return ['name' => $o->course_name, 'code' => $o->course_code, 'color' => $palette[crc32($o->course_code) % count($palette)]];
                }, $node['conflicts'])
            ];
        }

        // --- THE FIX IS HERE ---
        // Only fetch from DB if 'show=1' is in the URL (e.g. from Auto Generate)
        // Otherwise, send empty array so dropdowns stay empty.
        if ($request->has('show') || session('show_schedule')) {
            $savedSchedule = DB::table('exam_schedules')
                ->where('exam_slot_id', $slotId)
                ->pluck('exam_date_id', 'course_id')
                ->toArray();
        } else {
            $savedSchedule = []; // Start Empty
        }

        return view('ExamScheduling.CreateExamSchedule.manual', compact('slot', 'groupedCourses', 'dates', 'savedSchedule'));
    }

    // ==========================================
    // 3. Save Manual Schedule
    // ==========================================
    public function saveManual(Request $request, $slotId)
    {
        $request->validate(['schedule' => 'required|array']);

        DB::transaction(function () use ($request, $slotId) {
            DB::table('exam_schedules')->where('exam_slot_id', $slotId)->delete();

            foreach ($request->schedule as $deptLevelId => $courses) {
                foreach ($courses as $compositeKey => $dateId) {
                    if (empty($dateId)) continue;
                    
                    $parts = explode('_', $compositeKey);
                    $realCourseId = $parts[0];

                    DB::table('exam_schedules')->insert([
                        'exam_slot_id' => $slotId,
                        'course_id' => $realCourseId, 
                        'department_level_id' => $deptLevelId,
                        'exam_date_id' => $dateId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        });

        // At the end:
   return redirect()->route('schedule.manual', ['slot' => $slotId, 'show' => 1])
    ->with('success', 'تم حفظ الجدول اليدوي بنجاح');
    }

    // ==========================================
    // 4. Auto Generate
    // ==========================================
    public function autoGenerate($slotId)
    {
        // FIX: Removed 'departmentLevel'
        $slot = ExamSlot::with(['examDates', 'members.department.courses'])->findOrFail($slotId);
        $dates = $slot->examDates;

        if ($dates->isEmpty()) {
            return redirect()->back()->with('error', 'خطأ: لم يتم تحديد أيام امتحانات.');
        }

        $rawCourses = collect();
        foreach ($slot->members as $member) {
            // FIX: Access properties directly
            $targetLevel = $member->level;
            $deptLevelId = $member->id;

            if (!$member->department) continue;

            $deptCourses = $member->department->courses
                ->filter(function($c) use ($targetLevel) {
                    return (int)$c->level === (int)$targetLevel;
                });
            
            foreach($deptCourses as $c) {
                $courseInstance = clone $c;
                $courseInstance->dept_level_context = $deptLevelId;
                $courseInstance->composite_key = $c->id . '_' . $deptLevelId;
                $rawCourses->push($courseInstance);
            }
        }

        $graph = $this->buildConflictGraph($rawCourses);

        uasort($graph, function ($a, $b) {
            return $b['degree'] <=> $a['degree'];
        });

        $assignments = []; 
        $failedCourses = [];

        foreach ($graph as $compositeKey => $node) {
            $foundDate = null;
            foreach ($dates as $date) {
                $hasConflict = false;
                foreach ($node['conflicts'] as $neighborKey) {
                    if (isset($assignments[$neighborKey]) && $assignments[$neighborKey] == $date->id) {
                        $hasConflict = true;
                        break;
                    }
                }
                if (!$hasConflict) {
                    $foundDate = $date->id;
                    break;
                }
            }
            if ($foundDate) {
                $assignments[$compositeKey] = $foundDate;
            } else {
                $failedCourses[] = $node['course_obj']->course_name;
            }
        }

        DB::transaction(function () use ($assignments, $graph, $slotId) {
            DB::table('exam_schedules')->where('exam_slot_id', $slotId)->delete();
            foreach ($assignments as $compKey => $dateId) {
                $node = $graph[$compKey];
                DB::table('exam_schedules')->insert([
                    'exam_slot_id' => $slotId,
                    'course_id' => $node['course_obj']->id,
                    'department_level_id' => $node['course_obj']->dept_level_context,
                    'exam_date_id' => $dateId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });

        if (count($failedCourses) > 0) {
            return redirect()->route('schedule.manual', $slotId)
                ->with('warning', 'تم التوليد جزئياً، وتعذر جدولة: ' . implode(', ', $failedCourses));
        }

       // At the very end of the function:
    
      return redirect()->route('schedule.manual', ['slot' => $slotId, 'show' => 1]) 
    ->with('success', 'تم توليد الجدول تلقائياً بنجاح.');
    }
}






