@extends('layouts.app')

@section('title', 'Messages | ' . config('app.name'))

@section('content')
<div class="messages-general-container row p-4 mx-auto">
    <div class="conversations-list col-md-3 border-end d-flex flex-column">
        <h3 class="mb-3">Conversations</h3>
        @if($conversations->isEmpty())
            <p class="text-muted">No conversations yet.</p>
        @else
            <ul class="list-group overflow-auto flex-grow-1">
                @foreach($conversations as $otherUserId => $msgs)
                    @php
                        $otherUser = \App\Models\RegisteredUser::find($otherUserId);
                        $hasUnread = $msgs->where('receiver_id', auth()->id())->where('date_read', false)->isNotEmpty();
                    @endphp
                    <li class="list-group-item">
                        <a href="{{ route('messages.index', $otherUserId) }}" id="conv-{{ $otherUserId }}" class="text-decoration-none">
                            @if($hasUnread)
                                <strong>{{ $otherUser ? $otherUser->name : 'Deleted user' }}</strong>
                            @else
                                {{ $otherUser ? $otherUser->name : 'Deleted user' }}
                            @endif
                            <span class="text-muted">({{ $msgs->count() }})</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    <div class="col-md-9 d-flex flex-column">
        @if(isset($userId))
            <h3 class="mb-3">Conversation with {{ \App\Models\RegisteredUser::find($userId)?->name ?? 'Deleted user' }}</h3>
            <div class="messages overflow-auto p-3" id="messages-container">
                @foreach ($messages as $message)
                    @if($message->sender_id === auth()->id())
                        <div class="d-flex justify-content-end mb-2">
                            <div class="message-sent text-white p-2">
                                <p class="mb-1">{{ $message->content }}</p>
                                <small class="opacity-75">{{ \Carbon\Carbon::parse($message->date_sent)->format('d/m/Y H:i') }}</small>
                            </div>
                        </div>
                    @else
                        <div class="d-flex justify-content-start mb-2">
                            <div class="message-received p-2" style="border-radius: 0.5rem; max-width: 70%;">
                                <p class="mb-1">{{ $message->content }}</p>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($message->date_sent)->format('d/m/Y H:i') }}</small>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

            <form method="POST" action="{{ route('messages.store') }}" class="mt-3 bg-white py-3">
                @csrf
                <input type="hidden" name="receiver_id" value="{{ $userId }}">
                <div class="d-flex gap-3">
                    <textarea name="content" class="form-control" rows="2" placeholder="Type your message..." required></textarea>
                    <button type="submit" class="btn btn-primary">Send</button>
                </div>
            </form>
        @else
            <p class="text-muted">Select a conversation.</p>
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('messages-container');
        if (container) container.scrollTop = container.scrollHeight;
    });
</script>

<script>
    const userId = {{ $userId ?? 'null' }};
    const authId = parseInt({{ auth()->id() }});
    const messagesContainer = document.getElementById('messages-container');

    function fetchMessages() {
        if (!userId) return;
        fetch(`/messages/${userId}/json`)
            .then(res => res.json())
            .then(data => {
                messagesContainer.innerHTML = '';
                data.messages.forEach(msg => {
                    const div = document.createElement('div');
                    div.className = msg.sender_id === authId ? 'd-flex justify-content-end mb-2' : 'd-flex justify-content-start mb-2';
                    const bgClass = msg.sender_id === authId ? 'message-sent text-white' : 'message-received';
                    const dateClass = msg.sender_id === authId ? 'opacity-75' : 'text-muted';
                    const date = new Date(msg.date_sent);
                    const formattedDate = date.toLocaleDateString('en-GB') + ' ' + date.toLocaleTimeString('en-GB', {hour: '2-digit', minute: '2-digit'});
                    div.innerHTML = `<div class="${bgClass} p-2" style="border-radius: 0.5rem; max-width: 70%;"><p class="mb-1">${msg.content}</p><small class="${dateClass}">${formattedDate}</small></div>`;
                    messagesContainer.appendChild(div);
                });
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            });
    }
    setInterval(fetchMessages, 3000);

    function fetchConversations() {
        fetch('/conversations/json')
            .then(res => res.json())
            .then(data => {
                data.forEach(conv => {
                    const link = document.getElementById(`conv-${conv.otherUserId}`);
                    if (!link) return;
                    link.innerHTML = conv.hasUnread
                        ? `<strong>${conv.name}</strong> <span class="text-muted">(${conv.count})</span>`
                        : `${conv.name} <span class="text-muted">(${conv.count})</span>`;
                });
            });
    }
    setInterval(fetchConversations, 3000);
</script>
@endsection