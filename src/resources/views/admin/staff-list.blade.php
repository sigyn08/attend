@extends('layouts.app')

@section('title', 'スタッフ別一覧画面')

@section('css')
<link rel="stylesheet" href="{{ asset('css/staff-list.css') }}?v={{ time() }}">
@endsection

@include('components.admin')

@section('content')
<div class="staff-list-container">

    <h1 class="title">{{ $user->name }}さんの勤怠</h1>

    {{-- 月切り替え --}}
    <div class="month-selector">
        <a class="month-btn prev-month"
            href="{{ request()->fullUrlWithQuery([
            'month' => \Carbon\Carbon::createFromFormat('Y-m', $month_param)
                        ->subMonth()
                        ->format('Y-m')
        ]) }}">
            ← 前月
        </a>

        <div class="current-month">
            <span class="calendar-icon">📅</span>
            <span>{{ $current_month }}</span>
        </div>

        <a class="month-btn next-month"
            href="{{ request()->fullUrlWithQuery([
            'month' => \Carbon\Carbon::createFromFormat('Y-m', $month_param)
                        ->addMonth()
                        ->format('Y-m')
        ]) }}">
            翌月 →
        </a>
    </div>


    {{-- 勤怠一覧 --}}
    <table class="staff-list-table">
        <thead>
            <tr>
                <th>日付</th>
                <th>出勤</th>
                <th>退勤</th>
                <th>休憩</th>
                <th>合計</th>
                <th>詳細</th>
            </tr>
        </thead>
        <tbody>
            @forelse($attendances as $attendance)
            <tr>
                <td>{{ $attendance->date->format('m/d(D)') }}</td>

                <td>
                    {{ $attendance->clock_in
        ? \Carbon\Carbon::parse($attendance->clock_in)->format('H:i')
        : '-' }}
                </td>

                <td>
                    {{ $attendance->clock_out
        ? \Carbon\Carbon::parse($attendance->clock_out)->format('H:i')
        : '-' }}
                </td>

                <td>
                    {{ gmdate('H:i', $attendance->total_break_minutes * 60) }}
                </td>

                <td>
                    @php
                    if ($attendance->clock_in && $attendance->clock_out) {
                    $workMinutes =
                    \Carbon\Carbon::parse($attendance->clock_in)
                    ->diffInMinutes($attendance->clock_out)
                    - $attendance->total_break_minutes;
                    echo gmdate('H:i', $workMinutes * 60);
                    }
                    @endphp
                </td>

                <td>
                    <a class="detail-link"
                        href="{{ route('admin.attendances.show', $attendance->id) }}">
                        詳細
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align:center;">
                    勤怠データがありません
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <a
        class="CSV_button"
        href="{{ route('admin.attendance.csv', $user->id) }}?month={{ $month_param }}">
        CSV出力
    </a>
</div>
@endsection