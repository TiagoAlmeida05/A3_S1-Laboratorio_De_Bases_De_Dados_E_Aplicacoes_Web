@extends('layouts.app')

@section('title', 'My Applications | ' . config('app.name'))

@section('content')
<section>
    <h1>My Applications</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @php
        // Prepare groups to loop through easily in one go
        $groups = [
            ['title' => 'Pending', 'items' => $pending, 'color' => '#d39e00'],
            ['title' => 'Accepted', 'items' => $accepted, 'color' => '#28a745'],
            ['title' => 'Rejected', 'items' => $rejected, 'color' => '#dc3545']
        ];
    @endphp

    @if($pending->isEmpty() && $accepted->isEmpty() && $rejected->isEmpty())
        <div>
            You haven't applied to any jobs yet. <a href="{{ route('job_postings.index') }}">Browse jobs</a>
        </div>
    @else
        @foreach($groups as $group)
            @if($group['items']->isNotEmpty())
                <h3 style="margin-top: 30px; color: {{ $group['color'] }};">{{ $group['title'] }}</h3>
                
                @foreach($group['items'] as $application)
                <div style="margin-bottom: 20px; border-bottom: 1px solid #ccc; padding-bottom: 10px;">
                    <div>
                        <h3><a href="{{ route('job_postings.show', $application->job_posting_id) }}">{{ $application->jobPosting->title }}</a></h3>
                        <span>
                            @if($application->evaluated)
                                {{ $application->accepted ? 'Accepted' : 'Rejected' }}
                            @else
                                Pending
                            @endif
                        </span>
                    </div>
                    
                    <div>
                        <p><strong>Company:</strong> {{ $application->jobPosting->company->name ?? 'N/A' }}</p>
                        <p><strong>City:</strong> {{ $application->jobPosting->city->name ?? 'Not specified' }}</p>
                        <p><strong>Applied on:</strong> {{ $application->date->format('d/m/Y H:i') }}</p>
                        
                        <div class="application-files">
                            @if($application->cover_letter)
                                <span><a href="{{ asset('storage/' . $application->cover_letter) }}" target="_blank">Cover Letter</a></span>
                            @endif
                            @if($application->recommendation_letter)
                                <span><a href="{{ asset('storage/' . $application->recommendation_letter) }}" target="_blank">Recommendation Letter</a></span>
                            @endif
                        </div>
                    </div>
                    
                    {{-- Only show Edit/Cancel if Job is NOT closed --}}
                    <div>
                        @if($application->jobPosting->status !== 'Closed')
                            <a href="{{ route('applications.edit', $application->id) }}">Edit Application</a>
                            
                            <button data-bs-toggle="modal" data-bs-target="#cancelModal{{ $application->id }}">Cancel Application</button>

                            {{-- Modal --}}
                            <div class="modal fade" id="cancelModal{{ $application->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div><h5>Cancel Application</h5></div>
                                        <div>Are you sure you want to cancel your application for "{{ $application->jobPosting->title }}"?</div>
                                        <div>
                                            <button data-bs-dismiss="modal">No, Keep It</button>
                                            <form action="{{ route('applications.cancel', $application->id) }}" method="POST" style="display:inline;">
                                                @csrf @method('DELETE')
                                                <button>Yes, Cancel Application</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            {{-- Message when closed --}}
                            <span style="color: gray; font-style: italic;">
                                This job is closed. You cannot edit or cancel this application.
                            </span>
                        @endif
                    </div>
                </div>
                @endforeach
            @endif
        @endforeach
    @endif
</section>
@endsection