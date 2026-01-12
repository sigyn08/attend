@extends('layouts.app')

@section('title', '修正申請承認画面')

@section('css')
<link rel="stylesheet" href="{{ asset('css/approve.css') }}?v={{ time() }}">
@endsection

@section('content')

@include('components.admin')

<div class="approve-container">

    <h1 class="title">勤怠詳細</h1>

    <form
        method="POST"
        action="{{ route('admin.correction.approve', $correctionRequest->id) }}">
        @csrf

        <div class="approve-card">
            <table class="approve-table">
                <!-- 名前 -->
                <tr>
                    <th>名前</th>
                    <td>{{ $attendance->user->name }}</td>
                </tr>

                <!-- 日付 -->
                <tr>
                    <th>日付</th>
                    <td>{{ \Carbon\Carbon::parse($attendance->date)->format('Y年n月j日') }}</td>
                </tr>

                <!-- 出勤・退勤 -->
                <tr>
                    <th>出勤・退勤</th>
                    <td>
                        {{ $correctionRequest->clock_in
                            ? \Carbon\Carbon::parse($correctionRequest->clock_in)->format('H:i')
                            : \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') }}
                        〜
                        {{ $correctionRequest->clock_out
                            ? \Carbon\Carbon::parse($correctionRequest->clock_out)->format('H:i')
                            : \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') }}
                    </td>
                </tr>

                <!-- 休憩 -->
                @php
                // JSON文字列を配列に変換して安全に扱う
                if ($correctionRequest->break_times) {
                $breaks = is_string($correctionRequest->break_times)
                ? json_decode($correctionRequest->break_times, true)
                : $correctionRequest->break_times;
                $isArray = true;
                } else {
                $breaks = $attendance->breakTimes;
                $isArray = false;
                }
                @endphp

                @forelse ($breaks as $index => $break)
                <tr>
                    <th>休憩{{ $index + 1 }}</th>
                    <td>
                        @if ($isArray)
                        {{ isset($break['start_time']) ? \Carbon\Carbon::parse($break['start_time'])->format('H:i') : '—' }}
                        〜
                        {{ isset($break['end_time']) ? \Carbon\Carbon::parse($break['end_time'])->format('H:i') : '—' }}
                        @else
                        {{ $break->start_time ? \Carbon\Carbon::parse($break->start_time)->format('H:i') : '—' }}
                        〜
                        {{ $break->end_time ? \Carbon\Carbon::parse($break->end_time)->format('H:i') : '—' }}
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <th>休憩</th>
                    <td>—</td>
                </tr>
                @endforelse

                <!-- 備考 -->
                <tr>
                    <th>備考</th>
                    <td class="note">{{ $correctionRequest->reason ?? '—' }}</td>
                </tr>
            </table>
        </div>

        <button type="submit" class="approve_button">承認</button>
    </form>

</div>

@endsection