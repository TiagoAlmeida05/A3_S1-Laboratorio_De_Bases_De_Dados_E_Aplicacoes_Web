@extends('layouts.app')

@section('title', 'Edit Application - ' . $application->jobPosting->title . ' | ' . config('app.name'))

@section('content')
<section>
    <h1>Edit Application</h1>
    
    <div>
        <h2>{{ $application->jobPosting->title }}</h2>
        <p><strong>Company:</strong> {{ $application->jobPosting->company->name ?? 'Company' }}</p>
        <p><strong>City:</strong> {{ $application->jobPosting->city->name ?? 'Not specified' }}</p>
        <p><strong>Applied on:</strong> {{ $application->date->format('d/m/Y H:i') }}</p>
    </div>

    @if(session('success'))
        <div>
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('applications.update', $application->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div>
            <h3>Application Documents</h3>
            
            <!-- CV -->
            <div class="mb-3">
                <label class="form-label">CV</label>
                <input name="cv" type="file" class="form-control" accept=".pdf,.doc,.docx">
                
                @if($application->cv)
                    <div class="mt-2">
                        <p>Current CV: 
                            <a href="{{ asset('storage/' . $jobSeeker->cv) }}">View CV</a>
                        </p>
                    </div>
                @endif
            </div>

            <!-- Cover Letter -->
            <div class="mb-3">
                <label class="form-label">Cover Letter</label>
                <input name="cover_letter" type="file" class="form-control" accept=".pdf,.doc,.docx">
                
                @if($application->cover_letter)
                    <div>
                        <p>Current Cover Letter: 
                            <a href="{{ asset('storage/' . $application->cover_letter) }}">View Cover Letter</a>
                        </p>
                    </div>
                @endif
            </div>

            <div>
                <label class="form-label">Recommendation Letter (Optional)</label>
                <input name="recommendation_letter" type="file" class="form-control" accept=".pdf,.doc,.docx">
                
                @if($application->recommendation_letter)
                    <div>
                        <p>Current Recommendation Letter: 
                            <a href="{{ asset('storage/' . $application->recommendation_letter) }}">View Recommendation Letter</a>
                        </p>
                    </div>
                @endif
            </div>
        </div>

        <div class="d-flex justify-content-between">
            <a href="{{ route('jobseeker.applications') }}">Back to Applications</a>
            <button>Save Changes</button>
        </div>
    </form>
</section>
@endsection