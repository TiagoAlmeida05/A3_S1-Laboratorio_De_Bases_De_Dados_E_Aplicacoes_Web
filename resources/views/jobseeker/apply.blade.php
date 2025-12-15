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

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('jobseeker.apply.store', $jobPosting->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div">
            <h2>Application Details</h2>
            <div class="form-group">
                <label for="cv">CV</label>
                <input type="file" name="cv" id="cv" accept=".pdf,.doc,.docx">
            </div>

            <div class="form-group">
                <label for="cover_letter">Cover Letter</label>
                <input type="file" name="cover_letter" id="cover_letter" accept=".pdf,.doc,.docx">
            </div>

            <div class="form-group">
                <label for="recommendation_letter">Recommendation Letter</label>
                <input type="file" name="recomendation_letter" id="cover_letter" accept=".pdf,.doc,.docx">
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