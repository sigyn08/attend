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
        <button class="month-btn prev-month">← 前月</button>

        <div class="current-month">
            <span class="calendar-icon">📅</span>
            <span>{{ $current_month ?? '2023/06' }}</span>
        </div>

        <button class="month-btn next-month">翌月 →</button>
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
            <tr>
                <td>06/01(木)</td>
                <td>09:00</td>
                <td>18:00</td>
                <td>1:00</td>
                <td>8:00</td>
                <td><a class="detail-link" href="/attendance/detail/{id}">詳細</a></td>
            </tr>
        </tbody>
    </table>

</div>
@endsection