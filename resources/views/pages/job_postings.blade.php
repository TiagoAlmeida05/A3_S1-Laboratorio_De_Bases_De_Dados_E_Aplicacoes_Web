@extends('layouts.app')

@section('title', 'Homepage | ' . config('app.name'))

@section('content')
<section id="job-postings">
    <h2>All available Jobs</h2>
    {{-- Render each job posting using the partial --}}
    @each('partials.job_posting', $job_postings, 'job_posting')
</section>

@endsection