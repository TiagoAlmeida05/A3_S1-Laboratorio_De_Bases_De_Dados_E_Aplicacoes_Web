<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            // Se já estiver logado, verifica o tipo para redirecionar bem
            if (Auth::user()->isAdmin()) {
                return redirect()->route('admin.jobs');
            }
            return redirect()->route('job_postings.index');
        } else {
            return view('auth.login');
        }
    }

    /**
     * Process an authentication attempt.
     */
    public function authenticate(Request $request): RedirectResponse
    {
        // Validate the request data.
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Attempt to authenticate and log in the user.
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            // Regenerate the session ID to prevent session fixation attacks.
            $request->session()->regenerate();

            // --- AQUI ESTÁ A MUDANÇA ---
            // Obtém o utilizador que acabou de entrar
            $user = Auth::user();

            // Se for Admin, manda para o Painel de Administração
            if ($user->isAdmin()) {
                return redirect()->route('admin.jobs');
            }
            // ---------------------------

            // Se for um user normal (Alice), manda para a lista de ofertas
            return redirect()->intended(route('job_postings.index'));
        }

        // Authentication failed: return back with an error message.
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }
}