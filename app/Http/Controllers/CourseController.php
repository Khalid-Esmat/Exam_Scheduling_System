<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CourseController extends Controller
{
    /* =======================
        عرض الكورسات
    ======================== */

    public function index(Request $request)
    {
        $search = $request->search;
        $message = null;

        $query = Course::with('departments');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('course_name', 'like', "%{$search}%")
                    ->orWhere('course_code', 'like', "%{$search}%");
            });
        }

        $courses = $query
            ->orderBy('level')
            ->orderBy('semester')
            ->get();

        if ($search && $courses->isEmpty()) {
            $courses = Course::with('departments')
                ->orderBy('level')
                ->orderBy('semester')
                ->get();

            $message = 'لا توجد مواد مطابقة لبحثك';
        }

        return view('dashboard.courses', compact('courses', 'message'));
    }


    /* =======================
        حذف كورس
    ======================== */
    public function destroy($id)
    {
        Course::findOrFail($id)->delete();
        return redirect()->route('courses.index')->with('success', 'تم حذف الكورس بنجاح');

    }


    /* =======================
    Validation Rules
    ======================== */
    private function rules($id = null)
    {
        return [
            'course_name' => 'required|string|max:255',
            'course_code' => 'required|string|size:5|unique:courses,course_code,' . $id,
            'credit_hours' => 'required|integer|min:1|max:6',
            'level' => 'required|integer|between:1,4',
            'semester' => 'required|integer|in:1,2',

            'department_ids' => 'array',
            'department_ids.*' => 'exists:departments,id',
        ];
    }
    /* =======================
        Validation Messages
    ======================== */
    private function messages()
    {
        return [
            'course_name.required' => 'اسم المقرر مطلوب',

            'course_code.required' => 'كود المقرر مطلوب',
            'course_code.size' => 'كود المقرر يجب أن يتكون من 5 أحرف',
            'course_code.unique' => 'كود المقرر مستخدم من قبل',

            'credit_hours.required' => 'عدد الساعات المعتمدة مطلوب',
            'credit_hours.integer' => 'عدد الساعات المعتمدة يجب أن يكون رقمًا صحيحًا',
            'credit_hours.min' => 'عدد الساعات المعتمدة لا يقل عن ساعة واحدة',

            'level.required' => 'الفرقة الدراسية مطلوبة',
            'semester.required' => 'الترم مطلوب',

            'department_ids.array' => 'الأقسام غير صحيحة',
            'department_ids.*.exists' => 'القسم الأكاديمي غير موجود',
        ];
    }



    /* =======================
        إضافة كورس جديد
    ======================== */
    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            $this->rules(),
            $this->messages()
        );

        if ($validator->fails()) {
            return back()
                ->withErrors($validator, 'addSubjectModal')
                ->withInput()
                ->with('open_modal', 'addSubjectModal');
        }

        DB::transaction(function () use ($request) {

            $course = Course::create([
                'course_name' => $request->course_name,
                'course_code' => $request->course_code,
                'credit_hours' => $request->credit_hours,
                'level' => $request->level,
                'semester' => $request->semester,
            ]);

            if (in_array($request->level, [1, 2])) {
                // فرقة أولى أو تانية → عام فقط
                $course->departments()->sync(ids: [1]);
            } else {
                // فرقة ثالثة أو رابعة
                $course->departments()->sync($request->department_ids);
            }
        });

        return back()->with('success', 'تم إضافة المقرر بنجاح');
    }


    /* =======================
            تعديل كورس 
   ======================== */
    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        $validator = Validator::make(
            $request->all(),
            $this->rules($course->id),
            $this->messages()
        );

        if ($validator->fails()) {
            return back()
                ->withErrors($validator, 'editSubjectModal-' . $course->id)
                ->withInput()
                ->with('open_modal', 'editSubjectModal-' . $course->id);
        }

        DB::transaction(function () use ($request, $course) {

            $course->update([
                'course_name' => $request->course_name,
                'course_code' => $request->course_code,
                'credit_hours' => $request->credit_hours,
                'level' => $request->level,
                'semester' => $request->semester,
            ]);

             if (in_array($request->level, [1, 2])) {
                // أولى / تانية → عام فقط
                $course->departments()->sync([1]);
            } else {
                // ثالثة / رابعة → الأقسام المختارة
                $course->departments()->sync($request->department_ids);
            }
        });

        return back()->with('success', 'تم تحديث المقرر بنجاح');
    }


}
