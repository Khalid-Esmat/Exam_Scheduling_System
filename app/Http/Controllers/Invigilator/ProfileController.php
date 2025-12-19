<?php

namespace App\Http\Controllers\Invigilator;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller{
    public function index(){
        return view("InvigilatorPanel.profile");
    }
    
}
