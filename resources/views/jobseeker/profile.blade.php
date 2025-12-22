@extends('layouts.app')

@section('title', $jobSeeker->registeredUser->name . ' | ' . config('app.name'))

@section('content')
<section id="job_seeker_profile">
    <div class="card mx-auto seeker-profile-container">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex align-items-center gap-3">
                    @if ($jobSeeker->profile_photo)
                        <img src="{{ asset('storage/' . $jobSeeker->profile_photo) }}" class="rounded-circle profile-pic">
                    @else
                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <span class="text-muted fs-3 fw-bold">{{ substr($jobSeeker->registeredUser->name, 0, 1) }}</span>
                        </div>
                    @endif
                    <h2 class="mb-0">{{ $jobSeeker->registeredUser->name }}</h2>
                </div>

                <div class="d-flex gap-2">
                    @can('message', $jobSeeker)
                        <a class="btn btn-primary" href="{{ route('messages.index', $jobSeeker->registered_user_id) }}">Message</a>
                    @endcan
                    @can('update', $jobSeeker)
                        <a class="btn btn-primary" href="{{ route('jobseeker.profile.edit') }}">Edit profile</a>
                    @endcan
                </div>
            </div>

            <p class="mb-2"><strong>Email</strong>: {{ $jobSeeker->registeredUser->email }}</p>
            
            @if($jobSeeker->city)
                <p class="mb-2"><strong>City</strong>: {{ $jobSeeker->city->name }}</p>
            @endif
            
            @if($jobSeeker->website)
                <p class="mb-2"><strong>Website</strong>: <a href="{{ $jobSeeker->website }}" target="_blank" class="text-primary">{{ $jobSeeker->website }}</a></p>
            @endif
            
            @if($jobSeeker->cv && $jobSeeker->show_cv)
                <p class="mb-2"><strong>CV</strong>: <a href="{{ asset('storage/'.$jobSeeker->cv) }}" target="_blank" class="text-primary">View CV</a></p>
            @endif

            @if(!empty($jobSeeker->about_me))
                <div class="mb-3 mt-4">
                    <h4 class="h5 fw-semibold border-bottom pb-2">About me</h4>
                    <p class="mb-0">{{ $jobSeeker->about_me }}</p>
                </div>
            @endif

            @if($jobSeeker->experienceEntries->count() > 0)
                <div class="mb-3">
                    <h4 class="h5 fw-semibold border-bottom pb-2">Experience</h4>
                    <ul class="mb-0">
                        @foreach($jobSeeker->experienceEntries as $exp)
                            <li><strong>{{ $exp->position_name }}</strong> at {{ $exp->employer }} ({{ \Carbon\Carbon::parse($exp->start_date)->format('M Y') }} - {{ $exp->end_date ? \Carbon\Carbon::parse($exp->end_date)->format('M Y') : 'Present' }})</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if($jobSeeker->educationEntries->count() > 0)
                <div class="mb-3">
                    <h4 class="h5 fw-semibold border-bottom pb-2">Education</h4>
                    <ul class="mb-0">
                        @foreach($jobSeeker->educationEntries as $edu)
                            <li><strong>{{ $edu->name }}</strong> from {{ $edu->issued_by }} ({{ $edu->start_date ? \Carbon\Carbon::parse($edu->start_date)->format('M Y') : 'N/A' }} - {{ $edu->end_date ? \Carbon\Carbon::parse($edu->end_date)->format('M Y') : 'Present' }})</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if($jobSeeker->certifications->count() > 0)
                <div class="mb-3">
                    <h4 class="h5 fw-semibold border-bottom pb-2">Certifications</h4>
                    <ul class="mb-0">
                        @foreach($jobSeeker->certifications as $cert)
                            <li><strong>{{ $cert->name }}</strong> by {{ $cert->issued_by }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if($jobSeeker->tags->count() > 0)
                <div class="mb-3">
                    <h4 class="h5 fw-semibold border-bottom pb-2">Skills & Tags</h4>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($jobSeeker->tags as $tag)
                            <span class="badge">{{ $tag->name }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($jobSeeker->socialMediaProfiles->count() > 0)
                <div class="mb-3">
                    <h4 class="h5 fw-semibold border-bottom pb-2">Social media</h4>
                    <div class="d-flex gap-3">
                        @foreach($jobSeeker->socialMediaProfiles as $sm)
                            <a href="{{ $sm->url }}" target="_blank" class="text-primary">{{ $sm->socialMediaType->name }}</a>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($jobSeeker->awards->count() > 0)
                <div class="mb-3">
                    <h4 class="h5 fw-semibold border-bottom pb-2">Awards</h4>
                    <ul class="mb-0">
                        @foreach($jobSeeker->awards as $award)
                            <li><strong>{{ $award->name }}</strong></li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>

    <div class="d-flex justify-content-center mt-3 gap-2">
        <a class="btn btn-primary" href="{{ url()->previous() }}">Go back</a>
        @auth
            @if(!Auth::user()->isAdmin() && Auth::id() != $jobSeeker->registered_user_id)
                <a href="{{ route('reports.create', ['type' => 'JobSeeker', 'entity_id' => $jobSeeker->registered_user_id]) }}" class="btn btn-danger">Report job seeker</a>
            @endif
        @endauth
    </div>
</section>
@endsection