@extends('layouts.app')

@section('title', 'My Bookmarked Jobs')

@section('content')
<div>
    <h2>My Bookmarked Jobs</h2>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @forelse ($bookmarks as $job)
        <div>
            <div>
                <div>
                    <div>
                        <h5>
                            {{ $job->title }}
                        </h5>

                        <p>
                            {{ $job->recruiter?->department?->company?->name ?? 'Unknown Company' }}
                            · {{ $job->city?->name ?? 'Unknown City' }}
                        </p>


                        <p>
                            <strong>Deadline:</strong>
                            {{ \Carbon\Carbon::parse($job->deadline)->format('d M Y') }}
                        </p>
                    </div>

                    <form method="POST" action="{{ route('bookmarks.destroy', $job->id) }}">
                        @csrf
                        @method('DELETE')
                        <button>
                            Remove
                        </button>
                    </form>
                </div>

                <a href="{{ route('job_postings.show', $job->id) }}">View Job</a>
            </div>
        </div>
    @empty
        <div>
            You haven’t bookmarked any jobs yet.
        </div>
    @endforelse

    <div>
        {{ $bookmarks->links() }}
    </div>
</div>
@endsection
