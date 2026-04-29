<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class ApiAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $parcUserId = $request->header('ParcUserId');
        $user = User::query()->where('id', '=', $parcUserId)->first();
        if ($user == null) {
            // redirect('login');
            return response([
                "error" => "Access forbidden",
                "message" => "You are not allowed to access to this resource",
            ], 403);
        }
        return $next($request);
    }
}
