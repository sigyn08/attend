@extends('layouts.app')

@section('title', '管理者勤怠一覧画面')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin-list.css') }}?v={{ time() }}">
@endsection

@include('components.admin')

@section('content')
<div class="admin-list-container">
    <h1 class="title">2025年11月24日の勤怠一覧</h1>
    <div class="month-selector">
        <button class="month-btn prev-month">← 前月</button>

        <div class="current-month">
            <span class="calendar-icon">📅</span>
            <span>{{ $current_month ?? '2023/06/01' }}</span>
        </div>

        <button class="month-btn next-month">翌月 →</button>
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
            <tr>
                <td>山田　太郎</td>
                <td>09:00</td>
                <td>18:00</td>
                <td>1:00</td>
                <td>8:00</td>
                <td>
                    <a href="/admin/attendance/{id}">詳細</a>
                </td>
            </tr>
        </tbody>
    </table>
</div>
@endsection