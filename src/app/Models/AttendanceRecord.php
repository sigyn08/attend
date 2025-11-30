<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceRecord extends Model
{
    use HasFactory;

    protected $table = 'attendances';

    // 日付カラムのキャスト
    protected $dates = ['date', 'created_at', 'updated_at'];

    // BreakTimesリレーション
    public function breakTimes()
    {
        return $this->hasMany(BreakTime::class, 'attendance_id');
    }
}
