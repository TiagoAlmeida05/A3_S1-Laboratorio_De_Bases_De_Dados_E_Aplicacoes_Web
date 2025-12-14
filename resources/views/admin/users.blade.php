@extends('layouts.admin')

@section('content')
    <h1>User Management</h1>
    
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
                        @if(!$user->isAdmin())
                            <div>
                                {{-- Botão Bloquear/Desbloquear (US59) --}}
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
                            </div>
                        @else
                            <span>Administrator</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div>
        {{ $users->links() }}
    </div>
@endsection