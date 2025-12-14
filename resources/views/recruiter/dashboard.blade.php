@extends('layouts.app')

@section('title', 'My dashboard' . ' | ' . config('app.name'))

@section('content')
<section id="recruiter-dashboard">
    <h2>{{ $user->name }}{{Str::endsWith($user->name, 's') ? '\'' : "'s"}} Recruiter Dashboard</h2>
    {{-- MANAGER TABS --}}
    @if(Auth::user()->recruiter->is_company_manager)
        <div class="mb-4" style="margin-top: 20px; border-bottom: 1px solid #ddd;">
            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" style="display: flex; gap: 1rem; list-style: none; padding: 0;">               
                {{-- TAB 1: MY JOBS --}}
                <li class="mr-2">
                    {{-- Check request('view') instead of $viewMode --}}
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
            </ul>
        </div>
    @endif
    <div class="dashboard-create-job" style="margin: 1rem 0rem;">
        <a class="button btn btn-primary" style="background-color: #1c4eb1eb; padding: 0.5rem 0.4rem;" href="{{ route('job_postings.create') }}" class="a-as-button">Create new job posting</a>
    </div>
    <div class='job-postings'>
        <div class='active-pending-jps'>
            <h3>Your opened job postings</h3>
            @forelse ($job_postings->whereIn('status', ['Active', 'Pending']) as $job_posting)                
                @include('recruiter.active_pending_jp_partial')
            @empty
                <p>You don't currently have any active or pending job postings.</p>
            @endforelse
        </div>
        <div class='expired-jps'>
            <h3>Your expired job postings</h3>
            @forelse ($job_postings->where('status', 'Expired') as $job_posting)
                @include('recruiter.expired_jp_partial')
            @empty
                <p>You don't currently have any expired job postings.</p>
            @endforelse
        </div>
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
</section>

@endsection
