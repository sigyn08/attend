@extends('layouts.app')

<!-- タイトル -->
@section('title', '申請一覧画面')

<!-- css読み込み -->
@section('css')
<link rel="stylesheet" href="{{ asset('css/admin-request.css') }}">
@endsection

<!-- メインコンテンツ -->
@section('content')

@include('components.admin')
<div class="admin-request-container">
    <h1 class="title">申請一覧</h1>
    <div class="approval">
        <ul class="approval_select">
            <li><a href="approval_wait">承認待ち</a></li>
            <li><a href="approval_done">承認済み</a></li>
        </ul>
    </div>
    <table class="admin-request-table">
        <thead>
            <tr>
                <th>状態</th>
                <th>名前</th>
                <th>対象日時</th>
                <th>申請理由</th>
                <th>申請日時</th>
                <th>詳細</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td><a href="/stamp_correction_request/approve/{attendance_correct_request_id}">詳細</a></td>
            </tr>
        </tbody>
    </table>
</div>
@endsection