<article class="job-posting mb-3" data-id="{{ $job_posting->id }}">
    <div class="rec-job-posting border-light mb-3 container d-flex flex-column" style="max-width: 150rem;">
        <a href="{{ route('job_postings.show', $job_posting) }}">
            {{ $job_posting->title }}
        </a>
        <div class="jp-creation-date">
            <p><strong>Closed on</strong>: {{ \Carbon\Carbon::parse($job_posting->deadline)->format('d/m/Y') }}</p>
        </div>
        @if($job_posting->applications_count > 0)
            <div class="jp-actions d-flex">
                <a href="{{ route('job_postings.view-applications-closed-job', $job_posting->id) }}" class="button btn btn-primary" style="background-color: #1c4eb1eb; padding: 0.5rem 0.4rem;" >See applications</a>
            </div>
        @endif
    </div>
</article>