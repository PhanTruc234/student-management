<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\ScoreController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Middleware\CheckAdmin;

// Trang chủ
Route::get('/', function () {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    return redirect()->route('dashboard');
});
// Dashboard chung cho cả user và admin
Route::middleware(['auth'])->prefix('dashboard')->group(function () {
    Route::get('/', fn() => view('dashboard'))->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::middleware(['auth', CheckAdmin::class])->group(function () {
    Route::resource('students', StudentController::class);
    Route::resource('subjects', SubjectController::class);

    Route::prefix('students/{student}')->as('students.')->group(function () {
        Route::resource('scores', ScoreController::class);
        Route::resource('attendances', AttendanceController::class);
    });

    Route::get('/scores', [ScoreController::class, 'allScores'])->name('scores.all');
    Route::get('/attendances', [AttendanceController::class, 'allAttendances'])->name('attendances.all');
});

// Logout
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

// Auth routes (login, register...)
require __DIR__ . '/auth.php';
