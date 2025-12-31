<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use App\Models\Department;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        // 1. Fetch Students with User (Name/Email) and Department info
        $query = Student::with(['user', 'department', 'courses']);

        // Filter by Name/Code (Search in User table or Student table)
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                   ->orWhere('student_code', 'like', "%{$search}%");
            });
           
            
        }
        else{
             $query = Student::with(['user', 'department', 'courses']);
        }
        // Filter by Level
        if ($request->has('year') && !empty($request->year)) {
            $query->where('level', $request->year);
        }

        $students = $query->get();
        $departments = Department::all();
        $allCourses = Course::all();
        return view('students.index', compact('students', 'departments', 'allCourses'));
    }

    public function store(Request $request)
    {
        // Validation
        $request->validate([
            'full_name' => 'required|string',
            'email' => 'nullable|email|unique:users,email',
            'current_level' => 'required',
            'department_id' => 'required',
            'password' => 'required',
        ]);

        DB::transaction(function () use ($request) {
            // 1. Create User first (Name, Email, Password)
            $user = User::create([
                'name' => $request->full_name,
                'email' => $request->email ?? 'student'.time().'@hurghada.edu.eg',
                'password' => Hash::make($request->password), // Default password
                'role' => 'student'
            ]);

            // 2. Create Student Record linked to User
            $student = Student::create([
                'user_id' => $user->id,
                'level' => $request->current_level,
                'department_id' => $request->department_id,
                 'student_code' => $request->student_code // Uncomment if you add this column
            ]);

            // 3. Attach Subjects
            if ($request->has('subjects')) {
                $student->courses()->attach($request->subjects);
            }
        });

        return redirect()->back()->with('success', 'تم إضافة الطالب بنجاح');
    }

    public function update(Request $request)
    {
        $student = Student::with('user')->findOrFail($request->student_id);

        // Update User Table (Name, Email)
        $student->user->update([
            'name' => $request->full_name,
            'email' => $request->email
        ]);

        // Update Student Table (Level, Dept)
        $student->update([
            'level' => $request->current_level,
            'department_id' => $request->department_id,
             'student_code' => $request->student_code
        ]);

        return redirect()->back()->with('success', 'تم تعديل البيانات بنجاح');
    }

    public function destroy(Request $request)
    {
        $student = Student::findOrFail($request->student_id);
        
        // Delete the User (Cascades to Student if set up in DB, otherwise delete manually)
        $student->user->delete(); 
        // $student->delete(); // Handled by DB cascade usually

        return redirect()->back()->with('success', 'تم حذف الطالب بنجاح');
    }
}