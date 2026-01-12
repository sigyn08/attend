<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class AdminAttendanceListTest extends TestCase
{
    use RefreshDatabase;

    protected function adminLogin()
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $this->actingAs($admin);
    }

    /** @test */
    public function test_all_users_attendance_is_displayed()
    {
        Carbon::setTestNow('2025-01-10');

        $this->adminLogin();

        $users = User::factory()->count(3)->create();

        foreach ($users as $user) {
            Attendance::factory()->create([
                'user_id' => $user->id,
                'date'    => Carbon::today(), 
            ]);
        }

        $response = $this->get(route('admin.attendance.list'));

        $response->assertStatus(200);

        foreach ($users as $user) {
            $response->assertSee($user->name);
        }
    }

    /** @test */
    public function test_today_is_displayed_by_default()
    {
        Carbon::setTestNow('2025-01-10');

        $this->adminLogin();

        $response = $this->get(route('admin.attendance.list'));

        $response->assertStatus(200)
            ->assertSee('2025-01-10');
    }

    /** @test */
    public function test_previous_day_is_displayed()
    {
        Carbon::setTestNow('2025-01-10');

        $this->adminLogin();

        Attendance::factory()->create([
            'date' => Carbon::yesterday(), 
        ]);

        $response = $this->get(route('admin.attendance.list', [
            'date' => Carbon::yesterday()->format('Y-m-d'),
        ]));

        $response->assertStatus(200)
            ->assertSee('2025-01-09');
    }

    /** @test */
    public function test_next_day_is_displayed()
    {
        Carbon::setTestNow('2025-01-10');

        $this->adminLogin();

        Attendance::factory()->create([
            'date' => Carbon::tomorrow(), // ← work_date → date
        ]);

        $response = $this->get(route('admin.attendance.list', [
            'date' => Carbon::tomorrow()->format('Y-m-d'),
        ]));

        $response->assertStatus(200)
            ->assertSee('2025-01-11');
    }
}
