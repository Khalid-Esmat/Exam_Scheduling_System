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
use App\Http\Controllers\ExamSlotController;
use App\Http\Controllers\RoomAllocationController;
use App\Http\Controllers\ExamScheduleController;
use App\Http\Controllers\InvigilationController;
use App\Http\Controllers\StudentController;
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












/*
|--------------------------------------------------------------------------
| Step 1: Exam Slots (Time & Dates)
|--------------------------------------------------------------------------
| matches: route('exams.create'), route('exams.store'), etc.
*/
Route::controller(ExamSlotController::class)->prefix('examSlots')->name('examSlots.')->group(function () {
    Route::get('/create', 'create')->name('create');
    Route::post('/', 'store')->name('store');
    Route::put('/{examSlot}', 'update')->name('update'); // Matches the edit form
    Route::delete('/{examSlot}', 'destroy')->name('destroy');
});

/*
|--------------------------------------------------------------------------
| Step 2: Room Allocation (Distribution)
|--------------------------------------------------------------------------
| matches: route('allocation.store'), route('rooms.quickUpdate'), etc.
*/
Route::controller(RoomAllocationController::class)->prefix('roomsAllocation')->name('roomsAllocation.')->group(function () {
    // 1. Show the allocation page (The "Next Step" link)
    Route::get('/{slot}', 'show')->name('assign'); 
    
    // 2. Save allocations
    Route::post('/{slot}', 'store')->name('store');
    
    // 3. Delete a specific allocation
    Route::get('/delete/{id}', 'destroy')->name('destroy');
});

/*
|--------------------------------------------------------------------------
| AJAX Routes (API-like calls)
|--------------------------------------------------------------------------
| These are called by JavaScript fetch()
*/
Route::post('/rooms/{room}/quick-update', [RoomAllocationController::class, 'updateRoomCapacity'])
    ->name('rooms.quickUpdate');



Route::prefix('schedules')->group(function () {
    // 1. الصفحة الرئيسية للجدولة الشاملة
    Route::get('/all', [ExamScheduleController::class, 'indexAll'])->name('schedule.all');

    // 2. الجدولة اليدوية (التي سببت الخطأ)
    Route::get('/manual/{slot}', [ExamScheduleController::class, 'manualMode'])->name('schedule.manual');

    // 3. الجدولة التلقائية للكل
    Route::get('/generate-auto-all', [ExamScheduleController::class, 'autoGenerateAll'])->name('schedule.auto_all');
    
    // 4. حفظ الجدولة اليدوية (POST)
    Route::post('/manual/{slot}/save', [ExamScheduleController::class, 'saveManual'])->name('schedule.manual.save');
    // مسار التوليد التلقائي لمجموعة واحدة (الذي يطلبه الزر)
Route::get('/auto/{slot}', [ExamScheduleController::class, 'autoGenerate'])->name('schedule.auto');

});






/// مسارات توزيع المراقبين الشاملة
Route::prefix('invigilation')->group(function () {
    // الصفحة الرئيسية (عرض كل التواريخ)
    Route::get('/all', [InvigilationController::class, 'indexGlobal'])->name('invigilation.global');

    // حفظ التكليفات الشاملة
    Route::post('/save-all', [InvigilationController::class, 'saveGlobal'])->name('invigilation.save_global');
});










Route::get('/students', [StudentController::class, 'index'])->name('students.index');
Route::post('/students/save', [StudentController::class, 'store'])->name('students.store');
Route::post('/students/update', [StudentController::class, 'update'])->name('students.update');
Route::post('/students/delete', [StudentController::class, 'destroy'])->name('students.destroy');


Route::get('/dashboard/students' , function(){
    return view('students');
});