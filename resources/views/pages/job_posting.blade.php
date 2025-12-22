@extends('layouts.app')

@section('title', $job_posting->title . ' | ' . config('app.name'))

@section('content')
<section id="job_posting">
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

    <div class="card mx-auto jp-card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center" style="margin-bottom: 1rem;">
                @if(auth()->check() && auth()->user()->isJobSeeker())
                    @php
                        $isBookmarked = \DB::table('bookmark')->where('job_seeker_id', auth()->id())->where('job_posting_id', $job_posting->id)->where('is_active', true)->exists();
                    @endphp

                    @if($isBookmarked)
                        <form method="POST" action="{{ route('bookmarks.destroy', $job_posting->id) }}">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-primary">Remove Bookmark</button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('bookmarks.store', $job_posting->id) }}">
                            @csrf
                            <button class="btn btn-primary">Bookmark Job</button>
                        </form>
                    @endif
                @else
                    <div></div>
                @endif

                <h2 class="mb-0">{{ $job_posting->title }}</h2>

                @if(auth()->check() && auth()->user()->isJobSeeker())
                    <a class="btn btn-primary" href="{{ route('messages.index', $recruiter->registered_user_id) }}">Message recruiter</a>
                @else
                    <div></div>
                @endif
            </div>

            <p><strong>Company</strong>: {{ $job_posting->recruiter->department->company->name ?? 'N/A' }}</p>
            <p><strong>Department</strong>: {{ $job_posting->recruiter->department->name ?? 'N/A' }}</p>
            <p><strong>Description</strong>: {{ $job_posting->description }}</p>
            <p><strong>Tags</strong>: 
                @if($job_posting->tags->isNotEmpty())
                    @foreach($job_posting->tags as $tag)
                        <span class="badge">{{ $tag->name }}</span>
                    @endforeach
                @else
                    N/A
                @endif
            </p>
            <p><strong>Requirements</strong>:  {{ $job_posting->requirements ?? 'N/A' }}</p>
            <p><strong>City</strong>: {{ $job_posting->city->name ?? 'N/A' }}</p>
            <p><strong>Minimum wage</strong>: {{ $job_posting->min_wage ?? 'N/A' }}</p>
            <p><strong>Maximum wage</strong>: {{ $job_posting->max_wage ?? 'N/A' }}</p>
            <p><strong>Deadline</strong>: {{ \Carbon\Carbon::parse($job_posting->deadline)->format('d/m/Y') }}</p>
        </div>
    </div>

    <div class="jp-buttons d-flex justify-content-center mt-3 gap-2">
        <a class="btn btn-primary" href="{{ url()->previous() }}">Go back</a>
        @auth
            @if(Auth::user()->isJobSeeker())
                @if($hasApplied)
                    <button class="btn btn-secondary" disabled>Applied</button>
                @else
                    <a class="btn btn-primary apply-button" href="{{ route('jobseeker.apply', $job_posting->id) }}">Apply</a>
                @endif
            @endif
            @php
                $isOwnPosting = Auth::user()->isRecruiter() && $job_posting->recruiter_id == Auth::id();
            @endphp
            @if(!Auth::user()->isAdmin() && !$isOwnPosting)
                <a href="{{ route('reports.create', ['type' => 'JobPosting', 'entity_id' => $job_posting->id]) }}" class="btn btn-danger">Report job posting</a>
            @endif
        @endauth
    </div>

</section>

@endsection