<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Attendance extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'clock_in',
        'clock_out',
        'break_start',
        'break_end',
        'break_minutes'
    ];

    /**
     * 今日の勤怠レコードスコープ
     */
    public function scopeToday($query, $userId)
    {
        return $query->where('user_id', $userId)
            ->where('date', today()->toDateString());
    }

    /**
     * 状態判定（出勤前 / 出勤中 / 休憩中 / 退勤）
     */
    public function getStatusAttribute()
    {
        if (!$this->clock_in) return 'not_worked';
        if ($this->clock_in && !$this->clock_out && !$this->break_start) return 'working';
        if ($this->break_start && !$this->break_end) return 'breaking';
        if ($this->clock_out) return 'finished';
    }

    /**
     * 休憩の経過時間（休憩中のときだけ）
     */
    public function getCurrentBreakMinutes()
    {
        if (!$this->break_start || $this->break_end) return 0;

        return Carbon::parse($this->break_start)->diffInMinutes(Carbon::now());
    }
}
