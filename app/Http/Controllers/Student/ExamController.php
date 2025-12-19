<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ExamController extends Controller{

    public function index(){
        return view("StudentPanel.examschedule");
    }
    
}
