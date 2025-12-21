@extends('layouts.app')
@section('title', 'Homepage | ' . config('app.name'))
@section('content')

<section id="search-section">
    {{-- Search Bar & Filters--}}
    <div class="search-container mb-6" style="margin-bottom: 2rem;">
        <form method="GET" action="{{ route('homepage') }}" id="searchForm">
            <div class="navbar navbar-light bg-light">
                <input class="form-control mr-sm-2 mx-auto" style="width: 80vw;"
                    type="text" 
                    name="search" 
                    id="searchInput"
                    value="{{ request('search') }}"
                    placeholder="⌕ Search jobs, users or companies..."
                    class="search-input"
                    autocomplete="off"
                >
            </div>
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">

                {{-- Tags Filter --}}
                <select name="tag" class="form-select p-2 border rounded w-full" onchange="this.form.submit()">
                    <option value="">All Tags</option>
                    @foreach($filterTags as $tag)
                        <option value="{{ $tag->id }}" {{ request('tag') == $tag->id ? 'selected' : '' }}>
                            {{ $tag->name }}
                        </option>
                    @endforeach
                </select>

                {{-- Company Filter --}}
                <select name="company" class="form-select p-2 border rounded w-full" onchange="this.form.submit()">
                    <option value="">All Companies</option>
                    @foreach($filterCompanies as $company)
                        <option value="{{ $company->id }}" {{ request('company') == $company->id ? 'selected' : '' }}>
                            {{ $company->name }}
                        </option>
                    @endforeach
                </select>

                {{-- Region (City) Filter --}}
                <select name="region" class="form-select p-2 border rounded w-full" onchange="this.form.submit()">
                    <option value="">All Regions</option>
                    @foreach($filterCities as $city)
                        <option value="{{ $city->id }}" {{ request('region') == $city->id ? 'selected' : '' }}>
                            {{ $city->name }}
                        </option>
                    @endforeach
                </select>
                
                {{-- Minimum Salary Filter --}}
                <input 
                    type="number" 
                    name="min_salary" 
                    id="minSalaryInput"
                    placeholder="Min Salary (€)"
                    value="{{ request('min_salary') }}"
                    class="form-control p-2 border rounded w-full" 
                    onchange="this.form.submit()"
                    min="0"
                >
            </div>
            {{-- Reset Filters Button (Optional but helpful) --}}
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