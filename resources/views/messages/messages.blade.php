@extends('layouts.app')

@section('title', 'Messages | ' . config('app.name'))

@section('content')
<div style="display: flex; gap: 2rem;">
    <div>
        <h3>Conversations</h3>
        @if($conversations->isEmpty())
            <p>No conversations yet.</p>
        @else
            <ul>
                @foreach($conversations as $otherUserId => $msgs)
                    @php
                        $otherUser = \App\Models\RegisteredUser::find($otherUserId);
                        $hasUnread = $msgs->where('receiver_id', auth()->id())->where('date_read', false)->isNotEmpty();
                    @endphp
                    <li>
                        <a href="{{ route('messages.index', $otherUserId) }}" id="conv-{{ $otherUserId }}">
                            @if($hasUnread)
                                <strong>{{ $otherUser ? $otherUser->name : 'Deleted User' }}</strong>
                            @else
                                {{ $otherUser ? $otherUser->name : 'Deleted User' }}
                            @endif
                            ({{ $msgs->count() }} msgs)
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    <div>
        @if(isset($userId))
            <h3>Conversation with {{ \App\Models\RegisteredUser::find($userId)?->name ?? 'Deleted User' }}</h3>
           <div class="messages" id="messages-container">
                @foreach ($messages as $message)
                    <div>
                        <strong>{{ $message->sender_id === auth()->id() ? 'You' : $message->sender?->name ?? 'Deleted User' }}:</strong>
                        {{ $message->content }}
                        <small>({{ $message->date_sent }})</small>
                    </div>
                @endforeach
            </div>

            <form method="POST" action="{{ route('messages.store') }}">
                @csrf
                <input type="hidden" name="receiver_id" value="{{ $userId }}">
                <textarea name="content" required></textarea>
                <button type="submit">Send</button>
            </form>
        @else
            <p>Select a conversation.</p>
        @endif
    </div>

</div>

<script>
    const userId = {{ $userId }};
    const authId = parseInt({{ auth()->id() }});
    const messagesContainer = document.getElementById('messages-container');

    function fetchMessages() {
        fetch(`/messages/${userId}/json`)
            .then(res => res.json())
            .then(data => {
                messagesContainer.innerHTML = '';
                data.messages.forEach(msg => {
                    const div = document.createElement('div');
                    div.innerHTML = `<strong>${msg.sender_id === authId ? 'You' : msg.sender_name}:</strong> ${msg.content} <small>(${msg.date_sent})</small>`;
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
                        ? `<strong>${conv.name}</strong> (${conv.count} msgs)`
                        : `${conv.name} (${conv.count} msgs)`;
                });
            });
    }
    setInterval(fetchConversations, 3000);
</script>
@endsection
