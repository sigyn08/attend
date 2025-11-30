<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BreakTime extends Model
{
    use HasFactory;

    protected $table = 'break_times';

    // Attendanceリレーション
    public function attendance()
    {
        return $this->belongsTo(AttendanceRecord::class, 'attendance_id');
    }
}
