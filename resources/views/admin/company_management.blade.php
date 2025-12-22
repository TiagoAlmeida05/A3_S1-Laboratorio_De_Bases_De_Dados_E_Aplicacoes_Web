@extends('layouts.admin')

@section('content')
<div class="container py-4">
    
    <div class="mb-4">
        <h1 class="h3 text-secondary">Company Management</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Lógica para filtrar pendentes --}}
    @php
        $pendingCompanies = $companies->filter(function($company) {
            $manager = $company->recruiters->where('is_company_manager', true)->first();
            return $manager && $manager->user->status === 'Pending';
        });
    @endphp

    {{-- SECÇÃO: PENDING COMPANIES (Só aparece se houver) --}}
    @if($pendingCompanies->isNotEmpty())
        <div class="card shadow-sm border-warning border-2 mb-5">
            <div class="card-header bg-warning bg-opacity-10">
                <h2 class="h6 mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    ⚠️ Pending Approvals
                </h2>
            </div>
            <div class="card-body p-0">
                <div class="p-3 text-muted small">
                    The following companies have been created but their managers are waiting for approval.
                </div>
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Company Name</th>
                            <th>Manager Email</th>
                            <th>Date Registered</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingCompanies as $company)
                            @php
                                $manager = $company->recruiters->where('is_company_manager', true)->first();
                            @endphp
                            <tr>
                                <td class="ps-4 fw-bold">{{ $company->name }}</td>
                                <td>{{ $manager->user->email }}</td>
                                <td class="text-muted small">{{ $company->date_added }}</td>
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end gap-2">
                                        {{-- APPROVE FORM --}}
                                        <form action="{{ route('admin.users.approve', $manager->user->id) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-success fw-bold" onclick="return confirm('Approve this company and activate the manager?')">
                                                Approve
                                            </button>
                                        </form>
                                        
                                        {{-- REVIEW BUTTON --}}
                                        <a href="{{ route('admin.companies.edit', $company->id) }}" class="btn btn-sm btn-outline-secondary">
                                            Review
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- SECÇÃO: ACTIVE COMPANIES --}}
    <h2 class="h5 text-secondary mb-3">Active Companies</h2>
    
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="py-3 ps-4">ID</th>
                        <th class="py-3">Name</th>
                        <th class="py-3">Website</th>
                        <th class="py-3">Manager Status</th>
                        <th class="py-3 text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($companies as $company)
                        @php
                            $manager = $company->recruiters->where('is_company_manager', true)->first();
                            // Salta se for pendente (já está na tabela de cima)
                            if ($manager && $manager->user->status === 'Pending') continue; 
                        @endphp

                        <tr>
                            <td class="ps-4 text-muted fw-bold">#{{ $company->id }}</td>
                            
                            <td class="fw-bold text-dark">
                                {{ $company->name }}
                            </td>
                            
                            <td>
                                @if($company->website)
                                    <a href="{{ $company->website }}" target="_blank" class="text-decoration-none">
                                        Visit Website
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            <td>
                                @if($manager)
                                    @if($manager->user->status === 'Active')
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3">Active</span>
                                    @elseif($manager->user->status === 'Suspended')
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-3">Suspended</span>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 rounded-pill px-3">{{ $manager->user->status }}</span>
                                    @endif
                                @else
                                    <span class="badge bg-light text-danger border border-danger">No Manager</span>
                                @endif
                            </td>
                            
                            <td class="text-end pe-4">
                                <a href="{{ route('admin.companies.edit', $company->id) }}" class="btn btn-sm btn-outline-primary">
                                    Edit
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Paginação --}}
    <div class="mt-4 d-flex justify-content-center">
        {{ $companies->links() }}
    </div>

</div>
@endsection