@extends('layouts.app')

@section('content')
@if (session('success'))
    <div style="background-color: #d4edda; color: #155724; padding: 10px; margin-bottom: 15px; border: 1px solid #c3e6cb; border-radius: 5px; text-align: center;">
        {{ session('success') }}
    </div>
@endif
<form method="POST" action="{{ route('login') }}">
    @csrf

    <label for="email">E-mail</label>
    <input
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
    <input
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

    <label>
        <input type="checkbox" name="remember" value="1" @checked(old('remember'))>
        Remember me
    </label>

    <button type="submit">Login</button>
    <a class="button button-outline" href="{{ route('register') }}">Register</a>

    @if (session('status'))
        <p class="success" role="status">{{ session('status') }}</p>
    @endif
</form>
@endsection