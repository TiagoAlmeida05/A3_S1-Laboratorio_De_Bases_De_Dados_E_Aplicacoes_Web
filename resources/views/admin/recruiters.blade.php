@extends('layouts.admin')

@section('content')
<div class="container py-4">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-secondary">Recruiter Management</h1>
    </div>

    {{-- Barra de Pesquisa --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form action="{{ route('admin.recruiters') }}" method="GET" class="d-flex gap-2">
                <input type="text" name="search" class="form-control" placeholder="Search Recruiters..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary px-4">Search</button>
            </form>
        </div>
    </div>

    {{-- Tabela de Recrutadores --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="py-3 ps-4">ID</th>
                        <th class="py-3">Name</th>
                        <th class="py-3">Company</th>
                        <th class="py-3">Role</th>
                        <th class="py-3">Status</th>
                        <th class="py-3 text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td class="ps-4 fw-bold text-muted">#{{ $user->id }}</td>
                            
                            {{-- Nome e Email --}}
                            <td>
                                <div class="fw-bold text-dark">{{ $user->name }}</div>
                                <div class="small text-secondary">{{ $user->email }}</div>
                            </td>
                            
                            {{-- Empresa --}}
                            <td class="text-secondary">
                                @if($user->recruiter && $user->recruiter->department && $user->recruiter->department->company)
                                    <span class="fw-medium">{{ $user->recruiter->department->company->name }}</span>
                                @else
                                    <span class="text-muted fst-italic">N/A</span>
                                @endif
                            </td>

                            {{-- Role (Manager ou Recruiter) --}}
                            <td>
                                @if($user->recruiter->is_company_manager)
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-3">
                                        Manager
                                    </span>
                                @else
                                    <span class="badge bg-light text-dark border rounded-pill px-3">
                                        Recruiter
                                    </span>
                                @endif
                            </td>

                            {{-- Status --}}
                            <td>
                                @if($user->status === 'Active')
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3">
                                        Active
                                    </span>
                                @elseif($user->status === 'Suspended')
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-3">
                                        Suspended
                                    </span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 rounded-pill px-3">
                                        {{ $user->status }}
                                    </span>
                                @endif
                            </td>
                            
                            {{-- Ações --}}
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    
                                    @if($user->recruiter->is_company_manager)
                                        <span class="text-muted small fst-italic align-self-center me-2">
                                            Currently Managing
                                        </span>
                                    @else
                                        {{-- Promote Button --}}
                                        <form action="{{ route('admin.recruiters.promote', $user->id) }}" method="POST" onsubmit="return confirm('Promote to Manager? This will demote the current manager.');">
                                            @csrf @method('PATCH')
                                            <button class="btn btn-sm btn-outline-primary" title="Promote to Manager">
                                                Promote
                                            </button>
                                        </form>

                                        {{-- Block Button --}}
                                        <form action="{{ route('admin.users.block', $user->id) }}" method="POST">
                                            @csrf @method('PATCH')
                                            @if($user->status === 'Suspended')
                                                <button class="btn btn-sm btn-outline-success">Unblock</button>
                                            @else
                                                <button class="btn btn-sm btn-outline-warning text-dark">Block</button>
                                            @endif
                                        </form>

                                        {{-- Delete Button --}}
                                        <form action="{{ route('admin.recruiters.delete', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure? This action is irreversible.');">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger">
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
        </div>
    </div>

    {{-- Paginação --}}
    <div class="mt-4 d-flex justify-content-center">
        {{ $users->links() }}
    </div>

</div>
@endsection