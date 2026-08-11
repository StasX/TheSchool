<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\AdministratorController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::get('/logout', [AuthController::class, 'logout']);

Route::get('/administrator', [AdministratorController::class, 'getAll']);
Route::get('/administrator/{id}', [AdministratorController::class, 'getById']);
Route::post('/administrator', [AdministratorController::class, 'add']);
Route::put('/administrator/{id}', [AdministratorController::class, 'update']);
Route::delete('/administrator/{id}', [AdministratorController::class, 'remove']);

Route::get('/student', [StudentController::class, 'getAll']);
Route::get('/student/{id}', [StudentController::class, 'getById']);
Route::post('/student', [StudentController::class, 'add']);
Route::put('/student/{id}', [StudentController::class, 'update']);
Route::delete('/student/{id}', [StudentController::class, 'remove']);

Route::get('/course', [CourseController::class, 'getAll']);
Route::get('/course/{id}', [CourseController::class, 'getById']);
Route::post('/course', [CourseController::class, 'add']);
Route::put('/course/{id}', [CourseController::class, 'update']);
Route::delete('/course/{id}', [CourseController::class, 'remove']);
