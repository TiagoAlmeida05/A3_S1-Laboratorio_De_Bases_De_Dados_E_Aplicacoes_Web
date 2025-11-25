@extends('layouts.app')

@section('title', 'My dashboard' . ' | ' . config('app.name'))

@section('content')
<section id="recruiter-dashboard">
    <h1>{{ $user->name }}{{Str::endsWith($user->name, 's') ? '\'' : "'s"}} Recruiter Dashboard</h1>
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
