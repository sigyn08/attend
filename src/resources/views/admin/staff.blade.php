@extends('layouts.app')

@section('title', 'スタッフ一覧画面')

@section('css')
<link rel="stylesheet" href="{{ asset('css/staff.css') }}">
@endsection

@include('components.admin')

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
            @forelse($users as $user)
            <tr>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                    <a href="{{ url('/admin/attendance/staff/' . $user->id) }}">
                        詳細
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="3" style="text-align:center;">
                    スタッフがいません
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection