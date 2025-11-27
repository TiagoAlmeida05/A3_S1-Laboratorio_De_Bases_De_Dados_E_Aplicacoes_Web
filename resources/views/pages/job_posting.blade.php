@extends('layouts.app')

@section('title', $job_posting->title . ' | ' . config('app.name'))

@section('content')
<section id="job_posting">
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
    <div class="d-flex flex-column" style="margin-bottom: 1rem;">
        <h2 class="align-self-center" style="margin: 2rem 0rem;">{{ $job_posting->title }}</h2>
        <p><strong>Description</strong>: {{ $job_posting->description }}</p>
        <p><strong>Requirements</strong>:  {{ $job_posting->requirements ?? 'N/A' }}</p>
        <p><strong>City</strong>: {{ $job_posting->city->name ?? 'N/A' }}</p>
        <p><strong>Minimum wage</strong>: {{ $job_posting->min_wage ?? 'N/A' }}</p>
        <p><strong>Maximum wage</strong>: {{ $job_posting->max_wage ?? 'N/A' }}</p>
        <p><strong>Deadline</strong>: {{ \Carbon\Carbon::parse($job_posting->deadline)->format('d-m-Y') }}</p>
    </div>

    <a class="btn btn-primary" style="background-color: #1c4eb1eb;" href="{{ url('/') }}">Go back</a>

</section>

@endsection