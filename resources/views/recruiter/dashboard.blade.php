@extends('layouts.app')

@section('title', 'My dashboard' . ' | ' . config('app.name'))

@section('content')
<section id="recruiter-dashboard">
    <h2>{{ $user->name }}{{Str::endsWith($user->name, 's') ? '\'' : "'s"}} Recruiter Dashboard</h2>
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

    <hr>
    <div class="delete-account-container">
        <h2>Delete Account</h2>
        <p>
            <strong>Warning:</strong> Deleting your account is permanent. 
            All your <strong>Active</strong> and <strong>Pending</strong> job postings will be automatically <strong>Closed</strong>. 
            Your personal data will be anonymized, but the job history will remain associated with the company.
        </p>
        
        <form action="{{ route('recruiter.dashboard.destroy') }}" method="POST">
            @csrf
            @method('DELETE')

            <div class="form-group">
                <label for="password_delete">Confirm Password to delete:</label>
                <input type="password" id="password_delete" name="password" required class="form-control">
            </div>
            
            <button type="submit" 
                    class="btn btn-danger" 
                    onclick="return confirm('Are you sure? This will close all your active jobs and delete your account.');">
                Delete Account
            </button>
        </form>
    </div>
</section>

@endsection
