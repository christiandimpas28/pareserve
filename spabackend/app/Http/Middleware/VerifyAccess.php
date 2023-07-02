<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\LoginOtp;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyAccess
{
    protected $except = [
        'login.log',
        'login.verify-access',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!in_array($request->route()->getName(), $this->except)){
            $user = $request->user();
            $loginOtp = LoginOtp::where('user_id', $user->id)
                                ->where('is_verified', false)
                                ->first();
            if (!empty($loginOtp)) {
                return response()->json([
                    'id' => $loginOtp->id
                ], 406);
            }
        }
        return $next($request);
    }
}
