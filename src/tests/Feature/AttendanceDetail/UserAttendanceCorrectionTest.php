<?php

namespace Tests\Feature\AttendanceCorrection;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\StampCorrectionRequest;
use Carbon\Carbon;

class UserAttendanceCorrectionTest extends TestCase
{
    use RefreshDatabase;

    private function createAttendanceWithUser()
    {
        $user = User::factory()->create();

        $attendance = Attendance::factory()->create([
            'user_id'   => $user->id,
            'date'      => '2026-01-01',
            'clock_in'  => '09:00:00',
            'clock_out' => '18:00:00',
        ]);

        BreakTime::factory()->create([
            'attendance_id' => $attendance->id,
            'start_time'    => '12:00:00',
            'end_time'      => '13:00:00',
        ]);

        return [$user, $attendance];
    }

    /** @test */
    public function test_clock_in_after_clock_out_is_invalid()
    {
        [$user, $attendance] = $this->createAttendanceWithUser();

        $response = $this->actingAs($user)->post(
            route('attendance.submit_correction_request', $attendance->id),
            [
                'clock_in'  => '19:00',
                'clock_out' => '18:00',
                'reason'    => 'テスト',
            ]
        );

        $response->assertSessionHasErrors([
            'clock_out' => '出勤時間もしくは退勤時間が不適切な値です',
        ]);
    }

    /** @test */
    public function test_break_start_after_clock_out_is_invalid()
    {
        [$user, $attendance] = $this->createAttendanceWithUser();

        $response = $this->actingAs($user)->post(
            route('attendance.submit_correction_request', $attendance->id),
            [
                'clock_in'    => '09:00',
                'clock_out'   => '18:00',
                'break_start' => ['19:00'],
                'break_end'   => ['19:30'],
                'reason'      => 'テスト',
            ]
        );

        $response->assertSessionHasErrors([
            'break_start.0' => '休憩時間が不適切な値です',
        ]);
    }

    /** @test */
    public function test_break_end_after_clock_out_is_invalid()
    {
        [$user, $attendance] = $this->createAttendanceWithUser();

        $response = $this->actingAs($user)->post(
            route('attendance.submit_correction_request', $attendance->id),
            [
                'clock_in'    => '09:00',
                'clock_out'   => '18:00',
                'break_start' => ['17:30'],
                'break_end'   => ['18:30'],
                'reason'      => 'テスト',
            ]
        );

        $response->assertSessionHasErrors([
            'break_end.0' => '休憩時間もしくは退勤時間が不適切な値です',
        ]);
    }

    /** @test */
    public function test_reason_is_required()
    {
        [$user, $attendance] = $this->createAttendanceWithUser();

        $response = $this->actingAs($user)->post(
            route('attendance.submit_correction_request', $attendance->id),
            [
                'clock_in'  => '09:00',
                'clock_out' => '18:00',
                'reason'    => '',
            ]
        );

        $response->assertSessionHasErrors([
            'reason' => '備考を記入してください',
        ]);
    }

    /** @test */
    public function test_correction_request_is_created()
    {
        [$user, $attendance] = $this->createAttendanceWithUser();

        $this->actingAs($user)->post(
            route('attendance.submit_correction_request', $attendance->id),
            [
                'clock_in'  => '10:00',
                'clock_out' => '19:00',
                'reason'    => '修正申請テスト',
            ]
        );

        $this->assertDatabaseHas('stamp_correction_requests', [
            'attendance_id' => $attendance->id,
            'user_id'       => $user->id,
            'status'        => '0',
            'reason'        => '修正申請テスト',
        ]);
    }

    /** @test */
    public function test_pending_requests_are_displayed()
    {
        [$user, $attendance] = $this->createAttendanceWithUser();

        StampCorrectionRequest::factory()->create([
            'attendance_id' => $attendance->id,
            'user_id'       => $user->id,
            'status'        => '0',
        ]);

        $response = $this->actingAs($user)->get(
            route('user.correction.list', ['status' => '0'])
        );

        $response->assertStatus(200)
            ->assertSee('承認待ち');
    }

    /** @test */
    public function test_approved_requests_are_displayed()
    {
        [$user, $attendance] = $this->createAttendanceWithUser();

        StampCorrectionRequest::factory()->create([
            'attendance_id' => $attendance->id,
            'user_id'       => $user->id,
            'status'        => '1',
        ]);

        $response = $this->actingAs($user)->get(
            route('user.correction.list', ['status' => '1'])
        );

        $response->assertStatus(200)
            ->assertSee('承認済み');
    }

    /** @test */
    public function test_detail_button_navigates_to_detail_page()
    {
        [$user, $attendance] = $this->createAttendanceWithUser();

        $request = StampCorrectionRequest::factory()->create([
            'attendance_id' => $attendance->id,
            'user_id'       => $user->id,
        ]);

        $response = $this->actingAs($user)->get(
            route('attendance.show', $attendance->id)
        );

        $response->assertStatus(200)
            ->assertSee('勤怠詳細');
    }
}
