<?php

namespace Tests\Feature\Attendance;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;
use App\Models\User;

class DateTimeDisplayTest extends TestCase
{
    use RefreshDatabase;

    public function test_datetime_display_matches_current_time()
    {
        // テスト用ユーザー作成
        $user = User::factory()->create();

        // ログイン
        $this->actingAs($user);

        // 現在時刻を取得（Blade に渡される時刻と同じ形式）
        $now = Carbon::now()->format('H:i');

        // 勤怠打刻画面を取得
        $response = $this->get('/attendance');
        $response->assertStatus(200);

        // Blade で渡された初期値が画面にあるか確認
        $response->assertSeeText($now, false);
    }
}
