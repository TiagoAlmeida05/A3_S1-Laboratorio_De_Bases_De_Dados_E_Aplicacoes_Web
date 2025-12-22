@extends('layouts.admin')

@section('content')
<div class="container py-4">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-secondary">Job Seeker Management</h1>
    </div>

    {{-- Barra de Pesquisa --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form action="{{ route('admin.job_seekers') }}" method="GET" class="d-flex gap-2">
                <input type="text" name="search" class="form-control" placeholder="Search by name or email..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary px-4">Search</button>
            </form>
        </div>
    </div>

    {{-- Tabela de Utilizadores --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="py-3 ps-4">ID</th>
                        <th class="py-3">Name</th>
                        <th class="py-3">Email</th>
                        <th class="py-3">Status</th>
                        <th class="py-3 text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td class="ps-4 fw-bold text-muted">#{{ $user->id }}</td>
                            
                            <td class="fw-bold text-dark">
                                {{ $user->name }}
                            </td>

                            <td class="text-secondary">
                                {{ $user->email }}
                            </td>
                            
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
                            
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    
                                    {{-- Botão Editar --}}
                                    <a href="{{ route('admin.job_seekers.edit', $user->id) }}" class="btn btn-sm btn-outline-primary">
                                        Edit
                                    </a>

                                    {{-- Botão Bloquear/Desbloquear --}}
                                    <form action="{{ route('admin.users.block', $user->id) }}" method="POST">
                                        @csrf @method('PATCH')
                                        
                                        @if($user->status === 'Suspended')
                                            <button class="btn btn-sm btn-outline-success">
                                                Unblock
                                            </button>
                                        @else
                                            <button class="btn btn-sm btn-outline-warning text-dark">
                                                Block
                                            </button>
                                        @endif
                                    </form>

                                    {{-- Botão Apagar --}}
                                    <form action="{{ route('admin.job_seeker.delete', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this Job Seeker?');">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">
                                            Delete
                                        </button>
                                    </form>

                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4 d-flex justify-content-center">
        {{ $users->links() }}
    </div>

</div>
@endsection