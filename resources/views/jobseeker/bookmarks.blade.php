@extends('layouts.app')

@section('title', 'My Bookmarked Jobs')

@section('content')
<div class="container p-4 mx-auto bookmarks-container">
    <h2 class="mb-4">My Bookmarked Jobs</h2>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @forelse ($bookmarks as $job)
        <div class="card mb-3">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1">
                        <a href="{{ route('job_postings.show', $job->id) }}" class="text-decoration-none">{{ $job->title }}</a>
                    </h5>
                    <p class="mb-1 text-muted">
                        {{ $job->recruiter?->department?->company?->name ?? 'Unknown Company' }} · {{ $job->city?->name ?? 'Unknown City' }}
                    </p>
                    <small class="text-muted">
                        <strong>Deadline:</strong> {{ \Carbon\Carbon::parse($job->deadline)->format('d/m/Y') }}
                    </small>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('job_postings.show', $job->id) }}" class="btn btn-primary btn-sm">View job posting</a>
                    <form method="POST" action="{{ route('bookmarks.destroy', $job->id) }}">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm">Remove bookmark</button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="card">
            <div class="card-body text-center py-4">
                <p class="text-muted mb-0">You haven't bookmarked any jobs yet.</p>
            </div>
        </div>
    @endforelse

    <div class="mt-4">
        {{ $bookmarks->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection