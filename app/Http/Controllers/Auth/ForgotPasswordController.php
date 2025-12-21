<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class ForgotPasswordController extends Controller
{
    // Mostra o formulário de pedido de link
    public function showLinkRequestForm()
    {
        return view('auth.email');
    }

    //Envia o link de reset para o email
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // Tentar enviar o link
        $status = Password::sendResetLink(
            $request->only('email')
        );

        // Verificar o resultado e responder
        if ($status === Password::RESET_LINK_SENT) {
            return back()->with(['status' => __($status)]);
        }
        
        throw ValidationException::withMessages([
            'email' => [__($status)],
        ]);
    }
}