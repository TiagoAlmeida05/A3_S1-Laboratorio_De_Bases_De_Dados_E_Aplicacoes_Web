<article class="job-posting mb-3" data-id="{{ $job_posting->id }}">
    <div class="card border-light mb-3 container d-flex justify-content-center" style="max-width: 150rem; ">
        <div class="card-header">
            <h3>
                {{-- Link to the detailed job posting page --}}
                <a href="{{ route('job_postings.show', $job_posting) }}">
                    {{ $job_posting->title }}
                </a>
            </h3>
        </div>
        <div class="card-body">
            <p class="card-text">
                <p><strong>Deadline</strong>: {{ $job_posting->deadline ?? 'N/A' }}</p>
                <p><strong>Description</strong>: {{ $job_posting->description }}</p>
            </p>
        </div>
    </div>
</article>