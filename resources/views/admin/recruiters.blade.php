@extends('layouts.admin')

@section('content')
    <h1>Recruiter Management</h1>

    <div>
        <form action="{{ route('admin.recruiters') }}" method="GET">
            <input type="text" name="search" placeholder="Search Recruiters..." value="{{ request('search') }}">
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Company</th>
                <th>Role</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>
                        {{ $user->name }}<br>
                        <small>{{ $user->email }}</small>
                    </td>
                    
                    <td>
                        @if($user->recruiter && $user->recruiter->department && $user->recruiter->department->company)
                            {{ $user->recruiter->department->company->name }}
                        @else
                            <span>N/A</span>
                        @endif
                    </td>

                    <td>
                        @if($user->recruiter->is_company_manager)
                            <strong>Manager</strong>
                        @else
                            Recruiter
                        @endif
                    </td>

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
                        <div style="display: flex; gap: 10px; align-items: center;">
                            
                            
                            @if($user->recruiter->is_company_manager)
                                <span style="font-size: 0.8em; color: gray; font-style: italic;">
                                    Currently Managing
                                </span>

                            @else
                                
                                <form action="{{ route('admin.recruiters.promote', $user->id) }}" method="POST" onsubmit="return confirm('Promote to Manager? This will demote the current manager.');">
                                    @csrf @method('PATCH')
                                    <button class="button" title="Promote to Manager">
                                        Promote
                                    </button>
                                </form>

                                <form action="{{ route('admin.users.block', $user->id) }}" method="POST">
                                    @csrf @method('PATCH')
                                    @if($user->status === 'Suspended')
                                        <button class="button button-outline">Unblock</button>
                                    @else
                                        <button class="button button-outline">Block</button>
                                    @endif
                                </form>

                                <form action="{{ route('admin.recruiters.delete', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure? This action is irreversible.');">
                                    @csrf @method('DELETE')
                                    <button class="button button-outline">
                                        Delete
                                    </button>
                                </form>
                            @endif

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