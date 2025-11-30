@extends('layouts.app')

<!-- タイトル -->
@section('title', '管理者勤怠詳細画面')

<!-- css読み込み -->
@section('css')
<link rel="stylesheet" href="{{ asset('css/admin-detail.css') }}?v={{ time() }}">
@endsection

<!-- メインコンテンツ -->
@section('content')

<div class="admin-detail-container">
    <h1 class="title">勤怠詳細</h1>
    <form method="POST" action="admin/attendance/{id}" class="admin-detail-form">
        <table class="admin-detail-table">
            <thead>
                <tr>
                    <th>名前</th>
                    <td>山田　太郎</td>
                </tr>
                <tr>
                    <th>日付</th>
                    <td>2023年</td>
                    <td>6月1日</td>
                </tr>
                <tr>
                    <th>出勤・退勤</th>
                    <td>09:00 ~ 20:00</td>
                </tr>
                <tr>
                    <th>休憩</th>
                    <td>12:00 ~ 13:00</td>
                </tr>
                <tr>
                    <th>休憩２</th>
                    <td> ~ </td>
                </tr>
                <tr>
                    <th>備考</th>
                    <td></td>
                </tr>
            </thead>
        </table>
    </form>
    <a href="{{ route('admin.attendance.list') }}" class="update_button">修正</a>

</div>
@endsection