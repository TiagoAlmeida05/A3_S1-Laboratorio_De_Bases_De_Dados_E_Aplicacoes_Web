@extends('layouts.app')

@section('title', 'Finalizar Registo')

@section('content')
<div class="container d-flex flex-column align-items-center justify-content-center" style="min-height: 60vh;">

    <div class="text-center mb-4">
        <h3 class="mb-2">Almost there, {{ Auth::user()->name }}!</h3>
        <p class="text-muted">We only need the birthday to complete registration.</p>
    </div>

    <form method="POST" action="{{ route('register.complete.store') }}" 
          class="bg-white border rounded shadow-sm p-5 w-100" 
          style="max-width: 500px;">
        
        @csrf

        <div class="mb-4">
            <label for="birthday" class="fw-bold">Birthday</label>
            <input type="date" id="birthday" name="birthday" required class="w-100 form-control">

            @error('birthday')
                <small class="text-danger d-block mt-1">{{ $message }}</small>
            @enderror
        </div>

        <button type="submit" class="button button-primary w-100 mt-2">
            Complete Register
        </button>
        
    </form>
</div>
@endsection