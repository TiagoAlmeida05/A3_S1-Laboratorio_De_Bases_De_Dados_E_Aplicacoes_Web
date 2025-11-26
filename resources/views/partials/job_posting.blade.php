<article class="job-posting-partial mb-3" data-id="{{ $job_posting->id }}">
    <div class="jp-container border-light mb-3 container d-flex justify-content-center" style="max-width: 150rem; ">
        <div class="jp-header">
            <h3>
                {{-- Link to the detailed job posting page --}}
                <a href="{{ route('job_postings.show', $job_posting) }}">
                    {{ $job_posting->title }}
                </a>
            </h3>
        </div>
        <div class="jp-body">
            <p class="jp-content">
                <p><strong>Deadline:</strong>: {{ \Carbon\Carbon::parse($job_posting->deadline)->format('d-m-Y') }}</p>
                <p><strong>Description</strong>: {{ $job_posting->description }}</p>
            </p>
        </div>
    </div>
</article>