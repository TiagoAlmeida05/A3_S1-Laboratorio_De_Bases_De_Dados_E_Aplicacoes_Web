<div class="js-container" style="margin: 1rem 0rem;">
    <h3>
        {{ $jobSeeker->registeredUser->name ?? 'Unknown user' }}
    </h3>
    <div class="js-body list-group list-group-flush">
        <p class="js-content">
            <p class="list-group-item">{{ Str::limit($jobSeeker->about_me ?? 'No description available', 150) }}</p>
            <p class="list-group-item"><strong>City</strong>: {{ $jobSeeker->city->name ?? 'N/A' }}</p>
        </p>
    </div>

    <a href="{{ route('jobseeker.profile', $jobSeeker->registered_user_id) }}">View Profile</a>
    @auth
    <a href="{{ route('messages.index', ['registered_user_id' => $jobSeeker->registered_user_id]) }}">Message</a>
    @endauth
</div>