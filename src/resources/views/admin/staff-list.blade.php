@extends('layouts.app')

<!-- タイトル -->
@section('title', 'スタッフ一覧画面')

<!-- css読み込み -->
@section('css')
<link rel="stylesheet" href="{{ asset('css/staff-list.css') }}?v={{ time() }}">
@endsection

@include('components.admin')

<!-- メインコンテンツ -->
@section('content')
<div class="staff-list-container">

    <h1 class="title">西玲奈さんの勤怠</h1>

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
    <button class="CSV_button">CSV出力</button>
</div>
@endsection