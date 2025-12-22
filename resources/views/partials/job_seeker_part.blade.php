<div class="card mb-3 js-container">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h3 class="h5 mb-1">
                    <a href="{{ route('jobseeker.profile', $jobSeeker->registered_user_id) }}" class="text-decoration-none">{{ $jobSeeker->registeredUser->name ?? 'Unknown user' }}</a>
                </h3>
                <p class="mb-1">{{ Str::limit($jobSeeker->about_me ?? 'No description available', 150) }}</p>
                <small class="text-muted"><strong>City</strong>: {{ $jobSeeker->city->name ?? 'N/A' }}</small>
            </div>

            @can('message', $jobSeeker)
                <a class="btn btn-primary btn-sm" href="{{ route('messages.index', $jobSeeker->registered_user_id) }}">Message</a>
            @endcan
        </div>
    </div>
</div>