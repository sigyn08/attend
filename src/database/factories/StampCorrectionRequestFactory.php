<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Attendance;
use App\Models\User;
use App\Models\StampCorrectionRequest;

class StampCorrectionRequestFactory extends Factory
{
    protected $model = StampCorrectionRequest::class;

    public function definition()
    {
        return [
            'attendance_id' => Attendance::factory(),
            'user_id' => User::factory(),
            'clock_in' => '09:00',
            'clock_out' => '18:00',

            // ★ json_encode をやめる
            'break_times' => [
                [
                    'start_time' => '12:00',
                    'end_time'   => '13:00',
                ]
            ],

            'reason' => 'テスト用修正申請',
            'status' => 0, // ← 数値にする（地味に重要）
        ];
    }

    public function approved()
    {
        return $this->state(fn() => [
            'status' => 1,
        ]);
    }
}
