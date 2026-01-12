<?php

namespace Tests\Feature\Attendance;

use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class ClockInTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ゲストは出勤できずログイン画面にリダイレクトされる()
    {
        $response = $this->post(route('attendance.start'));

        $response->assertRedirect(route('user-login'));
    }

    /** @test */
    public function ログインユーザーは出勤できる()
    {
        Carbon::setTestNow('2025-01-01 09:00:00');

        $user = User::factory()->create();

        $this->actingAs($user)
            ->followingRedirects()
            ->post(route('attendance.start'))
            ->assertStatus(200);

        $this->assertDatabaseHas('attendances', [
            'user_id'  => $user->id,
            'date'     => '2025-01-01',
            'clock_in' => '09:00:00',
        ]);
    }

    /** @test */
    public function 同じ日に2回出勤しても勤怠は1件のみ()
    {
        Carbon::setTestNow('2025-01-01 09:00:00');

        $user = User::factory()->create();
        $this->actingAs($user);

        Attendance::create([
            'user_id'  => $user->id,
            'date'     => '2025-01-01',
            'clock_in' => '08:50:00',
        ]);

        $this->post(route('attendance.start'));

        $this->assertEquals(1, Attendance::count());
    }

    /** @test */
    public function 日付が変われば再度出勤できる()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Carbon::setTestNow('2025-01-01 09:00:00');
        $this->post(route('attendance.start'));

        Carbon::setTestNow('2025-01-02 09:00:00');
        $this->post(route('attendance.start'));

        $this->assertDatabaseHas('attendances', [
            'user_id' => $user->id,
            'date'    => '2025-01-02',
        ]);
    }
}
