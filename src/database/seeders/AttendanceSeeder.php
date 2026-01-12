<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    public function run()
    {
        // ユーザー作成 or 取得
        $user = User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => '西　怜奈',
                'password' => bcrypt('password'),
                'is_admin' => 0,
            ]
        );

        // 対象：2025年11月（必要なら年を変えてOK）
        $start = Carbon::create(2025, 11, 1);
        $end   = $start->copy()->endOfMonth();

        while ($start <= $end) {

            // 日曜日はスキップ
            if ($start->isSunday()) {
                $start->addDay();
                continue;
            }

            // 勤怠作成
            $attendance = Attendance::create([
                'user_id'   => $user->id,
                'date'      => $start->toDateString(),
                'clock_in'  => '09:00:00',
                'clock_out' => '18:00:00',
            ]);

            // 休憩（1時間）
            BreakTime::create([
                'attendance_id' => $attendance->id,
                'start_time' => '12:00:00',
                'end_time'   => '13:00:00',
            ]);

            $start->addDay();
        }
    }
}
