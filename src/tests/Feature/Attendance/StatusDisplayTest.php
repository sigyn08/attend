<?php

namespace Tests\Feature\Attendance;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use \App\Models\Attendance;
use \App\Models\BreakTime;
use Carbon\Carbon;

class StatusDisplayTest extends TestCase
{

    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(
            Carbon::create(2025, 12, 31, 9, 0, 0)
        );
    }


    /**
     * 勤務外ステータスが正しく表示される
     */
    public function test_status_off_work_is_displayed()
    {
        $user = User::factory()->create();

        // 今日の勤怠データ（未出勤）
        Attendance::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'clock_in' => null,
            'clock_out' => null,
        ]);

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertStatus(200);
        $response->assertSee('勤務外');
    }

    /**
     * 出勤中ステータスが正しく表示される
     */
    public function test_status_working_is_displayed()
    {
        $user = User::factory()->create();

        $attendance = Attendance::factory()->create([
            'user_id'   => $user->id,
            'date'      => Carbon::today()->toDateString(),
            'clock_in'  => '09:00:00',
            'clock_out' => null,
        ]);

        $this->actingAs($user)
            ->get('/attendance')
            ->assertStatus(200)
            ->assertSee('出勤中');
    }

    /**
     * 休憩中ステータスが正しく表示される
     */
    public function test_status_on_break_is_displayed()
    {
        $user = User::factory()->create();

        $attendance = Attendance::factory()->create([
            'user_id'   => $user->id,
            'date'      => Carbon::today()->toDateString(),
            'clock_in'  => '09:00:00',
            'clock_out' => null,
        ]);

        BreakTime::create([
            'attendance_id' => $attendance->id,
            'start_time'    => '12:00:00',
            'end_time'      => null,
        ]);

        $this->actingAs($user)
            ->get('/attendance')
            ->assertStatus(200)
            ->assertSee('休憩中');
    }

    /**
     * 退勤済ステータスが正しく表示される
     */
    public function test_status_finished_is_displayed()
    {
        $user = User::factory()->create(); // ← これを必ず追加

        Attendance::factory()->create([
            'user_id'   => $user->id,
            'date'      => Carbon::today()->toDateString(),
            'clock_in'  => '09:00:00',
            'clock_out' => '18:00:00',
        ]);


        $this->actingAs($user)
            ->get('/attendance')
            ->assertStatus(200)
            ->assertSee('退勤済');
    }
}
