<?php

namespace Tests\Feature\Attendance;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use Carbon\Carbon;

class BreakTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 休憩ボタンが正しく機能する()
    {
        Carbon::setTestNow('2025-01-01 12:00:00');

        $user = User::factory()->create();

        $attendance = Attendance::create([
            'user_id'  => $user->id,
            'date'     => '2025-01-01',
            'clock_in' => '09:00:00',
        ]);

        $this->actingAs($user);

        $response = $this->post(route('attendance.breakIn'));

        $response->assertRedirect('/attendance');

        $this->assertDatabaseHas('break_times', [
            'attendance_id' => $attendance->id,
            'start_time'    => '12:00:00',
            'end_time'      => null,
        ]);

        $this->assertEquals('breaking', $attendance->fresh()->status);
    }


    /** @test */
    public function 休憩は一日に何回でもできる()
    {
        $user = User::factory()->create(['status' => 'working']);

        Attendance::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'clock_in' => '09:00:00',
        ]);

        $this->actingAs($user);

        // 1回目
        $this->post(route('attendance.breakIn'));
        $this->post(route('attendance.breakOut'));

        // 2回目も例外なく実行できる
        $response = $this->post(route('attendance.breakIn'));

        $response->assertStatus(302);
    }


    /** @test */
    public function 休憩戻ボタンが正しく機能する()
    {
        Carbon::setTestNow('12:30');

        $user = User::factory()->create(['status' => 'breaking']);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'clock_in' => '09:00:00',
        ]);

        BreakTime::create([
            'attendance_id' => $attendance->id,
            'start_time' => '12:00:00',
            'end_time' => null,
        ]);

        $this->actingAs($user);

        $this->post(route('attendance.breakOut'));

        $this->assertDatabaseHas('break_times', [
            'attendance_id' => $attendance->id,
            'end_time' => '12:30:00',
        ]);

        $this->assertEquals('working', $user->fresh()->status);
    }

    /** @test */
    public function 休憩時刻が勤怠一覧画面で確認できる()
    {
        $user = User::factory()->create();

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
        ]);

        BreakTime::create([
            'attendance_id' => $attendance->id,
            'start_time' => '12:00:00',
            'end_time' => '12:30:00',
        ]);

        $this->actingAs($user);

        $response = $this->get(route('user.list'));

        $response->assertStatus(200);
        $response->assertSee('0:30'); // ← 合計休憩時間
    }
}
