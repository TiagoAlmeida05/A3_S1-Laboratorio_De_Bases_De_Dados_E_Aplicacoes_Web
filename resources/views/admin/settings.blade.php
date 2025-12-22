@extends('layouts.admin')

@section('title', 'Admin Settings')

@section('content')
<div class="container py-4">
    
    <div class="row justify-content-center">
        <div class="col-md-8">

            <h1 class="h3 text-secondary mb-4">Admin Profile Settings</h1>

            {{-- Alertas de Sucesso/Erro --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- CARD 1: Informação do Perfil --}}
            <div class="card shadow-sm border-0 mb-5">
                <div class="card-header bg-white fw-bold py-3">
                    My Information
                </div>
                <div class="card-body">
                    <div class="mb-3 border-bottom pb-2">
                        <label class="small text-muted text-uppercase fw-bold">Name</label>
                        <div class="fs-5 text-dark">{{ $user->name }}</div>
                    </div>
                    
                    <div class="mb-3 border-bottom pb-2">
                        <label class="small text-muted text-uppercase fw-bold">Email</label>
                        <div class="fs-5 text-dark">{{ $user->email }}</div>
                    </div>

                    <div>
                        <label class="small text-muted text-uppercase fw-bold">Role</label>
                        <div>
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-3">
                                Administrator
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="delete-account-container">
                <h2>Delete Account</h2>
                <p>
                    <strong>Warning:</strong> Once you delete your account, your personal data will be deleted.
                    This action is irreversible.
                </p>
                
                <form action="{{ route('admin.profile.destroy') }}" method="POST">
                    @csrf
                    @method('DELETE')

                    @if(is_null(Auth::user()->google_id))
                        <div class="mb-3">
                            <label for="password_delete" class="form-label">Confirm Password to delete:</label>
                            <input type="password" id="password_delete" name="password" required class="form-control">
                            
                            @error('password')
                                <div style="color: red; margin-top: 5px;">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <strong>Note:</strong> Since you logged in via Google, verification is automatic. No password required.
                        </div>
                    @endif

                    <button type="submit" 
                            class="btn btn-danger"
                            onclick="return confirm('Are you sure you want to delete your administrator account? This action cannot be undone.');">
                        Delete Account
                    </button>
                </form>
            </div>

        </div>
    </div>

</div>
@endsection