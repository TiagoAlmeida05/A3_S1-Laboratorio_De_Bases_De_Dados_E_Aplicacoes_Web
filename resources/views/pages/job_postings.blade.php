@extends('layouts.app')

@section('title', 'All job postings! | ' . config('app.name'))

@section('content')
<section id="job-postings">
    <h2>History of job postings</h2>
    {{-- Render each job posting using the partial --}}
    @each('partials.job_posting', $job_postings, 'job_posting')
</section>

@endsection