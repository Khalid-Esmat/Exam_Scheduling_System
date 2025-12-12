<?php

namespace App\Http\Controllers;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller{

    public function index(){

        $courses = Course::with('departments')->get();
        return view("dashboard.courses",compact('courses'));
    }
    
}
