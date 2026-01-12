<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Attendance;
use Carbon\Carbon;
use App\Models\User;


class AttendanceFactory extends Factory
{
    protected $model = Attendance::class;

    public function definition()
    {
        return [
            'user_id'   => User::factory(),
            'date'      => Carbon::today(),
            'clock_in'  => null,
            'clock_out' => null,
        ];
    }
}
