@extends('layouts.app')

@section('title', 'Application details | ' . config('app.name'))

@section('content')
<section id="view-application">
    <h2>Application details</h2>
    <p><strong>Job posting</strong>: <a href="{{ route('job_postings.show', $application->jobPosting->id) }}">{{ $application->jobPosting->title }}</a></p>

    <div>
        <h3>Applicant information</h3>
        <p><strong>Name</strong>: <a href="{{ route('jobseeker.profile', $application->jobSeeker->registered_user_id) }}">{{ $application->jobSeeker->registeredUser->name }}</a></p>
        <p><strong>E-mail</strong>: {{ $application->jobSeeker->registeredUser->email }}</p>
        @if($application->jobSeeker->website)
            <p><strong>Website</strong>: <a href="{{ $application->jobSeeker->website }}" target="_blank">{{ $application->jobSeeker->website }}</a></p>
        @endif
        @if($application->jobSeeker->cv && $application->jobSeeker->show_cv)
            <p><strong>CV:</strong> <a href="{{ asset('storage/' . $application->jobSeeker->cv) }}" target="_blank">View CV</a></p>
        @endif
    </div>

    <div>
        <h3>Application details</h3>
        <p><strong>Application date</strong>: {{ $application->date->format('d-m-Y @ H:i') }}</p>
        <p><strong>Status</strong>:
            @if($application->evaluated)
                @if($application->accepted)
                    <span>Accepted</span>
                @else
                    <span>Rejected</span>
                @endif
            @else
                <span>Pending evaluation</span>
            @endif
        </p>
    </div>

    @if($application->cover_letter)
        <div>
            <h3>Cover letter</h3>
            <a href="{{ asset('storage/' . $application->cover_letter) }}" target="_blank">See Cover Letter</a>
        </div>
    @endif

    @if($application->recommendation_letter)
        <div>
            <h3>Recommendation letter</h3>
            <a href="{{ asset('storage/' . $application->recommendation_letter) }}" target="_blank">See Recommendation Letter</a>
        </div>
    @endif

    <div>
        <a href="{{ route('jobseeker.profile', $application->jobSeeker->registered_user_id) }}" class="button btn btn-primary" style="background-color: #1c4eb1eb; padding: 0.5rem 0.4rem;">View applicant profile</a>
        <a href="{{ route('job_postings.manage-applications', $application->jobPosting->id) }}" class="button btn btn-primary" style="background-color: #1c4eb1eb; padding: 0.5rem 0.4rem;">All applications</a>
    </div>

    @auth
        @if(Auth::user()->isRecruiter())
            <a href="{{ route('reports.create', ['type' => 'Application', 'entity_id' => $application->id]) }}" class="button btn btn-primary" style="background-color: #1c4eb1eb; padding: 0.5rem 0.4rem;">Report application</a>
        @endif
    @endauth
</section>
@endsection