@extends('layouts.admin')

@section('content')
    <h1>Company Management</h1>

    {{-- SECTION 1: PENDING COMPANIES --}}
    @php
        $pendingCompanies = $companies->filter(function($company) {
            $manager = $company->recruiters->where('is_company_manager', true)->first();
            return $manager && $manager->user->status === 'Pending';
        });
    @endphp

    @if($pendingCompanies->isNotEmpty())
        <div style="margin-bottom: 40px; padding: 20px; background-color: #fff8e1; border: 1px solid #ffeeba; border-radius: 5px;">
            <h2 style="color: #856404; margin-top: 0;">⚠️ Pending Approvals</h2>
            <p>The following companies have been created but their managers are waiting for approval.</p>
            
            <table>
                <thead>
                    <tr>
                        <th>Company Name</th>
                        <th>Manager Email</th>
                        <th>Date Registered</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendingCompanies as $company)
                        @php
                            $manager = $company->recruiters->where('is_company_manager', true)->first();
                        @endphp
                        <tr>
                            <td>{{ $company->name }}</td>
                            <td>{{ $manager->user->email }}</td>
                            <td>{{ $company->date_added }}</td>
                            <td>
                                {{-- APPROVE FORM --}}
                                <form action="{{ route('admin.users.approve', $manager->user->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="button button-primary" onclick="return confirm('Approve this company and activate the manager?')">
                                        Approve
                                    </button>
                                </form>
                                
                                {{-- Optional: Edit before approving --}}
                                <a href="{{ route('admin.companies.edit', $company->id) }}" class="button button-outline" style="margin-left: 5px;">
                                    Review
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- SECTION 2: ALL ACTIVE COMPANIES --}}
    <h2>Active Companies</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Website</th>
                <th>Manager Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($companies as $company)
                @php
                    $manager = $company->recruiters->where('is_company_manager', true)->first();
                    if ($manager && $manager->user->status === 'Pending') continue; 
                @endphp

                <tr>
                    <td>{{ $company->id }}</td>
                    
                    <td>
                        {{ $company->name }}
                    </td>
                    
                    <td>
                        @if($company->website)
                            <a href="{{ $company->website }}" target="_blank">Link</a>
                        @else
                            -
                        @endif
                    </td>

                    <td>
                        @if($manager)
                            <span class="status-{{ strtolower($manager->user->status) }}">
                                {{ $manager->user->status }}
                            </span>
                        @else
                            <span style="color: red;">No Manager</span>
                        @endif
                    </td>
                    
                    <td>
                        <a href="{{ route('admin.companies.edit', $company->id) }}" class="button button-outline">
                            Edit
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div>
        {{ $companies->links() }}
    </div>
@endsection