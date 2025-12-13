@extends('layouts.app')

@section('title', $job_posting->title . ': view applications | ' . config('app.name'))

@section('content')
<section id="manage-applications">
    <h2> Applications to the {{ $job_posting->title }} job posting (closed) </h2>
    
    <div style="margin: 1rem 0;">
        <p><strong>Status:</strong> {{ $job_posting->status }}</p>
        <p><strong>Number of applications:</strong> {{ $applications->count() }}</p>
    </div>

    @if($applications->isEmpty())
        <p>No applications were submitted for this job posting.</p>
        <a href="{{ route('recruiter-dashboard.index') }}" class="button btn btn-primary" style="background-color: #1c4eb1eb; padding: 0.5rem 0.4rem;">Return to my dashboard</a>
    @else
        <form id="applicant-selection-form" style="padding: 3rem; width: 40rem; border-color: #4856e7ff;" method="POST" action="{{ route('job_postings.submit-application-selection', $job_posting->id) }}" id="application-selection-form">
            @csrf
            
            <table>
                <thead>
                    <tr>
                        <th>Applicant name</th>
                        <th>Application date</th>
                        <th>Cover letter?</th>
                        <th>Recommendation letter?</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($applications as $application)
                        <tr>
                            <td><a href="{{ route('jobseeker.profile', $application->jobSeeker->registered_user_id) }}">{{ $application->jobSeeker->registeredUser->name }}</a></td>
                            <td>{{ $application->date->format('d-m-Y') }}</td>
                            <td>{{ $application->cover_letter ? 'Yes' : 'No' }}</td>
                            <td>{{ $application->recommendation_letter ? 'Yes' : 'No' }}</td>
                            <td>
                                @if($application->evaluated)
                                    @if($application->accepted)
                                        <span>Accepted</span>
                                    @else
                                        <span>Rejected</span>
                                    @endif
                                @else
                                    <span>Pending evaluation</span>
                                @endif
                            </td>
                            <td><a href="{{ route('job_postings.view-application-closed-job', $application->id) }}" class="button btn btn-primary" style="background-color: #1c4eb1eb; padding: 0.5rem 0.4rem;">See full application</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </form>
    @endif
</section>

@endsection
