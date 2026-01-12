<?php

namespace Tests\Feature\AttendanceDetail;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class AdminAttendanceDetailTest extends TestCase
{
    use RefreshDatabase;
    

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($this->admin);
    }

    /** @test */
    public function test_detail_data_is_correct()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create([
            'user_id'   => $user->id,
            'date'      => '2025-01-10',
            'clock_in'  => '09:00',
            'clock_out' => '18:00',
        ]);

        $response = $this->get(route('admin.attendances.show', $attendance));
        $response->assertStatus(200);
        $response->assertSee('09:00');
        $response->assertSee('18:00');
    }

    /** @test */
    public function test_invalid_clock_in_time_is_rejected()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create([
            'user_id'   => $user->id,
            'date'      => Carbon::today(),
            'clock_in'  => '09:00',
            'clock_out' => '18:00',
        ]);

        $response = $this->post(route('admin.attendances.update', $attendance), [
            'clock_in'  => '19:00',
            'clock_out' => '18:00',
            'break_times' => [],
            'reason'    => '修正理由',
        ]);

        $response->assertSessionHasErrors('clock_in');
    }

    /** @test */
    public function test_invalid_break_start_time_is_rejected()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create([
            'user_id'   => $user->id,
            'date'      => Carbon::today(),
            'clock_in'  => '09:00',
            'clock_out' => '18:00',
        ]);

        

        $response = $this->post(route('admin.attendances.update', $attendance), [
            'clock_in'  => '09:00',
            'clock_out' => '18:00',
            'break_times' => [['start_time' => '19:00', 'end_time' => '19:30']],
            'reason'    => '修正理由',
        ]);

        $response->assertSessionHasErrors('break_times.0.start_time');
    }

    /** @test */
    public function test_invalid_break_end_time_is_rejected()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create([
            'user_id'   => $user->id,
            'date'      => Carbon::today(),
            'clock_in'  => '09:00',
            'clock_out' => '18:00',
        ]);

        $response = $this->post(route('admin.attendances.update', $attendance), [
            'clock_in'  => '09:00',
            'clock_out' => '18:00',
            'break_times' => [['start_time' => '12:00', 'end_time' => '19:00']],
            'reason'    => '修正理由',
        ]);

        $response->assertSessionHasErrors('break_times.0.end_time');
    }

    /** @test */
    public function test_reason_is_required()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create([
            'user_id'   => $user->id,
            'date'      => Carbon::today(),
            'clock_in'  => '09:00',
            'clock_out' => '18:00',
        ]);

        $response = $this->post(route('admin.attendances.update', $attendance), [
            'clock_in'  => '09:00',
            'clock_out' => '18:00',
            'break_times' => [],
            'reason'    => '',
        ]);

        $response->assertSessionHasErrors('reason');
    }
}
