<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Se não estiver logado, manda para o Login
        if (!Auth::check()) {
            return redirect('/login');
        }

        // 2. Se estiver logado mas NÃO for Admin (usando a função que criámos no User.php)
        if (!$request->user()->isAdmin()) {
            // Erro 403 = Acesso Proibido
            abort(403, 'Acesso reservado a administradores.');
        }

        // 3. Se passou nos testes, deixa entrar
        return $next($request);
    }
}