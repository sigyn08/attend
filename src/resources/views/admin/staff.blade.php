@extends('layouts.app')

<!-- タイトル -->
@section('title', 'スタッフ一覧画面')

<!-- css読み込み -->
@section('css')
<link rel="stylesheet" href="{{ asset('css/staff.css') }}">
@endsection

@include('components.admin')

<!-- メインコンテンツ -->
@section('content')
<div class="staff-container">
    <h1 class="title">スタッフ一覧</h1>
    <table class="staff-table">
        <thead>
            <tr>
                <th>名前</th>
                <th>メールアドレス</th>
                <th>月次勤怠</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td></td>
                <td></td>
                <td><a href="/admin/attendance/staff/{id}">詳細</a></td>
            </tr>
        </tbody>
    </table>
</div>
@endsection