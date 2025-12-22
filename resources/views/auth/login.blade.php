@extends('layouts.app')

@section('content')
@if (session('success'))
    <div style="background-color: #d4edda; color: #155724; padding: 10px; margin-bottom: 15px; border: 1px solid #c3e6cb; border-radius: 5px; text-align: center;">
        {{ session('success') }}
    </div>
@endif
<form style="padding: 3rem; max-width: 30rem; border-color: #4856e7ff;" method="POST" action="{{ route('login') }}">
    @csrf

    <label for="email">E-mail</label>
    <input class="input-group mb-3"
        id="email"
        name="email"
        type="email"
        value="{{ old('email') }}"
        required
        autofocus
        inputmode="email"
        autocomplete="email"
    >
    @error('email')
        <span id="email-error" class="error" role="alert">
            {{ $message }}
        </span>
    @enderror

    <label for="password" >Password</label>
    <input class="input-group mb-3"
        id="password"
        name="password"
        type="password"
        required
        autocomplete="current-password"
    >
    @error('password')
        <span id="password-error" class="error" role="alert">
            {{ $message }}
        </span>
    @enderror

    <div class="col">
        <button type="submit" class="btn btn-primary" style="background-color: #1c4eb1eb;">Login</button>
        <a class="btn btn-primary" style="background-color: #1c4eb1eb;" href="{{ route('register') }}">Register</a>
    </div>

    <div class="mt-3" style="text-align: left;">
        <a href="{{ route('password.request') }}" style="color: #1c4eb1eb; text-decoration: underline;">
            Forgot password?
        </a>
    </div>

    <div style="margin-top: 2rem; border-top: 1px solid #f0f0f0; padding-top: 1.5rem; text-align: center;">
        <span style="display: block; margin-bottom: 1rem; color: #666;">
            Or...
        </span>
        
        <a href="{{ route('auth.google') }}" style= "color: #3c4043; border: 1px solid #dadce0;">
            <img src="https://thumbs.dreamstime.com/b/google-logo-vector-format-white-background-illustration-407571048.jpg" 
                alt="Google Logo" 
                style="width: 20px; height: 20px; margin-right: 12px; object-fit: contain;">
            
            <span>Continue with Google</span>
        </a>
    </div>

    @if (session('status'))
        <p class="success" role="status">{{ session('status') }}</p>
    @endif
</form>
@endsection