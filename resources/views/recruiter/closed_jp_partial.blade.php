<article class="job-posting mb-3" data-id="{{ $job_posting->id }}">
    <div class="rec-job-posting border-light mb-3 container d-flex flex-column" style="max-width: 150rem;">
        <a href="{{ route('job_postings.show', $job_posting) }}">
            {{ $job_posting->title }}
        </a>
        <div class="jp-creation-date">
            <p><strong>Creation date</strong>: {{ \Carbon\Carbon::parse($job_posting->creation_date)->format('d-m-Y') }}</p>
        </div>
        <div class="jp-status">
            <p><strong>Status</strong>: {{ $job_posting->status }}</p>
        </div>
    </div>
</article>