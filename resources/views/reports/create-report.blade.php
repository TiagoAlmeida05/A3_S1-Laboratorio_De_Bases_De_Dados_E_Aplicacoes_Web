@extends('layouts.app')
@section('title', 'Submit report | ' . config('app.name'))

@section('content')
<section class="container py-5">
    <div class="create-report-container card mx-auto shadow-sm">
        <div class="card-body">
            <h1 class="h4 mb-4">
                @if($type === 'General')
                    Report an issue
                @elseif($type === 'JobSeeker')
                    Report a Job Seeker
                @elseif($type === 'JobPosting')
                    Report a job posting
                @elseif($type === 'Company')
                    Report a company
                @elseif($type === 'Application')
                    Report an application
                @endif
            </h1>

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('reports.store') }}" method="POST">
                @csrf

                <input type="hidden" name="type" value="{{ $type }}">
                @if($entityID)
                    <input type="hidden" name="entity_id" value="{{ $entityID }}">
                @endif

                <div class="mb-3">
                    <label class="form-label">Report type</label>
                    <input type="text" class="form-control bg-light" 
                           value="{{ $type === 'JobSeeker' ? 'Job Seeker' : ($type === 'JobPosting' ? 'Job posting' : $type) }}" 
                           disabled>
                </div>

                @if($entityName)
                    <div class="mb-3">
                        <label class="form-label">Reported content</label>
                        <input type="text" class="form-control bg-light" 
                               value="{{ $entityName }}" 
                               disabled>
                    </div>
                @endif

                <div class="mb-4">
                    <label for="report-description" class="form-label">Description</label>
                    <textarea name="description" id="report-description" class="form-control" rows="5" 
                              placeholder="Please explain the reasons behind your report." required>{{ old('description') }}</textarea>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ url()->previous() }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Submit report</button>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection