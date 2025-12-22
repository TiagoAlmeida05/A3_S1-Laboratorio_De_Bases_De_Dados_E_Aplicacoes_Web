@extends('layouts.app')

@section('title', 'Admin Settings | ' . config('app.name'))

@section('content')
<section id="admin-settings" class="container">
    
    <h1>Admin Profile Settings</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="card admin-info">
        <h3>My Information</h3>
        <p><strong>Name:</strong> {{ $user->name }}</p>
        <p><strong>Email:</strong> {{ $user->email }}</p>
        <p><strong>Role:</strong> Administrator</p>
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

</section>
@endsection