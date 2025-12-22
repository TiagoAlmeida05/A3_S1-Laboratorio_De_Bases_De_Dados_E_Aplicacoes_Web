<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CompleteRegistrationController extends Controller
{
    public function show()
    {
        return view('auth.complete_registration');
    }

    // Grava a data e finaliza
    public function store(Request $request)
    {
        $request->validate([
            'birthday' => 'required|date|before:today',
        ]);

        $user = Auth::user();
        
        // Calcular idade
        $birthday = Carbon::parse($request->birthday);
        $age = $birthday->age;

        // Atualizar user
        $user->update([
            'birthday' => $birthday,
            'age' => $age
        ]);

        return redirect()->route('job_postings.index')->with('success', 'Register successfully completed!');
    }
}