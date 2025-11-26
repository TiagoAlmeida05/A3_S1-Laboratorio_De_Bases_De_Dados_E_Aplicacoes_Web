@extends('layouts.app')

@section('title', $jobSeeker->registeredUser->name . ' | ' . config('app.name'))

@section('content')
<section id="job_seeker_profile">
    @auth
        @if(Auth::id() == $jobSeeker->registered_user_id)
            <a class="button" href="{{ route('jobseeker.profile.edit') }}">Edit Profile</a> 
        @endif
    @endauth

    <h1>{{ $jobSeeker->registeredUser->name }}</h1>
    
    <div>
        <img src="" alt="Profile Photo">
        <div>
            @if($jobSeeker->city)
                <p><strong>City:</strong> {{ $jobSeeker->city->name }}</p>
            @endif
            <p><strong>Email:</strong> {{ $jobSeeker->registeredUser->email }}</p>
            @if($jobSeeker->website)
                <p><strong>Website:</strong> <a href="{{ $jobSeeker->website }}" target="_blank">{{ $jobSeeker->website }}</a></p>
            @endif
            @if($jobSeeker->cv && $jobSeeker->show_cv)
                <p><strong>CV:</strong> <a href="{{ asset('storage/'.$jobSeeker->cv) }}" target="_blank">Download CV</a></p>
            @endif
        </div>
    </div>

    <div>
        <h2>About Me</h2>
        <p>{{ $jobSeeker->about_me ?? '' }}</p>
    </div>

    @if($jobSeeker->experienceEntries->count() > 0)
    <div>
        <h2>Experience</h2>
        <ul>
            @foreach($jobSeeker->experienceEntries as $exp)
            <li><strong>{{ $exp->position_name }}</strong> at {{ $exp->employer }} ({{ \Carbon\Carbon::parse($exp->start_date)->format('M Y') }} - {{ $exp->end_date ? \Carbon\Carbon::parse($exp->end_date)->format('M Y') : 'Present' }})</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if($jobSeeker->educationEntries->count() > 0)
    <div>
        <h2>Education</h2>
        <ul>
            @foreach($jobSeeker->educationEntries as $edu)
            <li><strong>{{ $edu->name }}</strong> from {{ $edu->issued_by }} ({{ $edu->start_date ? \Carbon\Carbon::parse($edu->start_date)->format('M Y') : 'N/A' }} - {{ $edu->end_date ? \Carbon\Carbon::parse($edu->end_date)->format('M Y') : 'Present' }})</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if($jobSeeker->certifications->count() > 0)
    <div>
        <h2>Certifications</h2>
        <ul>
            @foreach($jobSeeker->certifications as $cert)
            <li><strong>{{ $cert->name }}</strong> by {{ $cert->issued_by }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if($jobSeeker->tags->count() > 0)
    <div>
        <h2>Skills & Tags</h2>
        <div>
            @foreach($jobSeeker->tags as $tag)
            <span>{{ $tag->name }}</span>
            @endforeach
        </div>
    </div>
    @endif

    @if($jobSeeker->socialMediaProfiles->count() > 0)
    <div>
        <h2>Social Media</h2>
        <div>
            @foreach($jobSeeker->socialMediaProfiles as $sm)
            <a href="{{ $sm->url }}" target="_blank" style="text-decoration: none;">{{ $sm->socialMediaType->name }}</a>
            @endforeach
        </div>
    </div>
    @endif

    @if($jobSeeker->awards->count() > 0)
    <div>
        <h2>Awards</h2>
        <ul>
            @foreach($jobSeeker->awards as $award)
            <li><strong>{{ $award->name }}</strong></li>
            @endforeach
        </ul>
    </div>
    @endif
</section>
@endsection