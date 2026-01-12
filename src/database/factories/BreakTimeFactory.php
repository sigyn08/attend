<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;
use App\Models\Attendance;
use App\Models\BreakTime;

class BreakTimeFactory extends Factory
{
    protected $model = BreakTime::class;

    public function definition()
    {
        return [
            'attendance_id' => Attendance::factory(),
            'start_time' => Carbon::createFromTime(12, 0),
            'end_time' => Carbon::createFromTime(13, 0),
        ];
    }

    public function ongoing()
    {
        return $this->state(fn() => [
            'break_end' => null,
        ]);
    }
}
