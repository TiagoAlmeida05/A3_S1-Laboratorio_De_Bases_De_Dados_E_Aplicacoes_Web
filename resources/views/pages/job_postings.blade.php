@extends('layouts.app')
@section('title', 'Homepage | ' . config('app.name'))
@section('content')

<section id="search-section">
    <div class="search-container mb-4">
        <form method="GET" action="{{ route('homepage') }}" id="searchForm">
            <div class="navbar navbar-light bg-light">
                <div class="input-group mx-auto w-75">
                    <span class="input-group-text">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </span>
                    <input class="form-control"
                        type="text" 
                        name="search" 
                        id="searchInput"
                        value="{{ request('search') }}"
                        placeholder="Search jobs (title, requirements...), users or companies..."
                        autocomplete="off"
                    >
                    <button class="btn btn-primary" type="submit">Search</button>
                </div>
            </div>

            <div class="row g-3 mt-3 align-items-center">
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
                
                <div class="col-md-3">
                    <input 
                        type="number" 
                        name="min_salary" 
                        id="minSalaryInput"
                        placeholder="Minimum salary (€)"
                        value="{{ request('min_salary') }}"
                        class="form-control" 
                        min="0"
                    >
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-12 d-flex justify-content-end gap-2">
                    @if(request()->hasAny(['search', 'tag', 'company', 'regions', 'min_salary']))
                        <a href="{{ route('homepage') }}" class="btn btn-link text-danger text-decoration-none">Clear Filters</a>
                    @endif
                    <button type="submit" class="btn btn-primary btn-sm">Apply filters</button>
                </div>
            </div>
        </form>
    </div>

    <div id="companiesContainer" class="px-5">
        @if(request('search') && isset($companies) && $companies->count() > 0)
            <div class="mb-4">
                <h2 class="h5 mb-3 border-bottom pb-2">Companies ({{ $companies->count() }})</h2>
                @foreach($companies as $company)
                    @include('partials.company_post', ['company' => $company])
                @endforeach
            </div>
        @endif
    </div>

    <div id="jobSeekerContainer" class="px-5">
        @if(request('search') && isset($jobSeekers) && $jobSeekers->count() > 0)
            <div class="mb-4">
                <h2 class="h5 mb-3 border-bottom pb-2">Job Seekers ({{ $jobSeekers->count() }})</h2>
                @foreach($jobSeekers as $jobSeeker)
                    @include('partials.job_seeker_part', ['jobSeeker' => $jobSeeker])
                @endforeach
            </div>
        @endif
    </div>

    <div id="jobPostingsContainer" class="px-5">
        <h2 class="h5 mb-3 border-bottom pb-2">Job Postings ({{ $job_postings->count() }})</h2>
        
        @if($job_postings->count() > 0)
            <div class="row g-4">
                @foreach($job_postings as $job_posting)
                    @include('partials.job_posting', ['job_posting' => $job_posting])
                @endforeach
            </div>
        @else
            <div class="card">
                <div class="card-body text-center py-4">
                    <p class="text-muted mb-0">No job postings found matching your criteria.</p>
                </div>
            </div>
        @endif
    </div>
</section>

<script src="{{ asset('js/search.js') }}"></script>
@endsection