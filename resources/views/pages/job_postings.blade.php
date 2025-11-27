@extends('layouts.app')
@section('title', 'Homepage | ' . config('app.name'))
@section('content')

<section id="search-section">
    {{-- Search Bar --}}
    <div class="search-container mb-6">
        <form method="GET" action="{{ route('homepage') }}" id="searchForm">
            <div class="search-box">
                <input 
                    type="text" 
                    name="search" 
                    id="searchInput"
                    value="{{ request('search') }}"
                    placeholder="Search jobs or companies..."
                    class="search-input"
                    autocomplete="off"
                >
            </div>
        </form>
    </div>

    {{-- Companies Results (only shown when searching) --}}
    <div id="companiesContainer">
        @if(request('search') && request('search') !== '')
            @if($companies->count() > 0)
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">
                        Companies ({{ $companies->count() }})
                    </h2>
                    <div class="space-y-4">
                        @foreach($companies as $company)
                            @include('partials.company_post', ['company' => $company])
                        @endforeach
                    </div>
                </div>
            @else
                <div>
                    <h2>
                        Companies ({{ $companies->count() }})
                    </h2>
                    <p>No company found for "{{ request('search') }}"</p>
                </div>
            @endif
        @endif
    </div>

    <div id="jobSeekerContainer">
        @if(request('search') && request('search') !== '')
            @if($jobSeekers->count() > 0)
                <div>
                    <h2>
                        Job Seekers ({{ $jobSeekers->count() }})
                    </h2>
                    <div>
                        @foreach($jobSeekers as $jobSeeker)
                            @include('partials.job_seeker_part', ['jobSeeker' => $jobSeeker])
                        @endforeach
                    </div>
                </div>
            @else
                <div>
                    <h2>
                        Job Seekers ({{ $jobSeekers->count() }})
                    </h2>
                    <p>No job seekers found for "{{ request('search') }}"</p>
                </div>
            @endif
        @endif
    </div>

    {{-- Job Postings --}}
    <div id="jobPostingsContainer">
        @if(request('search') && request('search') !== '')
            <h2 class="text-2xl font-bold text-gray-900 mb-4">
                Job Postings ({{ $job_postings->count() }})
            </h2>
        @endif
        
        @if($job_postings->count() > 0)
            <div class="space-y-4">
                @each('partials.job_posting', $job_postings, 'job_posting')
            </div>
        @else
            <p class="no-results text-gray-500 text-center py-8">
                No job postings found.
            </p>
        @endif
    </div>
</section>
<script src="{{ asset('js/search.js') }}"></script>
@endsection