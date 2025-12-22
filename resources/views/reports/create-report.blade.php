@extends('layouts.app')
@section('title', 'Submit report | ' . config('app.name'))

@section('content')
<div>
    <div>
        <h1>
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
            <div>{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div>
                <ul>
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

            <div>
                <label>Report type</label>
                <input type="text" value="{{ $type === 'JobSeeker' ? 'Job Seeker' : ($type === 'JobPosting' ? 'Job posting' : $type) }}" disabled>
            </div>

            @if($entityName)
                <div>
                    <label>Reported content</label>
                    <input type="text" value="{{ $entityName }}" disabled>
                </div>
            @endif

            <div>
                <label for="report-description">Description</label>
                <textarea name="report-description" id="report-description" rows="5" placeholder="Please explain the reasons for your report." required>{{ old('description') }}</textarea>
            </div>

            <div>
                <button type="submit" class="button btn btn-primary" style="background-color: #1c4eb1eb; padding: 0.5rem 0.4rem;">Submit report</button>
                <a href="{{ url()->previous() }}">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection