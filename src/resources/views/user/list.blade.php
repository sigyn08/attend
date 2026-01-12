@extends('layouts.app')

@section('title', '勤怠一覧画面')

@section('css')
<link rel="stylesheet" href="{{ asset('css/list.css') }}?v={{ time() }}">
@endsection
@include('components.user')

@section('content')
<div class="user-list-container">

    <h1 class="title">勤怠一覧</h1>

    {{-- 月切り替え部分 --}}
    <div class="month-selector">

        {{-- 前月 --}}
        <a class="month-btn prev-month"
            href="{{ request()->fullUrlWithQuery([
            'month' => \Carbon\Carbon::createFromFormat('Y-m', $current_month_param)
                        ->subMonth()
                        ->format('Y-m')
        ]) }}">
            ← 前月
        </a>

        {{-- 現在の月 --}}
        <div class="current-month">

            {{-- 月選択アイコン --}}
            <label class="month-picker-label">
                📅
                <input
                    type="month"
                    class="month-picker"
                    value="{{ $current_month_param }}"
                    onchange="changeMonth(this.value)">
            </label>

            {{-- 表示用年月 --}}
            <span>{{ $current_month }}</span>
        </div>

        {{-- 翌月 --}}
        <a class="month-btn next-month"
            href="{{ request()->fullUrlWithQuery([
            'month' => \Carbon\Carbon::createFromFormat('Y-m', $current_month_param)
                        ->addMonth()
                        ->format('Y-m')
        ]) }}">
            翌月 →
        </a>

    </div>

    {{-- 勤怠一覧テーブル --}}
    <table class="user-list-table">
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
            @foreach ($attendances as $attendance)
            <tr>
                {{-- 日付 --}}
                <td>{{ \Carbon\Carbon::parse($attendance->date)
                    ->locale('ja')->isoFormat('MM/DD(ddd)') }}</td>

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


                {{-- 休憩合計 --}}
                <td>
                    @php
                    $b = $attendance->total_break_minutes;
                    @endphp
                    {{ $b ? floor($b / 60) . ':' . str_pad($b % 60, 2, '0', STR_PAD_LEFT) : '0:00' }}
                </td>

                {{-- 勤務合計 --}}
                <td>
                    @php
                    $w = $attendance->total_work_minutes;
                    @endphp
                    {{ $w ? floor($w / 60) . ':' . str_pad($w % 60, 2, '0', STR_PAD_LEFT) : '0:00' }}
                </td>

                {{-- 詳細リンク --}}
                <td>
                    <a class="detail-link" href="{{ route('attendance.show', $attendance->id) }}">
                        詳細
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

</div>
@endsection