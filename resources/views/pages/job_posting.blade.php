@extends('layouts.app')

@section('title', $job_posting->title . ' | ' . config('app.name'))

@section('content')
<section id="job_posting">
    @auth
        @if($jobSeeker)
            <a class="button" href="{{ route('jobseeker.apply', $job_posting->id) }}">Apply</a>
        @endif
    @endauth
    <h1>{{ $job_posting->title }}</h1>
    <p>Description:{{ $job_posting->description }}</p>
    <p>Requirements: {{ $job_posting->requirements ?? 'N/A' }}</p>
    <p>City: {{ $job_posting->city->name ?? 'N/A' }}</p>
    <p>Minimum wage: {{ $job_posting->min_wage ?? 'N/A' }}</p>
    <p>Maximum wage: {{ $job_posting->max_wage ?? 'N/A' }}</p>
    <p>Deadline:{{ $job_posting->deadline }}</p>
</section>

@endsection