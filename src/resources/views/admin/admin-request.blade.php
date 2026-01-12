@extends('layouts.app')

@section('title', '申請一覧画面')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin-request.css') }}?v={{ time() }}">
@endsection

@section('content')

@include('components.admin')

<div class="admin-request-container">

    <h1 class="title">申請一覧</h1>

    <!-- 承認タブ -->
    <div class="approval">
        <ul class="approval-select">
            <li class="{{ $status === 'pending' ? 'active' : '' }}">
                <a href="{{ route('admin.correction.list', ['status' => 'pending']) }}">
                    承認待ち
                </a>
            </li>

            <li class="{{ $status === 'approved' ? 'active' : '' }}">
                <a href="{{ route('admin.correction.list', ['status' => 'approved']) }}">
                    承認済み
                </a>
            </li>
        </ul>
    </div>

    <!-- テーブル -->
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
            @forelse ($requests as $request)
            <tr>
                <!-- 状態 -->
                <td>
                    {{ $request->status === 0 ? '承認待ち' : '承認済み' }}
                </td>

                <!-- 名前 -->
                <td>
                    {{ $request->user->name }}
                </td>

                <!-- 対象日時 -->
                <td>
                    {{ $request->attendance->date->format('Y/m/d') }}
                </td>

                <!-- 申請理由 -->
                <td>
                    {{ $request->reason }}
                </td>

                <!-- 申請日時 -->
                <td>
                    {{ $request->created_at->format('Y/m/d') }}
                </td>

                <!-- 詳細 -->
                <td>
                    <a href="{{ route('admin.correction.show', $request->id) }}">
                        詳細
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6">申請はありません</td>
            </tr>
            @endforelse
        </tbody>
    </table>

</div>

@endsection