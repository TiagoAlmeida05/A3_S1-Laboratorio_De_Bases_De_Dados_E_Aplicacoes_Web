<div class="js-container" style="margin: 1rem 0rem;">
    <h3>
        <a href="{{ route('jobseeker.profile', $jobSeeker->registered_user_id) }}">{{ $jobSeeker->registeredUser->name ?? 'Unknown user' }}</a>
    </h3>
    <div class="js-body list-group list-group-flush">
        <p class="js-content">
            <p class="list-group-item">{{ Str::limit($jobSeeker->about_me ?? 'No description available', 150) }}</p>
            <p class="list-group-item"><strong>City</strong>: {{ $jobSeeker->city->name ?? 'N/A' }}</p>
        </p>
    </div>

    @can('message', $jobSeeker)
        <a class="btn btn-primary btn-sm" href="{{ route('messages.index', $jobSeeker->registered_user_id) }}">Message</a>
    @endcan
</div>