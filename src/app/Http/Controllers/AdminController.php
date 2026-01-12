<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use App\Models\Attendance;
use App\Models\StampCorrectionRequest;
use Illuminate\Support\Facades\DB;
use App\Models\BreakTime;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AdminAttendanceUpdateRequest;

class AdminController extends Controller
{
    public function login()
    {
        return view('auth.admin-login');
    }

    public function authenticate(Request $request)
    {
        // ① バリデーション（メッセージ指定）
        $credentials = $request->validate(
            [
                'email' => 'required|email',
                'password' => 'required',
            ],
            [
                'email.required'    => 'メールアドレスを入力してください',
                'email.email'       => 'メールアドレスの形式が正しくありません',
                'password.required' => 'パスワードを入力してください',
            ]
        );

        // ② ログイン試行
        if (Auth::attempt($credentials)) {

            // ③ 管理者チェック
            if (!Auth::user()->is_admin) {
                Auth::logout();

                return back()->withErrors([
                    'email' => 'ログイン情報が登録されていません',
                ]);
            }

            // ④ 管理者ログイン成功
            return redirect()->route('admin.attendance.list');
        }

        // ⑤ ログイン失敗
        return back()->withErrors([
            'email' => 'ログイン情報が登録されていません',
        ]);
    }

    public function attendanceList(Request $request)
    {
        $date = $request->query('date', now()->toDateString());

        // 表示用フォーマット
        $formattedDate = \Carbon\Carbon::parse($date)
            ->format('Y年n月j日');

        // 管理者以外のユーザー
        $users = User::where('is_admin', 0)
            ->with(['attendances' => function ($query) use ($date) {
                $query->where('date', $date)
                    ->with('breakTimes');
            }])
            ->get();

        return view('admin.admin-list', [
            'users' => $users,
            'formattedDate' => $formattedDate,
            'date' => $date,
        ]);
    }


    public function userList()
    {
        // 管理者以外のユーザー（スタッフ）を取得
        $users = User::where('is_admin', 0)->get();

        return view('admin.staff', [
            'users' => $users,
        ]);
    }

    // 修正申請一覧（承認待ち）
    public function correctionRequestList(Request $request)
    {
        $statusParam = $request->query('status', 'pending');
        $status = $statusParam === 'approved' ? 1 : 0;

        $requests = StampCorrectionRequest::with(['user', 'attendance'])
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.admin-request', [
            'requests' => $requests,
            'status' => $statusParam,
        ]);
    }

    public function show($id)
    {
        $attendance = Attendance::with(['user', 'breakTimes'])
            ->findOrFail($id);

        return view('admin.admin-detail', compact('attendance'));
    }

    public function update(AdminAttendanceUpdateRequest $request, $id)
    {
        $attendance = Attendance::with('breakTimes')->findOrFail($id);

        // FormRequest からバリデート済みデータを取得
        $validated = $request->validated();

        // 勤怠更新
        $attendance->update([
            'clock_in' => $validated['clock_in'],
            'clock_out' => $validated['clock_out'],
            'reason' => $validated['reason'],
            'correction_status' => 'approved',
        ]);

        // 休憩時間更新
        if (!empty($validated['break_times'])) {
            foreach ($attendance->breakTimes as $index => $break) {
                if (isset($validated['break_times'][$index])) {
                    $breakData = $validated['break_times'][$index];
                    $break->update([
                        'start_time' => $breakData['start_time'],
                        'end_time' => $breakData['end_time'] ?? null,
                    ]);
                }
            }
        }

        // 修正申請を承認済みに
        StampCorrectionRequest::where('attendance_id', $attendance->id)
            ->where('status', 0)
            ->update(['status' => 1]);

        return redirect()
            ->route('admin.attendance.list')
            ->with('success', '勤怠を修正・承認しました');
    }





    public function staffDetail($id)
    {
        $user = User::findOrFail($id);

        $month = request('month', now()->format('Y-m'));
        $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $end   = $start->copy()->endOfMonth();

        $attendances = Attendance::where('user_id', $user->id)
            ->whereBetween('date', [$start, $end])
            ->with('breakTimes')
            ->orderBy('date')
            ->get();

        return view('admin.staff-list', [
            'user' => $user,
            'attendances' => $attendances,
            'current_month' => $start->format('Y/m'), // 表示用
            'month_param'   => $start->format('Y-m'), // Carbon用 ←★追加
        ]);
    }

    public function requestList()
    {
        $requests = StampCorrectionRequest::with(['user', 'attendance'])->get();

        return view('admin.request', [
            'requests' => $requests,
        ]);
    }

    public function approveCorrection($id)
    {
        DB::transaction(function () use ($id) {

            $request = StampCorrectionRequest::with('attendance')
                ->findOrFail($id);

            $attendance = $request->attendance;

            // 勤怠を修正内容で更新
            $attendance->update([
                'clock_in'  => $request->clock_in,
                'clock_out' => $request->clock_out,
                'correction_status' => 'approved',
            ]);

            // 休憩時間を反映
            if ($request->break_times) {
                $attendance->breakTimes()->delete();

                foreach ($request->break_times as $break) {
                    $attendance->breakTimes()->create([
                        'start_time' => $break['start_time'],
                        'end_time'   => $break['end_time'],
                    ]);
                }
            }

            // 修正申請を承認済みに
            $request->update([
                'status' => 1,
            ]);
        });

        return redirect()
            ->route('admin.correction.list')
            ->with('success', '修正申請を承認しました');
    }


    public function showCorrectionRequest($id)
    {
        $correctionRequest = StampCorrectionRequest::with([
            'user',
            'attendance.breakTimes',
        ])->findOrFail($id);

        return view('admin.approve', [
            'correctionRequest' => $correctionRequest,
            'attendance' => $correctionRequest->attendance,
        ]);
    }
}
