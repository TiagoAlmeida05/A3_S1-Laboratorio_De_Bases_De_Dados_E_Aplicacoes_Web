@extends('layouts.app')

@section('title', $job_posting->title . ' | ' . config('app.name'))

@section('content')
<section id="job_posting">
    <a href="{{ url('/') }}">Go back</a>
    @auth
        @if($jobSeeker)
            <a class="button" href="{{ route('jobseeker.apply', $job_posting->id) }}">Apply</a>
        @endif
    @endauth

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    <h1>{{ $job_posting->title }}</h1>
    <p><strong>Description</strong>: {{ $job_posting->description }}</p>
    <p><strong>Requirements</strong>:  {{ $job_posting->requirements ?? 'N/A' }}</p>
    <p><strong>City</strong>: {{ $job_posting->city->name ?? 'N/A' }}</p>
    <p><strong>Minimum wage</strong>: {{ $job_posting->min_wage ?? 'N/A' }}</p>
    <p><strong>Maximum wage</strong>: {{ $job_posting->max_wage ?? 'N/A' }}</p>
    <p><strong>Deadline</strong>: {{ \Carbon\Carbon::parse($job_posting->deadline)->format('d-m-Y') }}</p>
</section>

@endsection