@extends('layouts.app')

<!-- タイトル -->
@section('title', '申請一覧画面')

<!-- css読み込み　-->
@section('css')
<link rel="stylesheet" href="{{ asset('css/request.css') }}?v={{ time()}}">
@endsection
@include('components.user')

<!-- メインコンテンツ　-->
@section('content')
<div class="request-container">
    <h1 class="title">申請一覧</h1>
    <div class="border">
        <ul class="border_list">
            <li><a href="request?page=wait">承認待ち</a></li>
            <li><a href="request?page=complete">承認済み</a></li>
        </ul>
    </div>
    <table class="request-table">
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
                <td><a href="/attendance/detail/{id}">詳細</a></td>
            </tr>
        </tbody>
    </table>
</div>
@endsection