<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role): Response {
        $user = Auth::user();

        if (!$user) {
            abort(403);
        }

        switch($role) {
            case 'recruiter':
                if (!$user->recruiter) {
                    abort(403);
                }
                break;
            case 'admin':
                if (!$user->admin) {
                    abort(403);
                }
                break;
            
                // DO LATER: we will have to add here the logic for the other user roles (maybe?)
            default:
                abort(403);
        }

        return $next($request);
    }
}
