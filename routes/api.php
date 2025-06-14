<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\StudentApiController;
use App\Http\Controllers\Api\SubjectApiController;
use App\Http\Controllers\Api\ScoreApiController;
use App\Http\Controllers\Api\AttendanceApiController;

// Routes cho Students
Route::get('/students', [StudentApiController::class, 'index']);
Route::post('/students', [StudentApiController::class, 'store']);
Route::put('/students/{student}', [StudentApiController::class, 'update']);
Route::delete('/students/{student}', [StudentApiController::class, 'destroy']);

// Routes cho Subjects
Route::get('/subjects', [SubjectApiController::class, 'index']);
Route::post('/subjects', [SubjectApiController::class, 'store']);
Route::put('/subjects/{subject}', [SubjectApiController::class, 'update']);
Route::delete('/subjects/{subject}', [SubjectApiController::class, 'destroy']);

// Routes cho Scores
Route::get('/scores', [ScoreApiController::class, 'index']);
Route::get('/students/{student}/scores', [ScoreApiController::class, 'scoresByStudent']);
Route::post('/students/{student}/scores', [ScoreApiController::class, 'store']);
Route::put('/scores/{score}', [ScoreApiController::class, 'update']);
Route::delete('/scores/{score}', [ScoreApiController::class, 'destroy']);
Route::apiResource('scores', ScoreApiController::class)->shallow();

// Routes cho Attendance
Route::get('/students/{student}/attendances', [AttendanceApiController::class, 'index']);
Route::post('/students/{student}/attendances', [AttendanceApiController::class, 'store']);
Route::put('/attendances/{attendance}', [AttendanceApiController::class, 'update']);
Route::delete('/attendances/{attendance}', [AttendanceApiController::class, 'destroy']);
