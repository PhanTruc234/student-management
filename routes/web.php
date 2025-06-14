<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\ScoreController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

Route::get('/', function () {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $user = Auth::user();

    if ($user->role === 'admin') {
        return redirect('/students');
    } else {
        return redirect('/dashboard');
    }
});
Route::middleware(['auth'])->group(function () {
    Route::resource('students', StudentController::class);
    Route::resource('subjects', SubjectController::class);
    Route::prefix('students/{student}')->as('students.')->group(function () {
        Route::resource('scores', ScoreController::class);
        Route::resource('attendances', AttendanceController::class);
    });
    Route::get('/scores', [ScoreController::class, 'allScores'])->name('scores.all');
    Route::get('/attendances', [AttendanceController::class, 'allAttendances'])->name('attendances.all');
});
Route::middleware('auth')->prefix('dashboard')->group(function () {
    Route::get('/', function () {
        return view('dashboard');
    })->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
// Load các route auth từ file riêng auth.php
require __DIR__ . '/auth.php';
