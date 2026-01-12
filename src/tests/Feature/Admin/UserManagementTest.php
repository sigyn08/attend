<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($this->admin);
    }

    /** @test */
    public function test_admin_can_view_all_users()
    {
        $users = User::factory()->count(3)->create(['is_admin' => false]);

        $response = $this->get(route('admin.staff.list'));
        $response->assertStatus(200);

        foreach ($users as $user) {
            $response->assertSee($user->name);
            $response->assertSee($user->email);
        }
    }

    /** @test */
    public function test_admin_can_view_users_attendance()
    {
        $user = User::factory()->create();
        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => Carbon::today(),
        ]);

        // ※ route名が無いのでURL直指定
        $response = $this->get("/admin/attendance/staff/{$user->id}");

        $response->assertStatus(200);
        $response->assertSee($user->name);
    }

    /** @test */
    public function test_previous_month_is_displayed()
    {
        Carbon::setTestNow('2026-01-15');

        $admin = User::factory()->create([
            'is_admin' => 1,
        ]);
        $user = User::factory()->create();

        $this->actingAs($admin)
            ->get("/admin/attendance/staff/{$user->id}?month=2026-01")
            ->assertStatus(200)
            ->assertSee('2026/01')
            ->assertSee('month=2025-12');
    }

    /** @test */
    public function test_next_month_is_displayed()
    {
        Carbon::setTestNow('2026-01-15');

        $admin = User::factory()->create(['is_admin' => 1]);
        $user  = User::factory()->create();

        $this->actingAs($admin)
            ->get("/admin/attendance/staff/{$user->id}?month=2026-01")
            ->assertStatus(200)
            ->assertSee('2026/01') // 現在月
            ->assertSee('month=2026-02'); // 翌月リンク
    }


    /** @test */
    public function test_detail_button_navigates_to_attendance_detail()
    {
        $admin = User::factory()->create(['is_admin' => 1]);
        $user  = User::factory()->create();
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2026-01-10',
        ]);

        $this->actingAs($admin)
            ->get("/admin/attendance/staff/{$user->id}")
            ->assertSee(
                route('admin.attendances.show', $attendance->id)
            );
    }
}
