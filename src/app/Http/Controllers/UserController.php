<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

class UserController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }

    public function register()
    {
        return view('auth.register');
    }

    public function authenticate(Request $request)
    {
        // バリデーションと認証ロジックをここに追加

        return redirect()->route('user.attendance');
    }

    public function store(Request $request)
    {
        // ユーザー登録ロジックをここに追加

        return redirect()->route('user.attendance');
    }

    public function attendanceList(Request $request)
    {
        // 出勤情報リスト取得ロジックをここに追加

        return view('user.list');
    }

    public function correctionRequestList(Request $request)
    {
        // 打刻修正申請リスト取得ロジックをここに追加

        return view('user.request');
    }

    public function show($id)
    {
        // 出勤情報詳細取得ロジックをここに追加

        return view('user.detail');
    }

    public function submitCorrectionRequest(Request $request, $id)
    {
        // 打刻修正申請送信ロジックをここに追加

        return redirect()->route('user.list');
    }

    public function attendance()
    {
        $now = \Carbon\Carbon::now();

        $date = $now->format('Y年n月j日（D）');      // 例: 2025-11-30
        $time = $now->format('H:i:s');     // 例: 13:05:12

        // view に変数を渡す（これが一番明確）
        return view('user.attendance-breakin', compact('date', 'time'));
    }

    public function attendanceEnd()
    {
        $now = \Carbon\Carbon::now();

        $date = $now->format('Y年n月j日（D）'); // 日付
        $time = $now->format('H:i');           // 時刻
        $status_label = '退勤済';            // ← 好きな文言に変更可（例: "退勤中" など）

        return view('user.attendance-end', compact('date', 'time', 'status_label'));
    }


    public function breakIn()
    {
        $now = Carbon::now();
        $date = $now->format('Y年n月j日（D）');
        $time = $now->format('H:i');
        $status_label = '休憩中';

        return view('user.attendance-breakout', compact('date', 'time', 'status_label'));
    }


    public function break()
    {
        $now = Carbon::now();
        $date = $now->format('Y年n月j日（D）'); // 例: 2023年6月1日(木)
        $time = $now->format('H:i');           // 08:00 のような表示
        $status_label = '出勤中';              // 実際は勤怠状態から判定する

        return view('user.attendance-breakout', compact('date', 'time', 'status_label'));
    }

    public function breakOut()
    {
        $now = Carbon::now();
        $date = $now->format('Y年n月j日（D）'); // 例: 2023年6月1日(木)
        $time = $now->format('H:i');           // 08:00 のような表示
        $status_label = '休憩中';              // 実際は勤怠状態から判定する

        return view('user.attendance-breakin', compact('date', 'time', 'status_label'));
    }
}
