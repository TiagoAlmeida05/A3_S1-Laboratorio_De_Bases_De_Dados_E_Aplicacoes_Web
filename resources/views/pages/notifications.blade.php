@extends('layouts.app')
@section('title', 'My Notifications')

@section('content')
<div class="all-notifications-container mx-auto p-4">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>My notifications</h1>
        <button onclick="markAllOnPageRead()" class="btn btn-primary">
            Mark all as read
        </button>
    </div>

    <div class="notification-list-container">
        @forelse($notifications as $notification)
            @php
                $isRead = !is_null($notification->read_date);
            @endphp

            <div id="notif-{{ $notification->id }}"
                 class="card mb-2 {{ $isRead ? 'read-notif' : 'unread-notif' }}">
                
                <div class="card-body py-2 d-flex justify-content-between align-items-center">
                    <div>
                        <p class="notif-content mb-0 {{ $isRead ? 'text-muted' : 'fw-bold' }}">
                            {{ $notification->content }}
                        </p>
                        <small class="text-muted">
                            {{ \Carbon\Carbon::parse($notification->issue_date)->diffForHumans() }}
                        </small>
                    </div>

                    @if(!$isRead)
                        <button onclick="markSingleAsRead({{ $notification->id }}, this)" 
                                class="btn btn-primary btn-sm">
                            Mark as read
                        </button>
                    @else
                        <span class="text-success">✓</span>
                    @endif
                </div>
            </div>
        @empty
            <div class="card">
                <div class="card-body text-center py-4">
                    <h3 class="text-muted mb-2">No notifications yet.</h3>
                    <p class="text-muted mb-0">We will let you know when something important happens!</p>
                </div>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $notifications->links('pagination::bootstrap-5') }}
    </div>
</div>
<script src="{{ asset('js/notifications-page.js') }}"></script>
@endsection