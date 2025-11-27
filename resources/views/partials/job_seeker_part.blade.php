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

    <a class="button btn btn-primary" style="background-color: #1c4eb1eb; padding: 0.5rem 0.4rem;" href="{{ route('jobseeker.profile', $jobSeeker->registered_user_id) }}">View Profile</a>
</div>