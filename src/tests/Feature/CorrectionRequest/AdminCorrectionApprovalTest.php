<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\StampCorrectionRequest;

class AdminCorrectionApprovalTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // is_admin = true を管理者とする設計
        $this->admin = User::factory()->create([
            'is_admin' => true,
        ]);
    }

    /** @test */
    public function test_pending_requests_are_displayed()
    {
        $user = User::factory()->create();

        StampCorrectionRequest::factory()->create([
            'user_id' => $user->id,
            'status' => 0, // pending
        ]);

        StampCorrectionRequest::factory()->create([
            'user_id' => $user->id,
            'status' => 1, // approved
        ]);

        $response = $this
            ->actingAs($this->admin)
            ->get('/admin/stamp_correction_request/list?status=pending');

        $response->assertStatus(200);
        $response->assertSee($user->name);
    }

    /** @test */
    public function test_approved_requests_are_displayed()
    {
        $user = User::factory()->create();

        StampCorrectionRequest::factory()->create([
            'user_id' => $user->id,
            'status' => 1, // approved
        ]);

        $response = $this
            ->actingAs($this->admin)
            ->get('/admin/stamp_correction_request/list?status=approved');

        $response->assertStatus(200);
        $response->assertSee($user->name);
    }

    /** @test */
    public function test_correction_request_detail_is_displayed()
    {
        $user = User::factory()->create();

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2025-01-01',
            'clock_in' => '09:00',
        ]);

        $request = StampCorrectionRequest::factory()->create([
            'attendance_id' => $attendance->id,
            'user_id' => $user->id,
            'clock_in' => '10:00',
            'reason' => '打刻漏れ',
            'status' => 0,
            'break_times' => [], // ← ★必須
        ]);

        $response = $this
            ->actingAs($this->admin)
            ->get("/admin/stamp_correction_request/{$request->id}");

        $response->assertStatus(200);
        $response->assertSee('打刻漏れ');
        $response->assertSee('10:00');
    }

    /** @test */
    public function test_correction_request_can_be_approved()
    {
        $user = User::factory()->create();

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'clock_in' => '09:00',
        ]);

        $request = StampCorrectionRequest::factory()->create([
            'attendance_id' => $attendance->id,
            'user_id' => $user->id,
            'clock_in' => '10:00',
            'status' => 0,
            'break_times' => [], 
        ]);

        $response = $this
            ->actingAs($this->admin)
            ->post("/admin/stamp_correction_request/{$request->id}/approve");

        $response->assertRedirect();

        $this->assertDatabaseHas('stamp_correction_requests', [
            'id' => $request->id,
            'status' => 1,
        ]);

        $this->assertDatabaseHas('attendances', [
            'id' => $attendance->id,
            'clock_in' => '10:00:00',
        ]);
    }
}
