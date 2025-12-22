@extends('layouts.app')
@section('title', 'Homepage | ' . config('app.name'))
@section('content')

<section id="search-section">
    <div class="search-container mb-6" style="margin-bottom: 2rem;">
        <form method="GET" action="{{ route('homepage') }}" id="searchForm">
            <div class="navbar navbar-light bg-light">
                <div class="input-group mx-auto w-75">
                     <span class="input-group-text">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </span>
                    <input class="form-control mr-sm-2 mx-auto"
                        type="text" 
                        name="search" 
                        id="searchInput"
                        value="{{ request('search') }}"
                        placeholder="Search jobs, users or companies..."
                        class="search-input"
                        autocomplete="off"
                    >
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div class="row g-3 mt-3">
                    <div class="col-md-3">
                        <select name="tag" class="form-select" onchange="this.form.submit()">
                            <option value="">All tags</option>
                            @foreach($filterTags as $tag)
                                <option value="{{ $tag->id }}" {{ request('tag') == $tag->id ? 'selected' : '' }}>
                                    {{ $tag->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <select name="company" class="form-select" onchange="this.form.submit()">
                            <option value="">All companies</option>
                            @foreach($filterCompanies as $company)
                                <option value="{{ $company->id }}" {{ request('company') == $company->id ? 'selected' : '' }}>
                                    {{ $company->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <select name="region" class="form-select" onchange="this.form.submit()">
                            <option value="">All regions</option>
                            @foreach($filterCities as $city)
                                <option value="{{ $city->id }}" {{ request('region') == $city->id ? 'selected' : '' }}>
                                    {{ $city->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <input 
                            type="number" 
                            name="min_salary" 
                            id="minSalaryInput"
                            placeholder="Minimum salary (€)"
                            value="{{ request('min_salary') }}"
                            class="form-control" 
                            onchange="this.form.submit()"
                            min="0"
                        >
                    </div>
                </div>
            </div>

            @if(request('search') || request('field') || request('company') || request('region'))
                <div class="mt-3 text-right">
                    <a href="{{ route('homepage') }}" class="text-sm text-red-500 hover:underline">Clear all filters</a>
                </div>
            @endif
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
    <div id="jobPostingsContainer" class="px-5">
        @if(request('search') && request('search') !== '')
            <h2 class="text-2xl font-bold text-gray-900 mb-4">
                Job Postings ({{ $job_postings->count() }})
            </h2>
        @endif
        
        @if($job_postings->count() > 0)
            <div class="row g-4">
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