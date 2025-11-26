<div>
    <h3>
        {{ $jobSeeker->registeredUser->name ?? 'Unknown User' }}
    </h3>

    <p>
        {{ Str::limit($jobSeeker->about_me ?? 'No description available', 150) }}
    </p>

    <p>
        City: {{ $jobSeeker->city->name ?? 'N/A' }}
    </p>

    <a href="{{ route('jobseeker.profile', $jobSeeker->registered_user_id) }}">View Profile</a>
</div>