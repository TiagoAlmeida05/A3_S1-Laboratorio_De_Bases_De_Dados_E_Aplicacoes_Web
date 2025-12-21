@extends('layouts.app')

@section('content')
<div class="container" style="display: flex; justify-content: center; padding-top: 50px;">
    <div class="card" style="width: 100%; max-width: 500px; padding: 20px; border: 1px solid #ccc; border-radius: 8px;">
        <h3 class="text-center mb-4">Recuperar Password</h3>
        
        <div class="card-body">
            @if (session('status'))
                <div class="alert alert-success" role="alert" style="color: green; background: #d4edda; padding: 10px; margin-bottom: 15px; border-radius: 5px;">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="form-group mb-3">
                    <label for="email" class="form-label">Endereço de Email</label>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                           name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                           style="width: 100%; padding: 8px; margin-top: 5px;">

                    @error('email')
                        <span class="invalid-feedback" role="alert" style="color: red; font-size: 0.9em;">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group mb-0 text-center">
                    <button type="submit" class="btn btn-primary" style="background-color: #1c4eb1eb; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">
                        Enviar Link de Recuperação
                    </button>
                </div>
                
                <div class="mt-3 text-center">
                    <a href="{{ route('login') }}" style="text-decoration: none; color: #666;">Voltar ao Login</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection