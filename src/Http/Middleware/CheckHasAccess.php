<?php

namespace Xguard\PhoneScheduler\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Xguard\PhoneScheduler\Models\Employee;

class CheckHasAccess
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $employee = Employee::where('email', '=', Auth::user()->email)->first();
            if ($employee === null || $employee->role !== "admin") {
                return redirect()->route('home');
            }
        }
        else{
            return redirect()->route('home');
        }
        return $next($request);
    }
}
