<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\StampCorrectionRequestController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

Route::get('/admin/login', [AdminController::class, 'login'])->name('admin-login');
Route::post('/admin/login', [AdminController::class, 'authenticate'])->name('admin.login');


Route::post('/admin/logout', function () {
    Auth::logout();
    return redirect()->route('admin-login');
})->name('admin.logout');




Route::get('/login', [UserController::class, 'login'])->name('user-login');
Route::post('/login', [UserController::class, 'authenticate'])->name('user.login');
Route::get('/register', [UserController::class, 'register'])->name('user-register');
Route::post('/register', [UserController::class, 'store'])->name('user.register');
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})
    ->middleware('auth')
    ->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect('/attendance');
})
    ->middleware(['auth', 'signed'])
    ->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('message', '認証メールを再送しました');
})
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.send');

Route::post('/email/verification-send-and-open', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    // MailHogへリダイレクト
    return redirect()->away('http://localhost:8025');
})
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.send.and.open');

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/attendance', [UserController::class, 'index'])
        ->name('attendance.index');

    Route::post('/attendance', [UserController::class, 'attendance'])
        ->name('attendance.start');

    Route::post('/attendance/end', [UserController::class, 'attendanceEnd'])
        ->name('user.attendance.end');

    Route::post('/attendance/breakIn', [UserController::class, 'breakIn'])
        ->name('attendance.breakIn');

    Route::post('/attendance/breakOut', [UserController::class, 'breakOut'])
        ->name('attendance.breakOut');

    Route::get('/attendance/list', [UserController::class, 'attendanceList'])
        ->name('user.list');

    Route::get('/attendance/detail/{id}', [UserController::class, 'show'])
        ->name('attendance.show');

    Route::post('/attendance/detail/{id}', [UserController::class, 'submitCorrectionRequest'])
        ->name('attendance.submit_correction_request');

    Route::get('/user/stamp_correction_request/list', [UserController::class, 'correctionRequestList'])
        ->name('user.correction.list');
});



Route::prefix('admin')
    ->middleware('admin')
    ->group(function () {

        Route::get('/attendance/list', [AdminController::class, 'attendanceList'])
            ->name('admin.attendance.list');

        Route::get('/attendance/{id}', [AdminController::class, 'show'])
            ->name('admin.attendances.show');

        Route::post('/attendance/{id}', [AdminController::class, 'update'])
            ->name('admin.attendances.update');

        Route::get('/staff/list', [AdminController::class, 'userList'])
            ->name('admin.staff.list');

        Route::get('/attendance/staff/{id}', [AdminController::class, 'staffDetail']);

        // 修正申請一覧
        Route::get(
            '/stamp_correction_request/list',
            [AdminController::class, 'correctionRequestList']
        )->name('admin.correction.list');

        // 修正申請詳細
        Route::get(
            '/stamp_correction_request/{id}',
            [AdminController::class, 'showCorrectionRequest']
        )->name('admin.correction.show');

        // 承認
        Route::post(
            '/stamp_correction_request/{id}/approve',
            [AdminController::class, 'approveCorrection']
        )->name('admin.correction.approve');

        Route::get(
            '/staff/{user}/attendance/csv',
            [ExportController::class, 'export']
        )->name('admin.attendance.csv');
    });
