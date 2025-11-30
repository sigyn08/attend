@extends('layouts.app')

<!-- タイトル -->
@section('title', '管理者ログイン')

<!-- css読み込み -->
@section('css')
<link rel="stylesheet" href="{{ asset('css/admin-login.css') }}?v={{ time() }}">
@endsection

@include('components.auth')

<!-- メインコンテンツ -->
@section('content')

<form method="POST" action="/admin/login" class="admin-login-form">
    @csrf
    <h1 class="title">管理者ログイン</h1>
    <label for="mail" class="entry__name">メールアドレス</label>
    <input type="email" name="email" id="mail" class="entry__input" value="{{ old('email') }}">
    <div class="form__error">
        @error('email')
        {{ $message }}
        @enderror
    </div>
    <label for="password" class="entry__name">パスワード</label>
    <input type="password" name="password" id="password" class="entry__input">
    <div class="form__error">
        @error('password')
        {{ $message }}
        @enderror
    </div>
    <button class="entry__button">管理者ログインする</button>
</form>
@endsection