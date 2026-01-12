<?php

namespace Tests\Feature\Attendance;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class UserAttendanceListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_only_logged_in_users_attendance_is_displayed()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2025-01-10',
        ]);

        Attendance::factory()->create([
            'user_id' => $otherUser->id,
            'date' => '2025-01-11',
        ]);

        $response = $this->actingAs($user)
            ->get(route('user.list', ['month' => '2025-01']));

        $response->assertSee('01/10');
        $response->assertDontSee('01/11');
    }


    /** @test */
    public function test_current_month_is_displayed_by_default()
    {
        Carbon::setTestNow('2025-01-15');

        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('user.list'));

        $response->assertStatus(200);
        $response->assertSee('2025/01');
    }

    /** @test */
    public function test_previous_month_is_displayed()
    {
        $user = User::factory()->create();

        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2024-12-20',
        ]);

        $response = $this->actingAs($user)
            ->get(route('user.list', ['month' => '2024-12']));

        $response->assertStatus(200);
        $response->assertSee('2024/12');
        $response->assertSee('12/20');
    }

    /** @test */
    public function test_next_month_is_displayed()
    {
        $user = User::factory()->create();

        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2025-02-05',
        ]);

        $response = $this->actingAs($user)
            ->get(route('user.list', ['month' => '2025-02']));

        $response->assertStatus(200);
        $response->assertSee('2025/02');
        $response->assertSee('02/05');
    }

    public function test_detail_button_navigates_to_detail_page()
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
}
