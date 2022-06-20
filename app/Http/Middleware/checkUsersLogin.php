<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Auth;
class checkUsersLogin
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check())
        {
		
            $user = Auth::user();  

            if ($user->status == 1 )
            {
                return $next($request);
            }
            else
            {
                Auth::logout();
                return redirect('users/login');
            }
        } else
		{
            return redirect('users/login');
		}
    }
}
