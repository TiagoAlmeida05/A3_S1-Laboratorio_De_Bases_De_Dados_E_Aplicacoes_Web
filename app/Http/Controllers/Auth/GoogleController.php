<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors(['email' => 'Error trying to login with Google.']);
        }

        // Ver se o user já existe na BD
        $user = User::where('google_id', $googleUser->getId())
                    ->orWhere('email', $googleUser->getEmail())
                    ->first();

        // Se já existe...
        if ($user) {            
            // ...por email mas ainda não tinha o Google ID gravado, atualizamos
            if (is_null($user->google_id)) {
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                ]);
            }

            Auth::login($user);
            
            if ($user->isAdmin()) {
                return redirect()->route('admin.jobs');
            }
            return redirect()->intended(route('job_postings.index'));

        } else {
            // Se ainda não existe            
            $newUser = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'password' => Hash::make(Str::random(16)),
                'status' => 'Active',
                'sign_up_date' => now(),
                'birthday' => null, // temporariamente
            ]);

            Auth::login($newUser);

            return redirect()->route('register.complete');
        }
    }
}