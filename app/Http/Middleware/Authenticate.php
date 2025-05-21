<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    // protected function redirectTo(Request $request): ?string
    // {
    //     return $request->expectsJson() ? null : route('admin.login');
    // }

    protected function redirectTo(Request $request): ?string
{
    if ($request->expectsJson()) {
        return null; // Trả về 401 json khi ajax
    }

    // Nếu route đang gọi middleware có prefix 'admin' (ví dụ), hoặc guard admin
    if ($request->is('admin/*')) {
        return route('admin.login');
    }

    // Ngược lại redirect về client login
    return route('user.login'); // Đổi theo route client login của bạn
}

}
