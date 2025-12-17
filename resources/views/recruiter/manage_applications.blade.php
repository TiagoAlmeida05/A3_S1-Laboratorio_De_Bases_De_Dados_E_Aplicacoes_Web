@extends('layouts.app')

@section('title', $job_posting->title . ': manage applications | ' . config('app.name'))

@section('content')
<section id="manage-applications">
    <h2> Applications to the {{ $job_posting->title }} job posting</h2>
    
    <div style="margin: 1rem 0;">
        <p><strong>Status:</strong> {{ $job_posting->status }}</p>
        <p><strong>Number of applications:</strong> {{ $applications->count() }}</p>
    </div>

    @if($applications->isEmpty())
        <p>No applications have been submitted for this job posting yet.</p>
        <a href="{{ route('recruiter-dashboard.index') }}" class="button btn btn-primary" style="background-color: #1c4eb1eb; padding: 0.5rem 0.4rem;">Return to my dashboard</a>
    @else
        <form id="applicant-selection-form" style="padding: 3rem; width: 40rem; border-color: #4856e7ff;" method="POST" action="{{ route('job_postings.submit-application-selection', $job_posting->id) }}">
            @csrf
            
            <table>
                <thead>
                    <tr>
                        <th>Applicant name</th>
                        <th>Application date</th>
                        <th>Cover letter?</th>
                        <th>Recommendation letter?</th>
                        <th>Status</th>
                        <th></th>
                        @if($selectMode)
                            <th>Select</th>
                        @endif
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
                            <td><a href="{{ route('applications.view-application', $application->id) }}" class="button btn btn-primary" style="background-color: #1c4eb1eb; padding: 0.5rem 0.4rem;">See full application</a></td>
                            @if($selectMode)
                                <td><input type="checkbox" name="selected_applications[]" value="{{ $application->id }}"></td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div id="dynamic-manage-applications-buttons">
                @if($selectMode)
                    <button type="submit" id="submit-applicant-selection-button" class="button btn btn-primary" style="background-color: #1c4eb1eb; padding: 0.5rem 0.4rem;">Submit selection and close job posting</button>
                    <a href="{{ route('job_postings.manage-applications', $job_posting->id) }}" class="button btn btn-primary" style="background-color: #1c4eb1eb; padding: 0.5rem 0.4rem;">Cancel</a>
                @else
                    <a href="{{ route('job_postings.manage-applications', [$job_posting->id, 'selectMode' => 1]) }}" class="button btn btn-primary" style="background-color: #1c4eb1eb; padding: 0.5rem 0.4rem;"> Select applicants</a>
                    <a href="{{ route('recruiter-dashboard.index') }}" class="button btn btn-primary" style="background-color: #1c4eb1eb; padding: 0.5rem 0.4rem;">Return to my dashboard</a>
                @endif
            </div>
        </form>
    @endif
</section>

@endsection

<!-- IMPORTANT! -- The following JavaScript code makes it so that when a recruiter clicks on the Submit selection and close job posting button, it only effectively sbmits the selection
IF the recruiter confirms it! -->

<script>
document.addEventListener('DOMContentLoaded', function() {
    const submitApplicantSelectionButton = document.querySelector('#submit-applicant-selection-button');
    
    if (submitApplicantSelectionButton) {
        submitApplicantSelectionButton.addEventListener('click', function(event) {
            event.preventDefault();
            
            if (this.disabled) return;
            this.disabled = true;

            const confirmation = confirm('Are you sure you want to submit this selection?\nThis will also close the job posting.');

            if (confirmation) document.getElementById('applicant-selection-form').submit();
            else this.disabled = false;
        });
    }
});
</script>

