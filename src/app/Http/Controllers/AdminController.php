<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AdminController extends Controller
{
    public function login()
    {
        return view('auth.admin-login');
    }

    public function authenticate()
    {
        // 後で Fortify や Guard を追加可能
        return redirect()->route('admin.attendance.list');
    }

    public function attendanceList()
    {
        $date = now()->format('Y-m-d');

        // ★ 管理者以外（一般ユーザー）
        $users = User::where('is_admin', false)
            ->with(['attendanceRecords' => function ($query) use ($date) {
                $query->whereDate('date', $date)
                    ->with('breakTimes');
            }])
            ->get();

        return view('admin.admin-list', compact('date', 'users'));
    }

    public function userList()
    {
        return view('admin.staff');
    }

    public function staffDetail()
    {
        return view('admin.staff-list');
    }

    public function correctionRequestList()
    {
        return view('admin.admin-request');
    }

    public function approveCorrectionRequest()
    {
        return view('admin.approve');
    }

    public function show()
    {
        return view('admin.admin-detail');
    }

    public function update()
    {
        return redirect()->route('admin.attendance.list');
    }
}
