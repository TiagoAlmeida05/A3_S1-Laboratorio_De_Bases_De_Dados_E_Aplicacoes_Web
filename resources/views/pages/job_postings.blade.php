@extends('layouts.app')
@section('title', 'Homepage | ' . config('app.name'))
@section('content')
<section id="job-postings">
    <h2>All available Jobs</h2>
    
    {{-- Search Bar --}}
    <div class="search-container">
        <form method="GET" action="{{ route('homepage') }}" id="searchForm">
            <div class="search-box">
                <input 
                    type="text" 
                    name="search" 
                    id="searchInput"
                    value="{{ request('search') }}"
                    placeholder="Search job postings..."
                    class="search-input"
                    autocomplete="off"
                >
            </div>
        </form>
    </div>
    
    {{-- Results count --}}
    <div id="resultsCount">
        @if(request('search'))
            <p class="search-results">Found {{ $job_postings->count() }} results for "{{ request('search') }}"</p>
        @endif
    </div>
    
    {{-- Job postings container --}}
    <div id="jobPostingsContainer">
        @if($job_postings->count() > 0)
            @each('partials.job_posting', $job_postings, 'job_posting')
        @else
            <p class="no-results">No job postings found. Try a different search term.</p>
        @endif
    </div>
</section>

<script src="{{ asset('js/search.js') }}"></script>

@endsection