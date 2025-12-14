<?php

use App\Http\Controllers\CourseController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\InvigilatorController;
use App\Http\Controllers\ExamScheduleController;







Route::get('/', function () {
    return view('layouts.sidebar');
});


//dashboard routes

// ===============================
// CRUD: Rooms
// ===============================
Route::get('/rooms',[RoomController::class,'index'])->name('rooms.index');
Route::delete('/rooms/{room}',[RoomController::class,'destroy'])->name('rooms.destroy');
Route::post('/rooms',action: [RoomController::class,'store'])->name('rooms.store');
Route::put('/rooms/{room}', [RoomController::class, 'update'])->name('rooms.update');


// ===============================
// CRUD: Courses
// ===============================
Route::get('/courses',[CourseController::class,'index'])->name('courses.index');
Route::delete('/courses/{course}',[CourseController::class,'destroy'])->name('courses.destroy');
Route::post('/courses',action: [CourseController::class,'store'])->name('courses.store');
Route::put('/courses/{course}', [CourseController::class, 'update'])->name('courses.update');


// ===============================
// CRUD: Invigilators
// ===============================
Route::get('/invigilators',[InvigilatorController::class,'index'])->name('invigilators.index');
Route::delete('/invigilators/{invigilator}',[InvigilatorController::class,'destroy'])->name('invigilators.destroy');
Route::post('/invigilators',action: [InvigilatorController::class,'store'])->name('invigilators.store');
Route::put('/invigilators/{invigilator}', [InvigilatorController::class, 'update'])->name('invigilators.update');
