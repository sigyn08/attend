@extends('layouts.app')

@section('title', '勤怠詳細画面')

@php
// 表示に使うデータを切り替え
$displayClockIn = $pendingRequest?->clock_in ?? $attendance->clock_in;
$displayClockOut = $pendingRequest?->clock_out ?? $attendance->clock_out;

$displayBreaks = $pendingRequest
? $pendingRequest->break_times
: $attendance->breakTimes;
@endphp

@section('css')
<link rel="stylesheet" href="{{ asset('css/detail.css') }}?v={{ time() }}">
@endsection
@include('components.user')

@section('content')
<div class="detail-container">

    <h1 class="title">勤怠詳細</h1>

    {{-- 正しいルートを使う --}}
    <form action="{{ route('attendance.submit_correction_request', $attendance->id) }}" method="post">
        @csrf

        <div class="detail-card">

            {{-- 名前 --}}
            <div class="detail-row">
                <div class="detail-label">名前</div>
                <div class="detail-value">{{ $attendance->user->name ?? '不明' }}
                </div>
            </div>

            {{-- 日付 --}}
            <div class="detail-row">
                <div class="detail-label">日付</div>
                <div class="detail-value">
                    {{ $attendance->date->locale('ja')->isoFormat('Y年M月D日 ') }}
                </div>
            </div>

            {{-- 出勤・退勤 --}}
            <div class="detail-row">
                <div class="detail-label">出勤・退勤</div>

                <input type="time"
                    name="clock_in"
                    value="{{ $displayClockIn ? \Carbon\Carbon::parse($displayClockIn)->format('H:i') : '' }}"
                    {{ $pendingRequest ? 'disabled' : '' }}>
                <span class="tilde">〜</span>

                <input type="time"
                    name="clock_out"
                    value="{{ $displayClockOut ? \Carbon\Carbon::parse($displayClockOut)->format('H:i') : '' }}"
                    {{ $pendingRequest ? 'disabled' : '' }}>
                @error('clock_in')
                <p class="text-red">{{ $message }}</p>
                @enderror
                @error('clock_out')
                <p class="text-red">{{ $message }}</p>
                @enderror

            </div>

            {{-- 休憩時間（複数） --}}
            <div class="detail-row">
                <div class="detail-label">休憩</div>

                <div class="break-list">
                    @foreach ($displayBreaks ?? [] as $index => $break)
                    <div class="break-item">
                        <input type="time"
                            name="break_start[{{ $index }}]"
                            value="{{ 
                is_array($break)
                    ? \Carbon\Carbon::parse($break['start_time'])->format('H:i')
                    : \Carbon\Carbon::parse($break->start_time)->format('H:i')
            }}"
                            {{ $pendingRequest ? 'disabled' : '' }}>

                        <span class="tilde">〜</span>

                        <input type="time"
                            name="break_end[{{ $index }}]"
                            value="{{ 
                is_array($break)
                    ? \Carbon\Carbon::parse($break['end_time'])->format('H:i')
                    : \Carbon\Carbon::parse($break->end_time)->format('H:i')
            }}"
                            {{ $pendingRequest ? 'disabled' : '' }}>
                    </div>

                    {{-- ★ ここに出す --}}
                    @error("break_start.$index")
                    <p class="text-red">{{ $message }}</p>
                    @enderror

                    @error("break_end.$index")
                    <p class="text-red">{{ $message }}</p>
                    @enderror
                    @endforeach
                </div>



            </div>

            {{-- 備考（修正依頼理由） --}}
            <div class="detail-row">
                <div class="detail-label">備考</div>
                <textarea class="note-input"
                    name="reason"
                    placeholder="修正理由を入力してください" {{ $pendingRequest ? 'disabled' : '' }}>{{ old('reason', $pendingRequest?->reason) }}</textarea>
                @error('reason')
                <p class="text-red">{{ $message }}</p>
                @enderror
            </div>

        </div>
        @if ($pendingRequest)
        <p class="text-red">
            ＊承認待ちのため修正はできません。
        </p>
        @else
        <button type="submit" class="update-button">
            修正
        </button>
        @endif

    </form>

</div>
@endsection