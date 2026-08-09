<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\AdministratorController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::get('/logout', [AuthController::class, 'logout']);

Route::get('/administrator', [AdministratorController::class, 'getAll']);
Route::post('/administrator', [AdministratorController::class, 'add']);
Route::put('/administrator', [AdministratorController::class, 'update']);
Route::delete('/administrator', [AdministratorController::class, 'remove']);

Route::get('/student', [StudentController::class, 'getAll']);
Route::get('/student/{id}', [StudentController::class, 'getById']);
Route::post('/student', [StudentController::class, 'add']);
Route::put('/student', [StudentController::class, 'update']);
Route::delete('/student', [StudentController::class, 'remove']);

Route::get('/course', [CourseController::class, 'getAll']);
Route::get('/course/{id}', [CourseController::class, 'getById']);
Route::post('/course', [CourseController::class, 'add']);
Route::put('/course', [CourseController::class, 'update']);
Route::delete('/course', [CourseController::class, 'remove']);
