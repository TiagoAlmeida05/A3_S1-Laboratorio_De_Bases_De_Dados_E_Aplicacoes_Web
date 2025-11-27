@extends('layouts.app')

@section('content')
<form method="POST" style="padding: 3rem; max-width: 30rem; border-color: #4856e7ff;" action="{{ route('register') }}">
    @csrf

    <label for="name">Name</label>
    <input class="input-group mb-3"
        id="name"
        type="text"
        name="name"
        value="{{ old('name') }}"
        required
        autofocus
        autocomplete="name"
    >
    @error('name')
      <span id="name-error" class="error" role="alert">{{ $message }}</span>
    @enderror

    <label for="email">E-mail address</label>
    <input class="input-group mb-3"
        id="email"
        type="email"
        name="email"
        value="{{ old('email') }}"
        required
        autocomplete="email"
        inputmode="email"
    >
    @error('email')
      <span id="email-error" class="error" role="alert">{{ $message }}</span>
    @enderror

    <label for="birthday">Birthday</label>
    <input class="input-group mb-3" 
        id="birthday" 
        type="date" 
        name="birthday" 
        required>

    @error('birthday')
      <span id="birthday-error" class="error" role="alert">{{ $message }}</span>
    @enderror

    <label for="password">Password</label>
    <input class="input-group mb-3"
        id="password"
        type="password"
        name="password"
        required
        autocomplete="new-password"
    >
    @error('password')
      <span id="password-error" class="error" role="alert">{{ $message }}</span>
    @enderror

    <label for="password-confirm">Confirm password</label>
    <input class="input-group mb-3"
        id="password-confirm"
        type="password"
        name="password_confirmation"
        required
        autocomplete="new-password"
    >

    <button type="submit" class="btn btn-primary" style="background-color: #1c4eb1eb;">Register</button>
    <a class="btn btn-primary" style="font-size: 0.6em; background-color: #1c4eb1eb;" href="{{ route('login') }}"><- Return to Login</a>
</form>
@endsection