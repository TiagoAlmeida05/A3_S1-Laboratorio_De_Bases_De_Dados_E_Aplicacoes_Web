@extends('layouts.app')

@section('title', 'My Applications | ' . config('app.name'))

@section('content')
<section>
    <h1>My Applications</h1>

    @if(session('success'))
        <div>
            {{ session('success') }}
        </div>
    @endif

    @if($applications->isEmpty())
        <div>
            You haven't applied to any jobs yet.
            <a href="{{ route('job_postings.index') }}">Browse jobs</a>
        </div>
    @else
        <div>
            @foreach($applications as $application)
            <div>
                <div>
                    <h3>
                        <a href="{{ route('job_postings.show', $application->job_posting_id) }}">{{ $application->jobPosting->title }}</a>
                    </h3>
                    <span>
                        @if($application->evaluated)
                            @if($application->accepted)
                                <span>Accepted</span>
                            @else
                                <span>Rejected</span>
                            @endif
                        @else
                            <span>Pending</span>
                        @endif
                    </span>
                </div>
                
                <div>
                    <p><strong>Company:</strong> {{ $application->jobPosting->company->name ?? 'N/A' }}</p>
                    <p><strong>City:</strong> {{ $application->jobPosting->city->name ?? 'Not specified' }}</p>
                    <p><strong>Applied on:</strong> {{ $application->date->format('d/m/Y H:i') }}</p>
                    
                    <div class="application-files">
                        @if($application->cv)
                            <span>
                                <a href="{{ asset('storage/' . $application->cv) }}" target="_blank">CV</a>
                            </span>
                        @endif
                        @if($application->cover_letter)
                            <span>
                                <a href="{{ asset('storage/' . $application->cover_letter) }}" target="_blank">Cover Letter</a>
                            </span>
                        @endif
                        @if($application->recommendation_letter)
                            <span>
                                <a href="{{ asset('storage/' . $application->recommendation_letter) }}" target="_blank">Recommendation Letter</a>
                            </span>
                        @endif
                    </div>
                </div>
                
                <div>
                    <a href="{{ route('applications.edit', $application->id) }}">
                        Edit Application
                    </a>
                    
                    <button data-bs-toggle="modal" data-bs-target="#cancelModal{{ $application->id }}">
                        Cancel Application
                    </button>
                </div>
                
                <div class="modal fade" id="cancelModal{{ $application->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div>
                                <h5>Cancel Application</h5>
                            </div>
                            <div>
                                Are you sure you want to cancel your application for 
                                "{{ $application->jobPosting->title }}"?
                            </div>
                            <div>
                                <button>No, Keep It</button>
                                <form action="{{ route('applications.cancel', $application->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button>Yes, Cancel Application</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
            
            {{ $applications->links() }}
        </div>
    @endif
</section>
@endsection