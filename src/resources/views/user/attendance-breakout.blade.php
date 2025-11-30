@extends('layouts.app')

@section('title', '勤怠管理')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance.css') }}?v={{ time() }}">
@endsection
@include('components.user')

@section('content')

<form action="/attendance/breakout" method="post">
    @csrf
    <div class="attendanceend-container">

        {{-- ステータス表示 --}}
        <div class="status-badge">
            休憩中
        </div>

        {{-- 日付 --}}
        <div class="attendance-date">
            {{ $date }}
        </div>

        {{-- 現在時刻 --}}
        <div class="attendance-time" id="current-time">
            {{ $time }}
        </div>

        {{-- 出勤・退勤 ボタン --}}
        <div class="attendance-button-wrapper">
            <button class="attend-button">休憩戻</button>
        </div>
</form>
</div>

{{-- 時刻更新 --}}
<script>
    function updateClock() {
        const now = new Date();
        const time = now.toLocaleTimeString('ja-JP', {
            hour: '2-digit',
            minute: '2-digit'
        });
        document.getElementById('current-time').textContent = time;
    }
    setInterval(updateClock, 1000);
</script>

@endsection