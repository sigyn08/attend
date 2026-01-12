@extends('layouts.app')

@section('title', '管理者勤怠詳細画面')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin-detail.css') }}?v={{ time() }}">
@endsection

@include('components.admin')

@section('content')
<div class="admin-detail-container">
    <h1 class="title">勤怠詳細</h1>

    <form method="POST"
        action="{{ route('admin.attendances.update', $attendance->id) }}"
        class="admin-detail-form">
        @csrf

        <table class="admin-detail-table">
            <tbody>
                <tr>
                    <th>名前</th>
                    <td>{{ $attendance->user->name }}</td>
                </tr>

                <tr>
                    <th>日付</th>
                    <td colspan="2">
                        {{ \Carbon\Carbon::parse($attendance->date)->format('Y年m月d日') }}
                    </td>
                </tr>

                <tr>
                    <th>出勤・退勤</th>
                    <td colspan="2">
                        <input type="time" name="clock_in"
                            value="{{ old('clock_in', \Carbon\Carbon::parse($attendance->clock_in)->format('H:i')) }}">
                        〜
                        <input type="time" name="clock_out"
                            value="{{ old('clock_out', \Carbon\Carbon::parse($attendance->clock_out)->format('H:i')) }}">

                        @error('clock_in')
                        <span class="error">{{ $message }}</span>
                        @enderror
                    </td>
                </tr>

                @foreach ($attendance->breakTimes as $index => $break)
                <tr>
                    <th>休憩{{ $index + 1 }}</th>
                    <td colspan="2">
                        <input type="time"
                            name="break_times[{{ $index }}][start_time]"
                            value="{{ old('break_times.'.$index.'.start_time', \Carbon\Carbon::parse($break->start_time)->format('H:i')) }}">
                        〜
                        <input type="time"
                            name="break_times[{{ $index }}][end_time]"
                            value="{{ old('break_times.'.$index.'.end_time', $break->end_time ? \Carbon\Carbon::parse($break->end_time)->format('H:i') : '') }}">
                        @error('break_times.'.$index.'.start_time') <span class="error">{{ $message }}</span> @enderror
                    </td>
                </tr>
                @endforeach

                <tr>
                    <th>備考</th>
                    <td colspan="2">
                        <textarea name="reason">{{ old('reason', $attendance->reason) }}</textarea>
                        @error('reason') <span class="error">{{ $message }}</span> @enderror
                    </td>
                </tr>
            </tbody>
        </table>

        <button type="submit" class="update_button">修正</button>
    </form>
</div>
@endsection