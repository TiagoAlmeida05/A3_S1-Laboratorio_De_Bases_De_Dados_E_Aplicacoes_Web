@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <h2>Send Manual Notification</h2>
    <p class="text-muted">Send a direct alert to any user on the platform.</p>
    
    <div class="card p-4 shadow-sm" style="max-width: 600px;">
        <form action="{{ route('admin.notifications.send') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label for="email" class="form-label fw-bold">User Email</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="user@example.com" required>
                <div class="form-text">Enter the exact email of the user you want to notify.</div>
            </div>

            <div class="mb-4">
                <label for="message" class="form-label fw-bold">Message</label>
                <textarea class="form-control" id="message" name="message" rows="4" placeholder="Type your message here..." required></textarea>
            </div>

            <button type="submit" class="button button-primary w-100">Send Notification</button>
        </form>
    </div>
</div>
@endsection