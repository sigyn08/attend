<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Database\Factories\AttendanceFactory;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'clock_in',
        'clock_out',
    ];

    protected $dates = ['date', 'created_at', 'updated_at'];

    protected $casts = [
        'date'      => 'date',
    ];

    protected $appends = [
        'total_work_minutes',
        'total_break_minutes',
    ];

    /**
     * 今日の勤怠レコード
     */
    public function scopeToday($query, $userId)
    {
        return $query
            ->where('user_id', $userId)
            ->whereDate('date', Carbon::today());
    }

    /**
     * 状態判定（複数休憩対応）
     */
    public function getStatusAttribute()
    {
        if (!$this->clock_in) {
            return 'not_worked';
        }

        // 最新の休憩レコード取得
        $lastBreak = $this->breakTimes()->latest()->first();

        // 退勤済み
        if ($this->clock_out) {
            return 'finished';
        }

        // 休憩中判定
        if ($lastBreak && !$lastBreak->end_time) {
            return 'breaking';
        }

        return 'working';
    }

    /**
     * 現在の休憩中の経過時間（複数休憩対応）
     */
    public function getCurrentBreakMinutes()
    {
        $lastBreak = $this->breakTimes()->whereNull('end_time')->latest()->first();

        if (!$lastBreak) return 0;

        return Carbon::parse($lastBreak->start_time)
            ->diffInMinutes(Carbon::now());
    }

    /**
     * 複数休憩リレーション
     */
    public function breakTimes()
    {
        return $this->hasMany(BreakTime::class, 'attendance_id');
    }

    public function getTotalBreakMinutesAttribute()
    {
        return $this->breakTimes->sum(function ($break) {
            if (!$break->start_time || !$break->end_time) {
                return 0;
            }

            return \Carbon\Carbon::parse($break->start_time)
                ->diffInMinutes(\Carbon\Carbon::parse($break->end_time));
        });
    }

    public function getTotalWorkMinutesAttribute()
    {
        if (!$this->clock_in || !$this->clock_out) {
            return 0;
        }

        $date = $this->date->format('Y-m-d');

        $clockIn  = Carbon::parse($date . ' ' . $this->clock_in);
        $clockOut = Carbon::parse($date . ' ' . $this->clock_out);

        // 日跨ぎ対応
        if ($clockOut->lessThan($clockIn)) {
            $clockOut->addDay();
        }

        $workMinutes = $clockIn->diffInMinutes($clockOut);

        return max(0, $workMinutes - $this->total_break_minutes);
    }


    /**
     * ユーザーリレーション ←★これが必要
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function correctionRequests()
    {
        return $this->hasMany(StampCorrectionRequest::class);
    }
}
