@extends('layouts.app')

@section('title', '勤怠管理')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance.css') }}?v={{ time() }}">
@endsection

@include('components.user')

@section('content')

<div class="attendance-container">

    {{-- ステータス表示 --}}
    <div class="status-badge">
        {{ $status_label }}
    </div>

    {{-- 日付 --}}
    <div class="attendance-date">
        {{ $date->isoFormat('YYYY年M月D日（dd）') }}
    </div>

    {{-- 現在時刻 --}}
    <div class="attendance-time" id="current-time">
        {{ $time ?? now()->format('H:i') }}
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

    {{-- 勤務外 --}}
    @if (is_null($status))
    <form action="{{ route('attendance.start') }}" method="POST">
        @csrf
        <div class="attendance-button-wrapper">
            <button class="attendance-button">出勤</button>
        </div>
    </form>


    {{-- 出勤中 --}}
    @elseif ($status === 'working')
    <div class="attendanceend-button-wrapper">
        <form action="{{ route('user.attendance.end') }}" method="POST">
            @csrf
            <button class="attendanceend-button">退勤</button>
        </form>

        <form action="{{ route('attendance.breakIn') }}" method="POST">
            @csrf
            <button class="break-button">休憩入</button>
        </form>
    </div>

    {{-- 休憩中 --}}
    @elseif ($status === 'breaking')
    <form action="{{ route('attendance.breakOut') }}" method="POST">
        @csrf
        <div class="attendance-button-wrapper">
            <button class="attend-button">休憩戻</button>
        </div>
    </form>

    {{-- 退勤済み --}}
    @elseif ($status === 'finished')
    <p class="message">お疲れさまでした。</p>
    @endif

</div>

@endsection