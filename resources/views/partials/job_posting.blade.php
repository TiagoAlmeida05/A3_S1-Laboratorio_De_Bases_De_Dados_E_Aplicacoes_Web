<article class="job-posting-partial mb-3" data-id="{{ $job_posting->id }}">
    <div class="jp-container border-light mb-3 container d-flex justify-content-center flex-column" style="max-width: 150rem; ">
        <div class="jp-header card-title">
            <h3>
                <a class="hover-color" href="{{ route('job_postings.show', $job_posting) }}">
                    {{ $job_posting->title }}
                </a>
            </h3>
        </div>
        <div class="jp-body list-group list-group-flush">
            <p class="jp-content">
                <p class="list-group-item"><strong>Deadline</strong>: {{ \Carbon\Carbon::parse($job_posting->deadline)->format('d-m-Y') }}</p>
                <p class="list-group-item"><strong>Description</strong>: {{ $job_posting->description }}</p>
            </p>
        </div>
    </div>
</article>

<style>
  .hover-color:hover {
    color: #2762bcff;
  }
</style>