@extends('layouts.app')

@section('title', 'Apply for ' . $jobPosting->title . ' | ' . config('app.name'))

@section('content')
<section id="apply_job_posting">
    <div>
        <h1>Apply for Position</h1>
        <div class="job-posting-info">
            <h2>{{ $jobPosting->title }}</h2>
            <p><strong>Company:</strong> {{ $jobPosting->company->name ?? 'Company' }}</p>
            <p><strong>City:</strong> {{ $jobPosting->city->name ?? 'Not specified' }}</p>
            @if($jobPosting->salary)
                <p><strong>Salary:</strong> ${{ number_format($jobPosting->salary, 2) }}</p>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('jobseeker.apply.store', $jobPosting->id) }}" method="POST">
        @csrf
        <div">
            <h2>Application Details</h2>
            <div class="form-group">
                <label for="cover_letter">Cover Letter</label>
                <input type="file" name="Cover Letter">
            </div>

            <div class="form-group">
                <label for="recommendation_letter">Recommendation Letter</label>
                <input type="file" name="Recommendation Letter">
            </div>
        </div>

        <div>
            <a href="{{ route('job_postings.show', $jobPosting->id) }}">
                Cancel
            </a>
            <button type="submit">
                Submit Application
            </button>
        </div>
    </form>
</section>
@endsection