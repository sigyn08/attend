@extends('layouts.app')

<!-- タイトル -->
@section('title', 'ログイン画面')

<!-- css読み込み -->
@section('css')
<link rel="stylesheet" href="{{ asset('css/login.css') }}?v={{ time() }}">
@endsection
@include('components.header')

<!-- メインコンテンツ -->
@section('content')
<div class="login-container">
    <h1 class="title">ログイン</h1>
    <form method="POST" action="/login" class="login-form">
        @csrf
        <div class="form-group">
            <label for="mail" class="form-label">メールアドレス</label>
            <input type="email" name="email" id="mail" class="form-input" value="{{ old('email') }}">
            <div class="form-error">
                @error('email')
                {{ $message }}
                @enderror
            </div>
            <label for="password" class="form-label">パスワード</label>
            <input type="password" name="password" id="password" class="form-input">
            <div class="form-error">
                @error('password')
                {{ $message }}
                @enderror
            </div>
            <button class="login-button">ログインする</button>
            <a href="/register">会員登録はこちら</a>
        </div>
    </form>
</div>
@endsection