@extends('layouts.app')

@section('title', '勤怠詳細画面')

@section('css')
<link rel="stylesheet" href="{{ asset('css/detail.css') }}?v={{ time() }}">
@endsection
@include('components.user')

@section('content')
<div class="detail-container">

    <h1 class="title">勤怠詳細</h1>

    <form action="/attendance/detail/{{ $id ?? 1 }}" method="post">
        @csrf

        <div class="detail-card">

            <div class="detail-row">
                <div class="detail-label">名前</div>
                <div class="detail-value">西　怜奈</div>
            </div>

            <div class="detail-row">
                <div class="detail-label">日付</div>
                <div class="detail-value">2023年</div>
                <div class="detail-value">6月1日</div>
            </div>

            <div class="detail-row">
                <div class="detail-label">出勤・退勤</div>

                <input type="time" class="time-input" value="09:00">
                <span class="tilde">〜</span>
                <input type="time" class="time-input" value="18:00">
            </div>

            <div class="detail-row">
                <div class="detail-label">休憩</div>

                <input type="time" class="time-input" value="12:00">
                <span class="tilde">〜</span>
                <input type="time" class="time-input" value="13:00">
            </div>

            <div class="detail-row">
                <div class="detail-label">休憩２</div>

                <input type="time" class="time-input">
                <span class="tilde">〜</span>
                <input type="time" class="time-input">
            </div>

            <div class="detail-row">
                <div class="detail-label">備考</div>
                <textarea class="note-input">電車遅延のため</textarea>
            </div>

        </div>

        <button class="update-button">修正</button>

    </form>

</div>
@endsection