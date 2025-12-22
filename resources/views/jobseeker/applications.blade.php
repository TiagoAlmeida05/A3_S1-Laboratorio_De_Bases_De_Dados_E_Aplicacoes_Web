@extends('layouts.app')

@section('title', 'My Applications | ' . config('app.name'))

@section('content')
<div class="container p-4 mx-auto applications-container">
    <h2 class="mb-4">My Applications</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @php
        $groups = [
            ['title' => 'Pending', 'items' => $pending, 'badge' => 'bg-warning text-dark'],
            ['title' => 'Accepted', 'items' => $accepted, 'badge' => 'bg-success'],
            ['title' => 'Rejected', 'items' => $rejected, 'badge' => 'bg-danger']
        ];
    @endphp

    @if($pending->isEmpty() && $accepted->isEmpty() && $rejected->isEmpty())
        <div class="card">
            <div class="card-body text-center py-4">
                <p class="text-muted mb-0">You haven't applied to any jobs yet. <a href="{{ route('job_postings.index') }}" class="text-primary">Browse jobs</a></p>
            </div>
        </div>
    @else
        @foreach($groups as $group)
            @if($group['items']->isNotEmpty())
                <h3 class="h5 mb-3 mt-4 border-bottom pb-2">{{ $group['title'] }} ({{ $group['items']->count() }})</h3>
                
                @foreach($group['items'] as $application)
                    <div class="card mb-3">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-1">
                                    <a href="{{ route('job_postings.show', $application->job_posting_id) }}" class="text-decoration-none">{{ $application->jobPosting->title }}</a>
                                    <span class="badge {{ $group['badge'] }} ms-2">
                                        @if($application->evaluated)
                                            {{ $application->accepted ? 'Accepted' : 'Rejected' }}
                                        @else
                                            Pending
                                        @endif
                                    </span>
                                </h5>
                                
                                <p class="mb-1"><strong>Company</strong>: {{ $application->jobPosting->company->name ?? 'N/A' }}</p>
                                <p class="mb-1"><strong>City</strong>: {{ $application->jobPosting->city->name ?? 'Not specified' }}</p>
                                <p class="mb-1"><strong>Applied on</strong>: {{ $application->date->format('d/m/Y') }}</p>
                                
                                @if($application->cover_letter || $application->recommendation_letter)
                                    <div class="d-flex gap-2 mt-2">
                                        @if($application->cover_letter)
                                            <a href="{{ asset('storage/' . $application->cover_letter) }}" target="_blank" class="btn btn-outline-primary btn-sm">Cover letter</a>
                                        @endif
                                        @if($application->recommendation_letter)
                                            <a href="{{ asset('storage/' . $application->recommendation_letter) }}" target="_blank" class="btn btn-outline-primary btn-sm">Recommendation letter</a>
                                        @endif
                                    </div>
                                @endif
                            </div>
                            
                            @if($application->jobPosting->status !== 'Closed')
                                <div class="d-flex gap-2">
                                    <a href="{{ route('applications.edit', $application->id) }}" class="btn btn-primary btn-sm">Edit application</a>
                                    <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#cancelModal{{ $application->id }}">Cancel application</button>
                                </div>

                                <div class="modal fade" id="cancelModal{{ $application->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Cancel application</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                Are you sure you want to cancel your application for "{{ $application->jobPosting->title }}"?
                                            </div>
                                            <div class="modal-footer">
                                                <button class="btn btn-secondary" data-bs-dismiss="modal">No, keep it</button>
                                                <form action="{{ route('applications.cancel', $application->id) }}" method="POST">
                                                    @csrf 
                                                    @method('DELETE')
                                                    <button class="btn btn-danger">Yes, cancel application</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <p class="text-muted fst-italic mb-0">
                                    This job is closed.
                                </p>
                            @endif
                        </div>
                    </div>
                @endforeach
            @endif
        @endforeach
    @endif
</div>
@endsection