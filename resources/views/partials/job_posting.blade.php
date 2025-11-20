<article class="job-posting" data-id="{{ $job_posting->id }}">
    <header>
        <h2>
            {{-- Link to the detailed job posting page --}}
            <a href="{{ route('job_postings.show', $job_posting) }}">
                {{ $job_posting->title }}
            </a>
        </h2>
        <p>Deadline: {{ $job_posting->deadline ?? 'N/A' }}</p>
        <p>Description: {{ $job_posting->description }}</p>
    </header>
</article>