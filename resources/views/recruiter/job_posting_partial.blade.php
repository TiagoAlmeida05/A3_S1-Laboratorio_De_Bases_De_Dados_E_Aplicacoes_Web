<article class="job-posting mb-3" data-id="{{ $job_posting->id }}">
    <div class="rec-job-posting border-light mb-3 container d-flex justify-content-center" style="max-width: 150rem;">
        <a href="{{ route('job_postings.show', $job_posting) }}">
            {{ $job_posting->title }}
        </a>
        <div class="jp-status">
            <p><strong>Status</strong>: {{ $job_posting->status }}</p>
        </div>
        <div class="jp-actions">
            @if($job_posting->status !== 'Closed')
                <p><strong>Edit</strong></p>
                <p><strong>Delete</strong></p>
            @endif
        </div>
    </div>
</article>