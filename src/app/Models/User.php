<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // 勤怠記録 (1対多)
    public function attendanceRecords()
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    // 修正申請 (1対多)
    public function correctionRequests()
    {
        return $this->hasMany(CorrectionRequest::class);
    }

    // 管理者かチェック
    public function isAdmin()
    {
        return $this->role === 'admin';
    }
}
