<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // 未ログイン
        if (!auth()->check()) {
            return redirect()->route('admin-login');
        }

        // 管理者でない
        if (!auth()->user()->is_admin) {
            abort(403, '管理者権限がありません');
        }

        return $next($request);
    }
}
