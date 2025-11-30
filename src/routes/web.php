<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\StampCorrectionRequestController;


//====================
// 管理者側
//====================
Route::get('/admin/login', [AdminController::class, 'login'])->name('admin-login');
Route::post('/admin/login', [AdminController::class, 'authenticate'])->name('admin.login');

Route::get('/admin/attendance/list', [AdminController::class, 'attendanceList'])->name('admin.attendance.list');
Route::get('/admin/attendance/{id}', [AdminController::class, 'show'])->name('admin.attendances.show');
Route::post('/admin/attendance/{id}', [AdminController::class, 'update']);

Route::get('/admin/staff/list', [AdminController::class, 'userList'])->name('admin.staff.list');
Route::get('/admin/attendance/staff/{id}', [AdminController::class, 'staffDetail']);
Route::get('/admin/stamp_correction_request/list', [AdminController::class, 'correctionRequestList']);
Route::get('/stamp_correction_request/approve/{attendance_correct_request_id}', [AdminController::class, 'approveCorrectionRequest']);

Route::get('/login', [UserController::class, 'login'])->name('user-login');
Route::post('/login', [UserController::class, 'authenticate'])->name('user.login');
Route::get('/register', [UserController::class, 'register'])->name('user-register');
Route::post('/register', [UserController::class, 'store'])->name('user.register');
Route::get('/attendance', [AttendanceController::class, 'index'])->name('user.attendance');
Route::get('/attendance/list', [UserController::class, 'attendanceList'])->name('user.list');
Route::get('stamp_correction_request/list', [UserController::class, 'correctionRequestList']);
Route::get('/attendance/detail/{id}', [UserController::class, 'show'])->name('attendance.show');
Route::post('/attendance/detail/{id}', [UserController::class, 'submitCorrectionRequest'])->name('attendance.submit_correction_request');
Route::post('/attendance', [UserController::class, 'attendance'])->name('user.attendance');
// 出勤 → 退勤画面
Route::post('/attendance/breakin', [UserController::class, 'attendanceEnd'])
    ->name('user.attendance.end');

// 休憩開始（休憩入）ボタン押下
Route::post('/attendance/breakIn', [UserController::class, 'breakIn'])
    ->name('attendance.breakIn');;

// 休憩戻り
Route::post('/attendance/breakin/back', [UserController::class, 'break'])
    ->name('user.attendance.breakback');

// 休憩終了（休憩戻）ボタン押下
Route::post('/attendance/breakout', [UserController::class, 'breakOut'])
    ->name('attendance.breakOut');