<div class="col-md-6">
    <article class="job-posting-partial h-100" data-id="{{ $job_posting->id }}">
        <div class="card h-100">
            <div class="card-header">
                <h3 class="card-title mb-0">
                    <a class="hover-color" href="{{ route('job_postings.show', $job_posting) }}">
                        {{ $job_posting->title }}
                    </a>
                </h3>
            </div>
            <div class="card-body">
                <p class="mb-2">
                    <strong>Company</strong>: 
                    @if($job_posting->recruiter && $job_posting->recruiter->department && $job_posting->recruiter->department->company)
                        <a class="hover-color" href="{{ route('companies.show', $job_posting->recruiter->department->company) }}">
                            {{ $job_posting->recruiter->department->company->name }}
                        </a>
                    @endif
                </p>
                <p class="mb-2"><strong>Deadline</strong>: {{ \Carbon\Carbon::parse($job_posting->deadline)->format('d/m/Y') }}</p>
                <p class="mb-2"><strong>Description</strong>: {{ $job_posting->description }}</p>
                <p><strong>Tags</strong>: 
                    @if($job_posting->tags->isNotEmpty())
                        @foreach($job_posting->tags as $tag)
                            <span class="badge">{{ $tag->name }}</span>
                        @endforeach
                    @else
                        N/A
                    @endif
                </p>
            </div>
        </div>
    </article>
</div>

<style>
  .hover-color:hover {
    color: #2762bcff;
  }
</style>