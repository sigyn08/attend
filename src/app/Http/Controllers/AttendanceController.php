<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    // ユーザーIDを取得（認証なし）
    private function getUserId()
    {
        // セッションに保存されたユーザーIDを返す
        return session('user_id', null);
    }

    // 擬似ログイン
    public function fakeLogin(Request $request)
    {
        // フォームで送信されたユーザーIDをセッションに保存
        $userId = $request->input('user_id', 1); // デフォルト1
        session(['user_id' => $userId]);
        return redirect()->route('attendance.index');
    }

    // 擬似ログアウト
    public function fakeLogout(Request $request)
    {
        $request->session()->forget('user_id');
        return redirect('/login');
    }

    /**
     * 勤怠画面
     */
    public function index()
    {
        $userId = auth()->id(); // 未ログインの場合 null

        if (!$userId) {
            // 未ログイン用ダミーデータ
            return view('user.attendance', [
                'attendance'   => null,
                'status'       => 'not_logged_in',
                'status_label' => 'ログインしてください',
                'date'         => now()->format('Y年n月j日（D）'),
                'time'         => now()->format('H:i'),
            ]);
        }

        // ログインユーザーの勤怠取得
        $attendance = Attendance::today($userId)->first();

        if (!$attendance) {
            $attendance = new Attendance([
                'user_id'       => $userId,
                'clock_in'      => null,
                'clock_out'     => null,
                'break_start'   => null,
                'break_end'     => null,
                'break_minutes' => 0,
            ]);
        }

        $status = $attendance->status ?? 'not_worked';
        $labels = [
            'not_worked' => '勤務外',
            'working'    => '出勤中',
            'breaking'   => '休憩中',
            'finished'   => '退勤済',
        ];
        $status_label = $labels[$status] ?? '勤務外';

        return view('user.attendance', [
            'attendance'   => $attendance,
            'status'       => $status,
            'status_label' => $status_label,
            'date'         => now()->format('Y年n月j日（D）'),
            'time'         => now()->format('H:i'),
        ]);
    }



    /**
     * 出勤
     */
    public function clockIn(Request $request)
    {
        $userId = $this->getUserId();
        $attendance = Attendance::today($userId)->first();

        if ($attendance && $attendance->clock_in) {
            return back()->with('error', 'すでに出勤済みです。');
        }

        Attendance::create([
            'user_id'  => $userId,
            'date'     => today()->toDateString(),
            'clock_in' => now(),
            'break_minutes' => 0,
        ]);

        return back()->with('message', '出勤しました。');
    }

    /**
     * 退勤
     */
    public function clockOut(Request $request)
    {
        $userId = $this->getUserId();
        $attendance = Attendance::today($userId)->first();

        if (!$attendance || !$attendance->clock_in) {
            return back()->with('error', 'まず出勤してください。');
        }

        // 休憩中は退勤不可
        if ($attendance->break_start && !$attendance->break_end) {
            return back()->with('error', '休憩中のため退勤できません。');
        }

        // 退勤済み
        if ($attendance->clock_out) {
            return back()->with('error', 'すでに退勤済みです。');
        }

        $attendance->update([
            'clock_out' => now(),
        ]);

        return back()->with('message', '退勤しました。');
    }

    /**
     * 休憩開始
     */
    public function breakStart(Request $request)
    {
        $userId = $this->getUserId();
        $attendance = Attendance::today($userId)->first();

        if (!$attendance || !$attendance->clock_in) {
            return back()->with('error', 'まず出勤してください。');
        }

        if ($attendance->break_start && !$attendance->break_end) {
            return back()->with('error', 'すでに休憩中です。');
        }

        if ($attendance->clock_out) {
            return back()->with('error', '退勤後は休憩できません。');
        }

        $attendance->update([
            'break_start' => now(),
            'break_end'   => null,
        ]);

        return back()->with('message', '休憩に入りました。');
    }

    /**
     * 休憩終了
     */
    public function breakEnd(Request $request)
    {
        $userId = $this->getUserId();
        $attendance = Attendance::today($userId)->first();

        if (!$attendance || !$attendance->break_start) {
            return back()->with('error', '休憩に入っていません。');
        }

        if ($attendance->break_end) {
            return back()->with('error', 'すでに休憩終了済みです。');
        }

        // 休憩時間を計算
        $minutes = Carbon::parse($attendance->break_start)
            ->diffInMinutes(now());

        $attendance->update([
            'break_end'     => now(),
            'break_minutes' => $attendance->break_minutes + $minutes,
        ]);

        return back()->with('message', '休憩を終了しました。');
    }
}
