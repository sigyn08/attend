@extends('layouts.app')

@section('title', '管理者勤怠一覧画面')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin-list.css') }}?v={{ time() }}">
@endsection

@include('components.admin')

@section('content')
<div class="admin-list-container">
    <h1 class="title">{{ $formattedDate }}の勤怠一覧</h1>
    <div class="month-selector">
        <a href="{{ route('admin.attendance.list', ['date' => now()->subDay()->toDateString()]) }}" class="select-date">
            ← 前日
        </a>
        <div class="calendar-wrapper">
            <form method="GET" action="{{ url()->current() }}">
                <label class="calendar-label">
                    <input
                        type="date"
                        name="date"
                        value="{{ request('date', now()->toDateString()) }}"
                        onchange="this.form.submit()"
                        class="calendar-input">
                </label>
            </form>
        </div>
        <a href="{{ route('admin.attendance.list', ['date' => now()->addDay()->toDateString()]) }}" class="select-date">
            翌日 →
        </a>
    </div>

    <table class="admin-list-table">
        <thead>
            <tr>
                <th>名前</th>
                <th>出勤</th>
                <th>退勤</th>
                <th>休憩</th>
                <th>合計</th>
                <th>詳細</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
            @php
            $attendance = $user->attendances->first();
            @endphp

            <tr>
                <td>{{ $user->name }}</td>

                <td>
                    {{ $attendance?->clock_in
                ? \Carbon\Carbon::parse($attendance->clock_in)->format('H:i')
                : '-' }}
                </td>

                <td>
                    {{ $attendance?->clock_out
                ? \Carbon\Carbon::parse($attendance->clock_out)->format('H:i')
                : '-' }}
                </td>

                <td>
                    {{ $attendance
                ? gmdate('H:i', $attendance->total_break_minutes * 60)
                : '-' }}
                </td>

                <td>
                    {{ $attendance
                ? gmdate('H:i', $attendance->total_work_minutes * 60)
                : '-' }}
                </td>

                <td>
                    @if ($attendance)
                    <a href="{{ route('admin.attendances.show', $attendance->id) }}">
                        詳細
                    </a>
                    @else
                    -
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection