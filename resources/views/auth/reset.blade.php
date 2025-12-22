@extends('layouts.app')

@section('content')
<div class="container" style="display: flex; justify-content: center; padding-top: 50px;">
    <div class="card" style="width: 100%; max-width: 500px; padding: 20px; border: 1px solid #ccc; border-radius: 8px;">
        <h3 class="text-center mb-4">Definir Nova Password</h3>
        
        <div class="card-body">
            <form method="POST" action="{{ route('password.update') }}">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">

                <div class="form-group mb-3">
                    <label for="email" class="form-label">Endereço de Email</label>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                           name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus
                           style="width: 100%; padding: 8px; margin-top: 5px;">

                    @error('email')
                        <span class="invalid-feedback" role="alert" style="color: red; font-size: 0.9em;">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="password" class="form-label">Nova Password</label>
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                           name="password" required autocomplete="new-password"
                           style="width: 100%; padding: 8px; margin-top: 5px;">

                    @error('password')
                        <span class="invalid-feedback" role="alert" style="color: red; font-size: 0.9em;">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="password-confirm" class="form-label">Confirmar Nova Password</label>
                    <input id="password-confirm" type="password" class="form-control" 
                           name="password_confirmation" required autocomplete="new-password"
                           style="width: 100%; padding: 8px; margin-top: 5px;">
                </div>

                <div class="form-group mb-0 text-center">
                    <button type="submit" class="btn btn-primary" style="background-color: #1c4eb1eb; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">
                        Alterar Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection