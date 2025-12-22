@extends('layouts.app')
@section('title', 'Homepage | ' . config('app.name'))
@section('content')

<section id="search-section">
    <div class="container mt-4">
        <form method="GET" action="{{ route('homepage') }}" id="searchForm">
            
            {{-- Main Search Input --}}
            <div class="row mb-3">
                <div class="col-12">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">⌕</span>
                        <input type="text" 
                               name="search" 
                               class="form-control border-start-0" 
                               placeholder="Search jobs (title, requirements...), users or companies..."
                               value="{{ request('search') }}"
                               autocomplete="off">
                        <button class="btn btn-primary" type="submit">Search</button>
                    </div>
                </div>
            </div>

            {{-- Filters Row --}}
            <div class="row g-2">
                
                {{-- 1. TAGS FILTER (Multi-Select) --}}
                <div class="col-md-3">
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle w-100 text-start d-flex justify-content-between align-items-center" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Tags
                        </button>
                        <ul class="dropdown-menu w-100 p-2" style="max-height: 300px; overflow-y: auto;">
                            <li><h6 class="dropdown-header">Select Tags</h6></li>
                            @foreach($filterTags as $tag)
                                <li>
                                    <div class="form-check">
                                        {{-- Note: name="tag[]" creates an array --}}
                                        <input class="form-check-input" type="checkbox" 
                                               name="tag[]" 
                                               value="{{ $tag->id }}" 
                                               id="tag_{{ $tag->id }}"
                                               {{ in_array($tag->id, request('tag', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label w-100" for="tag_{{ $tag->id }}">
                                            {{ $tag->name }}
                                        </label>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                {{-- 2. COMPANIES FILTER (Multi-Select) --}}
                <div class="col-md-3">
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle w-100 text-start d-flex justify-content-between align-items-center" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Companies
                        </button>
                        <ul class="dropdown-menu w-100 p-2" style="max-height: 300px; overflow-y: auto;">
                            <li><h6 class="dropdown-header">Select Companies</h6></li>
                            @foreach($filterCompanies as $company)
                                <li>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                               name="company[]" 
                                               value="{{ $company->id }}" 
                                               id="comp_{{ $company->id }}"
                                               {{ in_array($company->id, request('company', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label w-100" for="comp_{{ $company->id }}">
                                            {{ $company->name }}
                                        </label>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                {{-- 3. REGIONS/CITIES FILTER (Multi-Select) --}}
                <div class="col-md-3">
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle w-100 text-start d-flex justify-content-between align-items-center" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Regions
                        </button>
                        <ul class="dropdown-menu w-100 p-2" style="max-height: 300px; overflow-y: auto;">
                            <li><h6 class="dropdown-header">Select Regions</h6></li>
                            @foreach($filterCities as $city)
                                <li>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                               name="regions[]" 
                                               value="{{ $city->id }}" 
                                               id="city_{{ $city->id }}"
                                               {{ in_array($city->id, request('regions', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label w-100" for="city_{{ $city->id }}">
                                            {{ $city->name }}
                                        </label>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                {{-- 4. MIN SALARY --}}
                <div class="col-md-3">
                    <input type="number" 
                           name="min_salary" 
                           class="form-control" 
                           placeholder="Min Salary (€)" 
                           value="{{ request('min_salary') }}"
                           min="0">
                </div>
            </div>

            {{-- Filter Actions --}}
            <div class="row mt-2">
                <div class="col-12 d-flex justify-content-end gap-2">
                    @if(request()->hasAny(['search', 'tag', 'company', 'regions', 'min_salary']))
                        <a href="{{ route('homepage') }}" class="btn btn-link text-danger text-decoration-none">Clear Filters</a>
                    @endif
                    <button type="submit" class="btn btn-secondary btn-sm">Apply Filters</button>
                </div>
            </div>
        </form>
    </div>

    {{-- RESULTS DISPLAY BELOW --}}
    <div class="container mt-4">
        {{-- Companies Results --}}
        @if(request('search') && isset($companies) && $companies->count() > 0)
            <div class="mb-5">
                <h3 class="h5 mb-3 border-bottom pb-2">Companies ({{ $companies->count() }})</h3>
                <div class="row g-3">
                    @foreach($companies as $company)
                        <div class="col-12">
                            @include('partials.company_post', ['company' => $company])
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Job Seeker Results --}}
        @if(request('search') && isset($jobSeekers) && $jobSeekers->count() > 0)
            <div class="mb-5">
                <h3 class="h5 mb-3 border-bottom pb-2">Job Seekers ({{ $jobSeekers->count() }})</h3>
                <div class="row g-3">
                    @foreach($jobSeekers as $jobSeeker)
                        <div class="col-md-6">
                            @include('partials.job_seeker_part', ['jobSeeker' => $jobSeeker])
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Job Postings Results --}}
        <div id="jobPostingsContainer">
            <h3 class="h5 mb-3 border-bottom pb-2">
                Job Postings ({{ $job_postings->count() }})
            </h3>
            
            @if($job_postings->count() > 0)
                <div class="d-flex flex-column gap-3">
                    @foreach($job_postings as $job_posting)
                        @include('partials.job_posting', ['job_posting' => $job_posting])
                    @endforeach
                </div>
            @else
                <div class="alert alert-light text-center border mt-3">
                    No job postings found matching your criteria.
                </div>
            @endif
        </div>
    </div>
</section>
<script src="{{ asset('js/search.js') }}"></script>
@endsection