@extends('layouts.app')

@section('title', $jobSeeker->registeredUser->name . ' | ' . config('app.name'))

@section('content')
<section id="job_seeker_profile">
    @auth
        @if(Auth::id() == $jobSeeker->registered_user_id)
            <a class="button button btn btn-primary" style="background-color: #1c4eb1eb; padding: 0.5rem 0.4rem;" href="{{ route('jobseeker.profile.edit') }}">Edit profile</a>
        @endif

        @auth
        @if(auth()->user()->isRecruiter() && auth()->id() != $jobSeeker->registered_user_id)
            <a class="button button btn btn-primary" style="background-color: #1c4eb1eb; padding: 0.5rem 0.4rem;" href="{{ route('messages.index', $jobSeeker->registered_user_id) }}" class="btn btn-success" style="margin-top: 1rem;">
                Message
            </a>
        @endif
@endauth

    @endauth

    <div class="d-flex flex-column" style="margin-bottom: 1rem;">
        <h2 class="align-self-center" style="margin: 2rem 0rem;">{{ $jobSeeker->registeredUser->name }}</h2>
        <div>
            @if ($jobSeeker->profile_photo)
                <img src="{{ asset('storage/' . $jobSeeker->profile_photo) }}" width="150" class="mb-3 rounded shadow-sm">
            @else
                <p>No profile picture uploaded.</p>
            @endif
            <div style="margin: 1rem 0rem;">
                @if($jobSeeker->city)
                    <p><strong>City:</strong> {{ $jobSeeker->city->name }}</p>
                @endif
                <p><strong>Email:</strong> {{ $jobSeeker->registeredUser->email }}</p>
                @if($jobSeeker->website)
                    <p><strong>Website:</strong> <a href="{{ $jobSeeker->website }}" target="_blank">{{ $jobSeeker->website }}</a></p>
                @endif
                @if($jobSeeker->cv && $jobSeeker->show_cv)
                    <p><strong>CV:</strong> <a href="{{ asset('storage/'.$jobSeeker->cv) }}" target="_blank">View CV</a></p>
                @endif
            </div>
        </div>

       @if(!empty($jobSeeker->about_me))
            <div style="margin: 1rem 0rem;">
                <h4>About me</h4>
                <p>{{ $jobSeeker->about_me }}</p>
            </div>
        @endif

        @if($jobSeeker->experienceEntries->count() > 0)
        <div style="margin: 1rem 0rem;">
            <h4>Experience</h4>
            <ul>
                @foreach($jobSeeker->experienceEntries as $exp)
                <li><strong>{{ $exp->position_name }}</strong> at {{ $exp->employer }} ({{ \Carbon\Carbon::parse($exp->start_date)->format('M Y') }} - {{ $exp->end_date ? \Carbon\Carbon::parse($exp->end_date)->format('M Y') : 'Present' }})</li>
                @endforeach
            </ul>
        </div>
        @endif

        @if($jobSeeker->educationEntries->count() > 0)
        <div style="margin: 1rem 0rem;">
            <h4>Education</h4>
            <ul>
                @foreach($jobSeeker->educationEntries as $edu)
                <li><strong>{{ $edu->name }}</strong> from {{ $edu->issued_by }} ({{ $edu->start_date ? \Carbon\Carbon::parse($edu->start_date)->format('M Y') : 'N/A' }} - {{ $edu->end_date ? \Carbon\Carbon::parse($edu->end_date)->format('M Y') : 'Present' }})</li>
                @endforeach
            </ul>
        </div>
        @endif

        @if($jobSeeker->certifications->count() > 0)
        <div style="margin: 1rem 0rem;">
            <h4>Certifications</h4>
            <ul>
                @foreach($jobSeeker->certifications as $cert)
                <li><strong>{{ $cert->name }}</strong> by {{ $cert->issued_by }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @if($jobSeeker->tags->count() > 0)
        <div style="margin: 1rem 0rem;">
            <h4>Skills & Tags</h4>
            <div>
                @foreach($jobSeeker->tags as $tag)
                <span>{{ $tag->name }}</span>
                @endforeach
            </div>
        </div>
        @endif

        @if($jobSeeker->socialMediaProfiles->count() > 0)
        <div style="margin: 1rem 0rem;">
            <h4>Social media</h4>
            <div>
                @foreach($jobSeeker->socialMediaProfiles as $sm)
                <a href="{{ $sm->url }}" target="_blank" style="text-decoration: none;">{{ $sm->socialMediaType->name }}</a>
                @endforeach
            </div>
        </div>
        @endif

        @if($jobSeeker->awards->count() > 0)
        <div style="margin: 1rem 0rem;">
            <h4>Awards</h4>
            <ul>
                @foreach($jobSeeker->awards as $award)
                <li><strong>{{ $award->name }}</strong></li>
                @endforeach
            </ul>
        </div>
        @endif  
    </div>
</section>
@endsection