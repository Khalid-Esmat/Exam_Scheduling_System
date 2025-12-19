<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\InvigilatorController;
use App\Http\Controllers\Student\ExamController;
use App\Http\Controllers\Student\ProfileController;
use App\Http\Controllers\Invigilator\ExamController as InvigilatorExamController;
use App\Http\Controllers\Invigilator\ProfileController as InvigilatorProfileController;
use App\Http\Controllers\Invigilator\ViolationController;

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('auth.login');
})->name('login.form');
Route::get('/login', function () {
    return view('auth.login');
})->name('login.form');

Route::post('/login', LoginController::class)->name('login');
Route::post('/logout', LogoutController::class)->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Admin Dashboard Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->group(function () {

    Route::get('/admin', function () {
        return view('AdminPanel.layouts.sidebar');
    })->name('admin.dashboard');

    // ===============================
    // CRUD: Rooms
    // ===============================
    Route::get('/rooms', [RoomController::class, 'index'])->name('rooms.index');
    Route::post('/rooms', [RoomController::class, 'store'])->name('rooms.store');
    Route::put('/rooms/{room}', [RoomController::class, 'update'])->name('rooms.update');
    Route::delete('/rooms/{room}', [RoomController::class, 'destroy'])->name('rooms.destroy');

    // ===============================
    // CRUD: Courses
    // ===============================
    Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
    Route::post('/courses', [CourseController::class, 'store'])->name('courses.store');
    Route::put('/courses/{course}', [CourseController::class, 'update'])->name('courses.update');
    Route::delete('/courses/{course}', [CourseController::class, 'destroy'])->name('courses.destroy');

    // ===============================
    // CRUD: Invigilators
    // ===============================
    Route::get('/invigilators', [InvigilatorController::class, 'index'])->name('invigilators.index');
    Route::post('/invigilators', [InvigilatorController::class, 'store'])->name('invigilators.store');
    Route::put('/invigilators/{invigilator}', [InvigilatorController::class, 'update'])->name('invigilators.update');
    Route::delete('/invigilators/{invigilator}', [InvigilatorController::class, 'destroy'])->name('invigilators.destroy');

});

/*
|--------------------------------------------------------------------------
| Student Dashboard Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'student'])->group(function () {
    
    Route::get('/exams', [ExamController::class, 'index'])->name('student.dashboard');
    Route::get('/profile', [ProfileController::class, 'index'])->name('student.profile');
    // Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
});

/*
|--------------------------------------------------------------------------
| Invigilator Dashboard Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'invigilator'])->group(function () {
        Route::get('/exams', [InvigilatorExamController::class, 'index'])
            ->name('invigilator.dashboard');

        Route::get('/profile', [InvigilatorProfileController::class, 'index'])
            ->name('profile');
        Route::get('/violations', [ViolationController::class, 'index'])
            ->name('violations');

        // Route::post('/profile', [InvigilatorProfileController::class, 'update'])
        //     ->name('profile.update');

        Route::get('/violations/create', [ViolationController::class, 'create'])
            ->name('violations.create');

        // Route::post('/violations', [ViolationController::class, 'store'])
        //     ->name('violations.store');
});
