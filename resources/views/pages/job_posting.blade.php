@extends('layouts.app')

@section('title', $job_posting->title . ' | ' . config('app.name'))

@section('content')
<section id="job_posting">
    @if(auth()->check() && auth()->user()->isJobSeeker())
        @php
            $isBookmarked = \DB::table('bookmark')->where('job_seeker_id', auth()->id())->where('job_posting_id', $job_posting->id)->where('is_active', true)->exists();
        @endphp

        @if($isBookmarked)
            <form method="POST" action="{{ route('bookmarks.destroy', $job_posting->id) }}">
                @csrf
                @method('DELETE')
                <button>Remove Bookmark</button>
            </form>
        @else
            <form method="POST" action="{{ route('bookmarks.store', $job_posting->id) }}">
                @csrf
                <button>Bookmark Job</button>
            </form>
        @endif
    @endif

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
        <p><strong>Company</strong>: {{ $job_posting->recruiter->department->company->name ?? 'N/A' }}</p>
        <p><strong>Department</strong>: {{ $job_posting->recruiter->department->name ?? 'N/A' }}</p>
        <p><strong>Description</strong>: {{ $job_posting->description }}</p>
        <p><strong>Tags</strong>: {{ $job_posting->tags->pluck('name')->join(', ') ?: 'N/A' }}</p>
        <p><strong>Requirements</strong>:  {{ $job_posting->requirements ?? 'N/A' }}</p>
        <p><strong>City</strong>: {{ $job_posting->city->name ?? 'N/A' }}</p>
        <p><strong>Minimum wage</strong>: {{ $job_posting->min_wage ?? 'N/A' }}</p>
        <p><strong>Maximum wage</strong>: {{ $job_posting->max_wage ?? 'N/A' }}</p>
        <p><strong>Deadline</strong>: {{ \Carbon\Carbon::parse($job_posting->deadline)->format('d-m-Y') }}</p>
    </div>

    <div class="jp-buttons d-flex">
        <a class="button btn btn-primary" style="background-color: #1c4eb1eb; padding: 0.5rem 0.4rem;" href="{{ url('/') }}">Go back</a>
        @auth
            @if(Auth::user()->isJobSeeker())
                @if($hasApplied)
                    <button style="background-color: gray" disabled>Applied</button>
                @else
                    <a class="button btn btn-primary" style="background-color: #3f9236eb;  padding: 0.5rem 0.4rem;" href="{{ route('jobseeker.apply', $job_posting->id) }}">Apply</a>
                @endif
            @endif
        @endauth
    </div>

</section>

@endsection