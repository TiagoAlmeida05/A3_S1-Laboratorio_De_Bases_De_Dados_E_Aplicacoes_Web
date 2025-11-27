<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use Illuminate\View\View;

use App\Models\User;

class RegisterController extends Controller
{
    /**
     * Show the user registration form.
     */
    public function showRegistrationForm(): View
    {
        // Render the registration view.
        return view('auth.register');
    }

    /**
     * Handle a new user registration request.
     *
     * This method:
     * - Validates the registration input data.
     * - Creates a new user with a hashed password.
     * - Logs the user in automatically after registration.
     * - Regenerates the session to prevent fixation attacks.
     * - Redirects the user to the cards page with a success message.
     */
    public function register(Request $request)
    {
        // Validate registration input.
        $request->validate([
            'name' => 'required|string|max:250',
            'email' => 'required|email|max:250|unique:registered_user',
            'password' => 'required|min:8|confirmed',
            'birthday' => 'required|date|before:-18 years',],
            ['birthday.before' => 'Register is only available if you are at least 18 years old.']
        );

        $age = date_diff(date_create($request->birthday), date_create('today'))->y;

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'birthday' => $request->birthday,
            'age' => $age,
            'status' =>  'Active'
        ]);

        // Attempt login for the newly registered user.
        $credentials = $request->only('email', 'password');
        Auth::attempt($credentials);

        // Regenerate session for security (protection against session fixation).
        $request->session()->regenerate();

        // Redirect to cards page with a success message.
        return redirect()->route('job_postings.index')
            ->withSuccess('You have successfully registered & logged in!');
    }
}
