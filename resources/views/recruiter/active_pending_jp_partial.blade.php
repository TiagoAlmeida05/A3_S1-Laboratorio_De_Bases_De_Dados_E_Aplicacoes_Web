<article class="job-posting mb-3" data-id="{{ $job_posting->id }}">
    <div class="rec-job-posting border-light mb-3 container d-flex flex-column" style="max-width: 150rem;">
        
        <a href="{{ route('job_postings.show', $job_posting) }}">
            {{ $job_posting->title }}
        </a>

        {{-- POSTED BY LABEL (Only visible in 'Company' view) --}}
        @if(request('view') === 'company')
            <div class="jp-author" style="font-size: 0.9rem; color: #666; margin-bottom: 0.5rem;">
                Posted by: <strong>{{ $job_posting->recruiter->user->name ?? 'Unknown' }}</strong>
            </div>
        @endif

        <div class="jp-creation-date">
            <p><strong>Creation date</strong>: {{ \Carbon\Carbon::parse($job_posting->creation_date)->format('d-m-Y') }}</p>
        </div>
        <div class="jp-deadline">
            <p><strong>Deadline</strong>: {{ \Carbon\Carbon::parse($job_posting->deadline)->format('d-m-Y') }}</p>
        </div>
        <div class="jp-status">
            <p><strong>Status</strong>: {{ $job_posting->status }}</p>
        </div>
        <div class="jp-applications">
            <p><strong>Number of applications</strong>: {{ $job_posting->applications_count }}</p>
        </div>

        <div class="jp-actions d-flex">
            
            @can('update', $job_posting)
                <a href="{{ route('job_postings.edit', $job_posting->id) }}" class="button btn btn-primary" style="background-color: #1c4eb1eb; padding: 0.5rem 0.4rem; margin-right: 5px;">Edit</a>
            @endcan

            @if($job_posting->status === 'Pending')
                
                @if(Auth::user()->recruiter->is_company_manager)
                    <form action="{{ route('job_postings.approve', $job_posting->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="button btn btn-success" style="background-color: #28a745; color: white; padding: 0.5rem 0.4rem; margin-right: 5px; border:none;">
                            Approve
                        </button>
                    </form>
                @endif

                @can('delete', $job_posting)
                    <form id="delete-{{ $job_posting->id }}-job" method="POST" action="{{ route('job_postings.delete', $job_posting->id) }}">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="delete-button button btn btn-primary" style="background-color: #1c4eb1eb; padding: 0.5rem 0.4rem;" data-id="{{ $job_posting->id }}">Delete</button>
                    </form>
                @endcan
            @endif

            @if($job_posting->status === 'Active')
                @if($job_posting->applications_count > 0)
                    @can('close', $job_posting)
                        <a href="{{ route('job_postings.manage-applications', $job_posting->id) }}" class="button btn btn-primary" style="background-color: #1c4eb1eb; padding: 0.5rem 0.4rem; margin-right: 5px;" >See applications</a>
                        <a href="{{ route('job_postings.manage-applications', [$job_posting->id, 'selectMode' => 1]) }}" class="button btn btn-primary" style="background-color: #1c4eb1eb; padding: 0.5rem 0.4rem;">Select applicants and close</a>
                    @endcan
                @else
                    @can('close', $job_posting)
                        <form id="close-{{ $job_posting->id }}-job" method="POST" action="{{ route('job_postings.close', $job_posting->id) }}" style="display:inline; margin-right: 5px;">
                            @csrf
                            @method('PATCH')
                            <button type="button" class="close-button button btn btn-primary" style="background-color: #1c4eb1eb; padding: 0.5rem 0.4rem;" data-id="{{ $job_posting->id }}">Close</button>
                        </form>
                    @endcan
                    
                    @can('delete', $job_posting)
                        <form id="delete-{{ $job_posting->id }}-job" method="POST" action="{{ route('job_postings.delete', $job_posting->id) }}" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="delete-button button btn btn-primary" style="background-color: #1c4eb1eb; padding: 0.5rem 0.4rem;" data-id="{{ $job_posting->id }}">Delete</button>
                        </form>
                    @endcan
                @endif
            @endif
        </div>
    </div>
</article>

<!-- IMPORTANT! -- The following JavaScript code makes it so that when a recruiter clicks on the Delete button, it only effectively deletes the post
IF the recruiter confirms it! -->
<script>
    document.querySelectorAll('.delete-button').forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            if (this.disabled) return;
            this.disabled = true;

            const id = this.getAttribute('data-id');
            const confirmation = confirm('Are you sure you want to delete this job posting?');

            if (confirmation) document.getElementById('delete-' + id + '-job').submit();
            else this.disabled = false;
        })
    });

    document.querySelectorAll('.close-button').forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            if (this.disabled) return;
            this.disabled = true;

            const id = this.getAttribute('data-id');
            const confirmation = confirm('Are you sure you want to close this job posting?\nJob seekers will no longer be able to apply!');

            if (confirmation) document.getElementById('close-' + id + '-job').submit();
            else this.disabled = false;
        })
    });
</script>
