@extends('layouts.app')

<!-- タイトル -->
@section('title', '修正申請承認画面')

<!-- css読み込み -->
@section('css')
<link rel="stylesheet" href="{{ asset('css/approve.css') }}?v={{ time() }}">
@endsection

<!-- メインコンテンツ -->
@section('content')

@include('components.admin')
<div class="approve-container">
    <h1 class="title">勤怠詳細</h1>
    <table class="approve-table">
        <tr>
            <th>名前</th>
            <td></td>
        </tr>
        <tr>
            <th>日付</th>
            <td></td>
        </tr>
        <tr>
            <th>出勤・退勤</th>
            <td></td>
        </tr>
        <tr>
            <th>休憩</th>
            <td></td>
        </tr>
        <tr>
            <th>休憩２</th>
            <td></td>
        </tr>
        <tr>
            <th>備考</th>
            <td></td>
        </tr>
    </table>
    <button class="approve_button">承認</button>
</div>
@endsection