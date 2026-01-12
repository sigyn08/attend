<?php

namespace Tests\Feature\Attendance;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;

class UserAttendanceDetailTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_name_is_correct()
    {
        $user = User::factory()->create([
            'name' => '山田 太郎',
        ]);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2025-01-08',
        ]);

        $response = $this->actingAs($user)
            ->get(route('attendance.show', $attendance->id));

        $response->assertStatus(200);
        $response->assertSee('山田 太郎');
    }

    /** @test */
    public function test_date_is_correct()
    {
        $user = User::factory()->create();

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2025-01-08',
        ]);

        $response = $this->actingAs($user)
            ->get(route('attendance.show', $attendance->id));

        $response->assertStatus(200);
        $response->assertSee('2025年1月8日');
    }

    /** @test */
    public function test_clock_in_and_out_times_are_correct()
    {
        $user = User::factory()->create();

        $attendance = Attendance::factory()->create([
            'user_id'   => $user->id,
            'date'      => '2025-01-08',
            'clock_in'  => '09:00:00',
            'clock_out' => '18:00:00',
        ]);

        $response = $this->actingAs($user)
            ->get(route('attendance.show', $attendance->id));

        $response->assertStatus(200);

        // input type="time" の value を確認
        $response->assertSee('value="09:00"', false);
        $response->assertSee('value="18:00"', false);
    }

    /** @test */
    public function test_break_times_are_correct()
    {
        $user = User::factory()->create();

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2025-01-08',
        ]);

        BreakTime::factory()->create([
            'attendance_id' => $attendance->id,
            'start_time' => '12:00:00',
            'end_time'   => '13:00:00',
        ]);

        $response = $this->actingAs($user)
            ->get(route('attendance.show', $attendance->id));

        $response->assertStatus(200);

        // 休憩時間表示（input time）
        $response->assertSee('value="12:00"', false);
        $response->assertSee('value="13:00"', false);
    }
}
