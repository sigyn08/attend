<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\StampCorrectionRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UserAttendanceCorrectionRequest;
use Illuminate\Auth\Events\Registered;

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

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = auth()->user();
            $today = Carbon::today()->toDateString();

            $todayAttendance = Attendance::where('user_id', $user->id)
                ->where('date', $today)
                ->first();

            // 今日の勤怠がなければステータスをリセット
            if (! $todayAttendance) {
                $user->status = null; // 未出勤状態
                $user->save();
            }

            return redirect()->route('attendance.index');
        }

        return back()->withErrors([
            'email' => 'ログイン情報が登録されていません',
        ]);
    }

    // 出勤処理
    public function attendance()
    {
        $user = auth()->user();
        $now = Carbon::now();

        $attendance = Attendance::today($user->id)->first();

        if (!$attendance) {
            Attendance::create([
                'user_id'  => $user->id,
                'date'     => $now->toDateString(),
                'clock_in' => $now->format('H:i:s'),
            ]);

            $user->status = 'working';
            $user->save();
        }

        return redirect()->route('attendance.index');
    }

    // 退勤処理
    public function attendanceEnd()
    {
        $user = auth()->user();
        $now = Carbon::now();

        $attendance = Attendance::where('user_id', $user->id)
            ->whereNull('clock_out')
            ->orderBy('date', 'desc')
            ->firstOrFail();

        $attendance->update([
            'clock_out' => $now->format('H:i:s'),
        ]);

        $user->status = 'finished';
        $user->save();

        return redirect()->route('attendance.index');
    }

    // 休憩開始
    public function breakIn()
    {
        $user = auth()->user();
        $now = Carbon::now();

        $attendance = Attendance::today($user->id)->firstOrFail();

        BreakTime::create([
            'attendance_id' => $attendance->id,
            'start_time'    => $now->format('H:i:s'),
        ]);

        $user->status = 'breaking';
        $user->save();

        return redirect()->route('attendance.index');
    }

    // 休憩戻り
    public function breakOut()
    {
        $user = auth()->user();
        $now = Carbon::now();
        $attendance = Attendance::today($user->id)->firstOrFail();

        $break = $attendance->breakTimes()->whereNull('end_time')->latest()->first();

        if ($break) {
            $break->update([
                'end_time' => $now->format('H:i:s')
            ]);
        }

        $user->status = 'working';
        $user->save();

        return redirect()->route('attendance.index');
    }

    public function index()
    {
        $user = auth()->user();
        $now = Carbon::now();

        $attendance = Attendance::today($user->id)->first();

        $status = $attendance?->status;

        $statusLabel = '勤務外';

        if ($attendance) {
            $statusLabel = match ($attendance->status) {
                'working'   => '出勤中',
                'breaking'  => '休憩中',
                'finished'  => '退勤済',
                default     => '勤務外',
            };
        }

        return view('user.attendance', [
            'date' => $now,
            'time' => $now->format('H:i'),
            'status' => $status,
            'status_label' => $statusLabel,
        ]);
    }

    public function store(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('verification.notice');
    }

    // （クラス内のメソッドとして置き換えてください）
    public function attendanceList(Request $request)
    {
        $user = auth()->user();
        $today = Carbon::today()->toDateString();

        $todayAttendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        if (! $todayAttendance) {
            $user->update(['status' => null]);
        }

        if (! $user) {
            // 未ログイン時の扱い（ログインページへリダイレクトするか空データで表示）
            return redirect()->route('user-login');
        }

        // month パラメータ（例: 2025-12）を受け取る。なければ今月。
        $monthParam = $request->query('month', now()->format('Y-m'));
        try {
            $currentMonth = Carbon::parse($monthParam . '-01');
        } catch (\Exception $e) {
            $currentMonth = now();
        }

        $start = $currentMonth->copy()->startOfMonth()->toDateString();
        $end   = $currentMonth->copy()->endOfMonth()->toDateString();

        // attendances を取得（breakTimes を eager load）
        $attendances = Attendance::with('breakTimes')
            ->where('user_id', $user->id)
            ->whereBetween('date', [$start, $end])
            ->orderBy('date', 'desc')
            ->get();

        return view('user.list', [
            'attendances'    => $attendances,
            'current_month'  => $currentMonth->format('Y/m'),
            'current_month_param' => $currentMonth->format('Y-m'),
        ]);
    }

    public function show($id)
    {
        $attendance = Attendance::with([
            'user',
            'breakTimes',
            'correctionRequests' => function ($query) {
                $query->where('status', 0) // 承認待ち
                    ->latest();
            }
        ])->findOrFail($id);

        // 最新の承認待ち申請（なければ null）
        $pendingRequest = $attendance->correctionRequests->first();

        return view('user.detail', [
            'attendance' => $attendance,
            'pendingRequest' => $pendingRequest,
        ]);
    }


    public function submitCorrectionRequest(
        UserAttendanceCorrectionRequest $request,
        $id
    ) {
        $attendance = Attendance::findOrFail($id);

        // ★ 未承認の申請が存在するか確認
        $existsPending = StampCorrectionRequest::where('attendance_id', $attendance->id)
            ->where('status', 0)
            ->exists();

        if ($existsPending) {
            return back()->withErrors([
                'reason' => '現在承認待ちの申請があるため、新たに申請できません。',
            ]);
        }

        // 休憩時間まとめ
        $breakTimes = [];
        foreach ($request->break_start ?? [] as $i => $start) {
            if ($start && ($request->break_end[$i] ?? null)) {
                $breakTimes[] = [
                    'start_time' => $start,
                    'end_time'   => $request->break_end[$i],
                ];
            }
        }

        StampCorrectionRequest::create([
            'attendance_id' => $attendance->id,
            'user_id'       => auth()->id(),
            'clock_in'      => $request->clock_in,
            'clock_out'     => $request->clock_out,
            'break_times'   => $breakTimes,
            'reason'        => $request->reason,
            'status'        => 0,
        ]);

        return redirect()
            ->route('user.list')
            ->with('success', '修正申請を送信しました');
    }

    public function correctionRequestList(Request $request)
    {
        $user = auth()->user();
        $statusParam = $request->query('status', 'pending');

        $status = $statusParam === 'approved' ? 1 : 0;

        $correctionRequests = StampCorrectionRequest::with('attendance')
            ->where('user_id', $user->id)
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('user.request', [
            'correctionRequests' => $correctionRequests,
            'status' => $statusParam,
        ]);
    }
}
