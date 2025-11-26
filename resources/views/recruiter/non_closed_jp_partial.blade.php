<article class="job-posting mb-3" data-id="{{ $job_posting->id }}">
    @if($job_posting->status !== 'Closed')
        <div class="rec-job-posting border-light mb-3 container d-flex justify-content-center" style="max-width: 150rem;">
            <a href="{{ route('job_postings.show', $job_posting) }}">
                {{ $job_posting->title }}
            </a>
            <div class="jp-creation-date">
                <p><strong>Creation date</strong>: {{ \Carbon\Carbon::parse($job_posting->creation_date)->format('d-m-Y') }}</p>
            </div>
            <div class="jp-status">
                <p><strong>Status</strong>: {{ $job_posting->status }}</p>
            </div>
            <div class="jp-actions">
                    <a href="{{ route('job_postings.edit', $job_posting->id) }}" class="a-as-button" id="edit-job">Edit</a>
                    <form id="delete-{{ $job_posting->id }}-job" method="POST" action="{{ route('job_postings.delete', $job_posting->id) }}">
                        @csrf
                        @method('DELETE')

                        <button type="button" class="delete-button" data-id="{{ $job_posting->id }}">Delete</button>
                    </form>
            </div>
        </div>
    @endif
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
</script>