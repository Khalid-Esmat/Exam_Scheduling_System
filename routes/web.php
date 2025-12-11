<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoomController;






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