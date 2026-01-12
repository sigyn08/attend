<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Carbon\Carbon;

class ExportController extends Controller
{
    public function export(User $user)
    {
        // month パラメータ（例: 2025-01）
        $monthParam = request('month', now()->format('Y-m'));

        try {
            $month = Carbon::createFromFormat('Y-m', $monthParam);
        } catch (\Exception $e) {
            $month = now();
        }

        $start = $month->copy()->startOfMonth()->toDateString();
        $end   = $month->copy()->endOfMonth()->toDateString();

        $attendances = $user->attendances()
            ->whereBetween('date', [$start, $end])
            ->with('breakTimes')
            ->orderBy('date')
            ->get();

        $response = new StreamedResponse(function () use ($attendances) {
            $handle = fopen('php://output', 'w');

            // ヘッダー（SJIS）
            fputcsv($handle, array_map(
                fn($v) => mb_convert_encoding($v, 'SJIS-win', 'UTF-8'),
                ['日付', '出勤', '退勤', '休憩時間(分)', '労働時間(分)']
            ));

            foreach ($attendances as $attendance) {
                $workMinutes = null;

                if ($attendance->clock_in && $attendance->clock_out) {
                    $workMinutes =
                        Carbon::parse($attendance->clock_in)
                        ->diffInMinutes($attendance->clock_out)
                        - $attendance->total_break_minutes;
                }

                $row = [
                    $attendance->date->format('Y-m-d'),
                    $attendance->clock_in,
                    $attendance->clock_out,
                    $attendance->total_break_minutes,
                    $workMinutes,
                ];

                fputcsv($handle, array_map(
                    fn($v) => mb_convert_encoding($v, 'SJIS-win', 'UTF-8'),
                    $row
                ));
            }

            fclose($handle);
        });

        $filename = "{$user->name}_{$month->format('Y-m')}_attendance.csv";

        $response->headers->set('Content-Type', 'text/csv; charset=SJIS-win');
        $response->headers->set(
            'Content-Disposition',
            "attachment; filename=\"{$filename}\""
        );

        return $response;
    }
}
