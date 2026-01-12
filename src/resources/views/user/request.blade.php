@extends('layouts.app')

@section('title', '申請一覧画面')

@section('css')
<link rel="stylesheet" href="{{ asset('css/request.css') }}?v={{ time() }}">
@endsection
@include('components.user')

@section('content')
<div class="request-container">
    <h1 class="title">申請一覧</h1>

    <div class="approval">
        <ul class="approval-select">
            <li class="{{ $status === 'pending' ? 'active' : '' }}">
                <a href="{{ route('user.correction.list', ['status' => 'pending']) }}">
                    承認待ち
                </a>
            </li>
            <li class="{{ $status === 'approved' ? 'active' : '' }}">
                <a href="{{ route('user.correction.list', ['status' => 'approved']) }}">
                    承認済み
                </a>
            </li>
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
            @forelse ($correctionRequests as $request)
            <tr>
                <td>{{ $status === 'pending' ? '承認待ち' : '承認済み' }}</td>
                <td>{{ $request->user->name }}</td>
                <td>{{ $request->attendance->date->format('Y/m/d') }}</td>
                <td>{{ $request->reason }}</td>
                <td>{{ $request->created_at->format('Y/m/d') }}</td>
                <td>
                    <a href="{{ route('attendance.show', ['id' => $request->attendance->id]) }}">
                        詳細
                    </a>
            </tr>
            @empty
            <tr>
                <td colspan="4">申請はありません</td>
            </tr>
            @endforelse
        </tbody>


    </table>
</div>
@endsection