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
                    @endphp
                    <li>
                        <a href="{{ route('messages.index', $otherUserId) }}">
                            {{ $otherUser ? $otherUser->name : 'Deleted User' }}
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
            <div class="messages">
                @foreach ($messages as $message)
                    @php
                        $sender = \App\Models\RegisteredUser::find($message->sender_id);
                    @endphp
                    <div>
                        <strong>
                            {{ $message->sender_id === auth()->id() ? 'You' : ($sender?->name ?? 'Deleted User') }}:
                        </strong>
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
@endsection
