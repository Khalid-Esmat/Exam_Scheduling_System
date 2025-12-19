<?php

namespace App\Http\Controllers\Invigilator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ViolationController extends Controller{
    public function index(){
        return view("InvigilatorPanel.reportviolation");
    }
    
}
