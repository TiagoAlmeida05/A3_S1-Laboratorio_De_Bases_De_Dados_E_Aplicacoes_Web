@extends('layouts.app')

@section('title', 'My dashboard' . ' | ' . config('app.name'))

@section('content')

<section id="recruiter-dashboard">
    <h2>{{ $user->name }}{{Str::endsWith($user->name, 's') ? '\'' : "'s"}} Recruiter Dashboard</h2>
    
    @if(isset($companyManager) && !$user->recruiter->is_company_manager)
        <div style="margin: 1rem 0;">
            <a href="{{ route('messages.index', $companyManager->registered_user_id) }}" class="button btn btn-primary" style="background-color: #1c4eb1eb;">Chat with Company Manager</a>
        </div>
    @endif

    {{-- MANAGER TABS --}}

    @if(Auth::user()->recruiter->is_company_manager)
        <div class="mb-4" style="margin-top: 20px; border-bottom: 1px solid #ddd;">
            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" style="display: flex; gap: 1rem; list-style: none; padding: 0;">               
                {{-- TAB 1: MY JOBS --}}
                <li class="mr-2">
                    <a href="{{ route('recruiter-dashboard.index', ['view' => 'personal']) }}" 
                       style="text-decoration: none; padding: 10px; 
                              border-bottom: 2px solid {{ request('view', 'personal') === 'personal' ? '#1c4eb1' : 'transparent' }}; 
                              color: {{ request('view', 'personal') === 'personal' ? '#1c4eb1' : '#666' }}; 
                              font-weight: {{ request('view', 'personal') === 'personal' ? 'bold' : 'normal' }};">
                        My Job Postings
                    </a>
                </li>
                {{-- TAB 2: COMPANY JOBS --}}
                <li class="mr-2">
                    <a href="{{ route('recruiter-dashboard.index', ['view' => 'company']) }}" 
                       style="text-decoration: none; padding: 10px; 
                              border-bottom: 2px solid {{ request('view') === 'company' ? '#1c4eb1' : 'transparent' }}; 
                              color: {{ request('view') === 'company' ? '#1c4eb1' : '#666' }}; 
                              font-weight: {{ request('view') === 'company' ? 'bold' : 'normal' }};">
                        All Company Jobs
                    </a>
                </li>
                {{-- TAB 3: MANAGE STAFF --}}
                <li class="mr-2">
                    <a href="{{ route('recruiter-dashboard.index', ['view' => 'staff']) }}" 
                       style="text-decoration: none; padding: 10px; 
                              border-bottom: 2px solid {{ request('view') === 'staff' ? '#1c4eb1' : 'transparent' }}; 
                              color: {{ request('view') === 'staff' ? '#1c4eb1' : '#666' }}; 
                              font-weight: {{ request('view') === 'staff' ? 'bold' : 'normal' }};">
                        Manage Staff
                    </a>
                </li>
            </ul>
        </div>
    @endif

    {{-- VIEW: JOB POSTINGS --}}

    @if(request('view') !== 'staff')
        
        <div class="dashboard-create-job" style="margin: 1rem 0rem;">
            <a class="button btn btn-primary" style="background-color: #1c4eb1eb; padding: 0.5rem 0.4rem;" href="{{ route('job_postings.create') }}" class="a-as-button">Create new job posting</a>
        </div>

        <div class='job-postings'>
            {{-- Active & Pending --}}
            <div class='active-pending-jps'>
                <h3>Your opened job postings</h3>
                @forelse ($job_postings->whereIn('status', ['Active', 'Pending']) as $job_posting)                
                    @include('recruiter.active_pending_jp_partial')
                @empty
                    <p>You don't currently have any active or pending job postings.</p>
                @endforelse
            </div>

            {{-- Expired --}}
            <div class='expired-jps'>
                <h3>Your expired job postings</h3>
                @forelse ($job_postings->where('status', 'Expired') as $job_posting)
                    @include('recruiter.expired_jp_partial')
                @empty
                    <p>You don't currently have any expired job postings.</p>
                @endforelse
            </div>

            {{-- Closed --}}
            <div class='closed-jps'>
                <h3>Your closed job postings</h3>
                @forelse ($job_postings->where('status', 'Closed') as $job_posting)
                    @include('recruiter.closed_jp_partial')
                @empty
                    <p>You don't currently have any closed job postings.</p>
                @endforelse
            </div>
        </div>

        <div class="dashboard-see-statistics" style="margin: 1rem 0rem;">
            <a class="button btn btn-primary" style="background-color: #1c4eb1eb; padding: 0.5rem 0.4rem;" href="{{ route('recruiter-dashboard.statistics') }}" class="a-as-button">My statistics</a>
        </div>


    {{-- VIEW: MANAGE STAFF --}}

    @elseif(isset($companyStaff) && isset($departments))
        
        <div class="staff-management" style="margin-top: 2rem;">
            
            {{-- 1. FORM: Promote User --}}
            <div class="card p-4 mb-4" style="background: #f8f9fa; padding: 20px; border-radius: 8px; border: 1px solid #ddd;">
                <h4 style="margin-bottom: 0.5rem;">Add New Recruiter</h4>
                <p class="text-muted small" style="margin-bottom: 1rem;">Enter the email of a user and assign them to a department to promote them.</p>
                
                <form action="{{ route('recruiter.promote') }}" method="POST" style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;">
                    @csrf
                    
                    {{-- Email Input --}}
                    <div style="flex-grow: 1; min-width: 250px;">
                        <label for="email" style="font-weight: bold; font-size: 0.9rem;">User Email</label>
                        <input type="email" name="email" id="email" required placeholder="user@example.com" class="form-control" style="width: 100%;">
                    </div>

                    {{-- Department Select --}}
                    <div style="flex-grow: 1; min-width: 250px;">
                        <label for="department_id" style="font-weight: bold; font-size: 0.9rem;">Assign Department</label>
                        <select name="department_id" id="department_id" required class="form-control" style="width: 100%; background-color: white;">
                            <option value="" disabled selected>Select a Department...</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Submit Button --}}
                    <div style="margin-bottom: 2px;">
                        <button type="submit" class="button btn-primary" style="background-color: #1c4eb1eb; border: none;">Promote User</button>
                    </div>
                </form>
            </div>

            {{-- 2. TABLE: Staff List --}}
            <h3>Current Recruiters</h3>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Department</th>
                            <th>Role</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($companyStaff as $staff)
                            <tr>
                                {{-- Accessing 'user' relation to match your Model --}}
                                <td>{{ $staff->user->name }}</td>
                                <td>{{ $staff->user->email }}</td>
                                <td>
                                    {{ $staff->department ? $staff->department->name : 'N/A' }}
                                </td>
                                <td>
                                    @if($staff->is_company_manager)
                                        <span class="badge" style="background-color: #d1e7dd; color: #0f5132; padding: 0.25em 0.6em; border-radius: 4px; font-weight: bold;">Manager</span>
                                    @else
                                        <span class="badge" style="background-color: #e2e3e5; color: #41464b; padding: 0.25em 0.6em; border-radius: 4px;">Recruiter</span>
                                    @endif
                                </td>
                                <td>
                                    @if($staff->registered_user_id !== Auth::id())
                                        <form action="{{ route('recruiter.demote', $staff->registered_user_id) }}" method="POST" onsubmit="return confirm('Are you sure? This user will lose recruiter access and become a Job Seeker.');" style="margin: 0;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="button button-outline" style="border: 1px solid #dc3545; color: #dc3545; background: transparent; padding: 0.2rem 0.6rem; font-size: 0.8rem; height: auto; line-height: 1.5;">
                                                Demote
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-muted" style="font-size: 0.9rem;">(You)</span>
                                    @endif
                                </td>

                                <td>
                                    @if($staff->registered_user_id !== Auth::id())
                                        @if(!$staff->is_company_manager)
                                            <form action="{{ route('messages.index', $staff->registered_user_id) }}" method="GET" style="display: inline-block;">
                                                <button type="submit" class="button btn-primary" style="background-color: #1c4eb1eb;">
                                                    Send Message
                                                </button>
                                            </form>
                                        @endif
                                    @else
                                        <span class="text-muted" style="font-size: 0.9rem;">(You)</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif



    <hr style="margin-top: 3rem;">
    <div class="delete-account-container">
        <h2 style="color: #dc3545;">Delete Account</h2>
        
        @if(session('error'))
            <div style="color: #dc3545">
                {{ session('error') }}
            </div>
        @endif

        <p>
            <strong>Warning:</strong> Deleting your account is permanent. 
            All your <strong>Active</strong> and <strong>Pending</strong> job postings will be automatically <strong>Closed</strong>. 
            Your personal data will be anonymized, but the job history will remain associated with the company.
        </p>
        
        <form action="{{ route('recruiter.dashboard.destroy') }}" method="POST">
            @csrf
            @method('DELETE')

            @if(is_null(Auth::user()->google_id))
                {{-- CENÁRIO NORMAL: Pede Password --}}
                <div class="form-group" style="margin-bottom: 1rem;">
                    <label for="password_delete">Confirm Password to delete:</label>
                    <input type="password" id="password_delete" name="password" required class="form-control" style="max-width: 400px;">
                    
                    @error('password')
                        <div style="color: #dc3545;">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            @else
                {{-- CENÁRIO GOOGLE: Aviso informativo --}}
                <div>
                    <strong>Note:</strong> Since you logged in via Google, you don't need to enter a password to delete your account.
                </div>
            @endif
            
            <button type="submit" 
                    class="button" 
                    style="background-color: #dc3545; border-color: #dc3545; color: white;"
                    onclick="return confirm('Are you sure? This will close all your active jobs and delete your account.');">
                Delete Account
            </button>
        </form>
    </div>
</section>

@endsection