@extends('layouts.app')
@section('title', 'My Notifications')

@section('content')
<div class="container mx-auto p-4 max-w-4xl">
    
    {{-- Header Section --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">My Notifications</h1>
        
        <button onclick="markAllOnPageRead()" class="button button-outline text-sm">
            Mark all as read
        </button>
    </div>

    <div class="notification-list-container space-y-4">
        @forelse($notifications as $notification)
            @php
                $isRead = !is_null($notification->read_date);
            @endphp

            <div id="notif-{{ $notification->id }}"
                 class="p-5 rounded-lg transition duration-200 relative
                 {{ $isRead 
                    ? 'bg-gray-50 border border-gray-200' 
                    : 'bg-white border-l-4 border-purple-600 shadow-md unread-item' 
                 }}">
                
                <div class="flex justify-between items-start gap-4">
                    <div class="flex-1">
                        {{-- Content --}}
                        <p class="notif-content text-base leading-relaxed {{ $isRead ? 'font-normal text-gray-500' : 'font-bold text-gray-900' }}">
                            {{ $notification->content }}
                        </p>
                        
                        {{-- Date --}}
                        <small class="block mt-2 text-gray-400 text-xs">
                            {{ \Carbon\Carbon::parse($notification->issue_date)->diffForHumans() }}
                        </small>
                    </div>

                    {{-- Action Button --}}
                    @if(!$isRead)
                        <button onclick="markSingleAsRead({{ $notification->id }}, this)" 
                                class="text-sm text-purple-600 hover:text-purple-800 font-semibold p-0 m-0 h-auto leading-none bg-transparent border-0">
                            Mark Read
                        </button>
                    @else
                        <span class="text-gray-300 text-xl">✓</span>
                    @endif
                </div>
            </div>
        @empty
            {{-- Empty State --}}
            <div class="text-center py-16 px-4 bg-gray-50 rounded-lg border border-gray-100">
                <h3 class="text-gray-400 text-lg font-semibold mb-2">No notifications yet</h3>
                <p class="text-gray-500">We will let you know when something important happens!</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mt-8">
        {{ $notifications->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection