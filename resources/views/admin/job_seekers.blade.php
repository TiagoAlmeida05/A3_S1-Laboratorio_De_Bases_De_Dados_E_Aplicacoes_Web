@extends('layouts.admin')

@section('content')
    <h1>Job Seeker Management</h1>

    <div>
        <form action="{{ route('admin.job_seekers') }}" method="GET">
            <input type="text" name="search" placeholder="Search Job Seekers..." value="{{ request('search') }}">
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    
                    <td>
                        @if($user->status === 'Active')
                            <span>Active</span>
                        @elseif($user->status === 'Suspended')
                            <span>Suspended</span>
                        @else
                            <span>{{ $user->status }}</span>
                        @endif
                    </td>
                    
                    <td>
                        <div>
                            {{-- Botão Bloquear/Desbloquear --}}
                            <form action="{{ route('admin.users.block', $user->id) }}" method="POST">
                                @csrf @method('PATCH')
                                
                                @if($user->status === 'Suspended')
                                    <button class="button button-outline">
                                        Unblock
                                    </button>
                                @else
                                    <button class="button button-outline">
                                        Block
                                    </button>
                                @endif
                            </form>

                            {{-- Botão Apagar --}}
                            <form action="{{ route('admin.job_seeker.delete', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this Job Seeker?');">
                                @csrf @method('DELETE')
                                <button class="button button-outline">
                                    Delete
                                </button>
                            </form>
                            <a href="{{ route('admin.job_seekers.edit', $user->id) }}" class="button button-outline">
                                Edit
                            </a>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div>
        {{ $users->links() }}
    </div>
@endsection