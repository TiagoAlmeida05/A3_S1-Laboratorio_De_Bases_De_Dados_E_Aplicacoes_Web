@extends('layouts.app')

@section('title', 'My dashboard' . ' | ' . config('app.name'))

@section('content')
<section id="recruiter-dashboard">
    <h2>{{ $user->name }}{{Str::endsWith($user->name, 's') ? '\'' : "'s"}} Recruiter Dashboard</h2>
    <div class="dashboard-create-job" style="margin: 1rem 0rem;">
        <a class="button btn btn-primary" style="background-color: #1c4eb1eb; padding: 0.5rem 0.4rem;" href="{{ route('job_postings.create') }}" class="a-as-button">Create new job posting</a>
    </div>
    <div class='job-postings'>
        <div class='non-closed-jps'>
            <h3>Your opened job postings</h3>
            @each('recruiter.non_closed_jp_partial', $job_postings, 'job_posting')
        </div>
        <div class='closed-jps'>
            <h3>Your closed job postings</h3>
            @each('recruiter.closed_jp_partial', $job_postings, 'job_posting')
        </div>
    </div>
</section>

@endsection
