<?php

namespace Tests\Feature\Attendance;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class ClockOutTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 退勤ボタンが正しく機能する
     *
     * @return void
     */
    public function test_user_can_clock_out()
    {
        // 1. 出勤中のユーザーを作成
        $user = User::factory()->create([]);
        $this->actingAs($user);

        // 2. 出勤済みの勤務を作成
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => Carbon::today()->format('Y-m-d'),
            'clock_in' => '09:00:00',
            'clock_out' => null,
        ]);

        // 3. 退勤処理をPOST
        $response = $this->post(route('user.attendance.end'), [
            'attendance_id' => $attendance->id,
        ]);

        $response->assertStatus(302); // リダイレクト想定
        $response->assertSessionHasNoErrors();

        // DBの退勤時刻が更新されていることを確認
        $this->assertDatabaseHas('attendances', [
            'id' => $attendance->id,
            'clock_out' => now()->format('H:i:s'),
        ]);
    }

    /**
     * 退勤時刻が勤怠一覧画面で確認できる
     *
     * @return void
     */
    public function test_clock_out_time_is_displayed_in_attendance_list()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => Carbon::today()->format('Y-m-d'),
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
        ]);

        $response = $this->get(route('user.list'));

        $response->assertStatus(200);
        $response->assertSee('18:00'); // 退勤時刻が画面に表示される
    }
}
