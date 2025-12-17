<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            if (Auth::user()->isAdmin()) {
                return redirect()->route('admin.jobs');
            }
            return redirect()->route('job_postings.index');
        } else {
            return view('auth.login');
        }
    }

    public function authenticate(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
        $credentials['status'] = 'Active';

        if (Auth::attempt($credentials, $request->filled('remember'))) {
+            $request->session()->regenerate();
            $user = Auth::user();

            if ($user->isAdmin()) {
                return redirect()->route('admin.jobs');
            }

            return redirect()->intended(route('job_postings.index'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records or deleted/blocked account.',
        ])->onlyInput('email');
    }
}