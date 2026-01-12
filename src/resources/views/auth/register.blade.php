@extends('layouts.app')

<!-- タイトル -->
@section('title', '会員登録画面')

<!-- css読み込み -->
@section('css')
<link rel="stylesheet" href="{{ asset('css/register.css') }}?v={{ time() }}">
@endsection
@include('components.header')

<!-- メインコンテンツ -->
@section('content')

<div class="register-container">
    <h1 class="title">会員登録</h1>
    <form method="POST" action="/register" class="register-form">
        @csrf
        <div class="form-group">
            <label for="name" class="form-label">名前</label>
            <input type="text" name="name" id="name" class="form-input" value="{{ old('name') }}">
            <div class="form-error">
                @error('name')
                {{ $message }}
                @enderror
            </div>
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
            <label for="password_confirmation" class="form-label">パスワード確認</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="form-input">
            <div class="form-error">
                @error('password_confirmation')
                {{ $message }}
                @enderror
            </div>
            <button class="register-button">登録する</button>
            <a href="/login">ログインはこちら</a>
        </div>
    </form>
</div>
@endsection