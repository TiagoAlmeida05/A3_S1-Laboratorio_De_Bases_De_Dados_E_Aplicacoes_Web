@extends('layouts.app')
@section('title', $company->name . ' | ' . config('app.name'))
@section('content')
<div class="company-profile-container p-4 mx-auto">
    <div class="company-info card mb-4">
        <div class="card-body m-3">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex align-items-center gap-3">
                    @if($company->logo)
                        <img src="{{ asset('storage/' . $company->logo) }}" alt="{{ $company->name }} Logo" class="rounded-circle profile-pic">
                    @else
                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <span class="text-muted fs-3 fw-bold">{{ substr($company->name, 0, 1) }}</span>
                        </div>
                    @endif
                    <h1 class="mb-0">{{ $company->name }}</h1>
                </div>

                @auth
                    @php
                        $isCompanyManager = Auth::user()->isRecruiter() && Auth::user()->recruiter->is_company_manager && Auth::user()->recruiter->department->company_id == $company->id;
                    @endphp
                    @if(!Auth::user()->isAdmin() && !$isCompanyManager)
                        <a href="{{ route('reports.create', ['type' => 'Company', 'entity_id' => $company->id]) }}" class="btn btn-danger">Report company</a>
                    @endif
                @endauth
            </div>

            <div class="mb-4">
                <h2 class="h5 fw-semibold">Company statistics</h2>
                <p class="mb-1"><strong>Total company job postings</strong>: {{ $companyStatistics['total_company_job_postings'] }}</p>
                <p class="mb-1"><strong>Total applications</strong>: {{ $companyStatistics['total_company_applications'] }}</p>
                <p class="mb-0"><strong>Total accepted applications</strong>: {{ $companyStatistics['total_company_accepted_applications'] }}</p>
            </div>

            @if($company->about_us)
            <div class="mb-4">
                <h2 class="h5 fw-semibold">About us</h2>
                <p class="mb-0">{{ $company->about_us }}</p>
            </div>
            @endif

            @if($company->city)
            <div class="mb-4">
                <h2 class="h5 fw-semibold">Location</h2>
                <p class="mb-0">{{ $company->city->name }}@if($company->city->country), {{ $company->city->country->name }}@endif</p>
            </div>
            @endif

            @if($company->website)
            <div class="mb-4">
                <h2 class="h5 fw-semibold">Website</h2>
                <p class="mb-0"><a href="{{ $company->website }}" target="_blank" class="link-text" >{{ $company->website }}</a></p>
            </div>
            @endif

            <div class="mb-4">
                <h2 class="h5 fw-semibold">Departments</h2>
                @if($company->departments && $company->departments->count() > 0)
                <ul class="mb-0">
                    @foreach($company->departments as $dept)
                    <li>{{ $dept->name }}</li>
                    @endforeach
                </ul>
                @else
                <p class="mb-0 text-muted">No departments listed</p>
                @endif
            </div>

            <div class="mb-4">
                <h2 class="h5 fw-semibold">Skills & Tags</h2>
                @if($company->tags && $company->tags->count() > 0)
                <div class="d-flex flex-wrap gap-2">
                    @foreach($company->tags as $tag)
                    <span class="badge">{{ $tag->name }}</span>
                    @endforeach
                </div>
                @else
                <p class="mb-0 text-muted">No tags available</p>
                @endif
            </div>

            <div class="mb-4">
                <h2 class="h5 fw-semibold">Social media</h2>
                @if($company->socialMediaProfiles && $company->socialMediaProfiles->count() > 0)
                <div class="d-flex gap-3">
                    @foreach($company->socialMediaProfiles as $sm)
                    <a href="{{ $sm->url }}" target="_blank" class="link-text">{{ $sm->socialMediaType->name }}</a>
                    @endforeach
                </div>
                @else
                <p class="mb-0 text-muted">No social media profiles</p>
                @endif
            </div>
        </div>
    </div>

    <div class="company-job-postings card">
        <div class="card-header">
            <h2 class="h5 fw-semibold mb-0">Job postings</h2>
        </div>
        <div class="card-body">
            @if($company->jobPostings && $company->jobPostings->count() > 0)
                @foreach($company->jobPostings as $job)
                <div class="mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <h3 class="h6 fw-semibold mb-1">
                        <a href="{{ route('job_postings.show', $job->id) }}" class="title-colored">{{ $job->title }}</a>
                    </h3>
                    <p class="mb-1">{{ $job->description }}</p>
                    <small class="text-muted">Deadline: {{ \Carbon\Carbon::parse($job->deadline)->format('d/m/Y') }}</small>
                </div>
                @endforeach
            @else
                <p class="mb-0 text-muted">No job postings available at this time</p>
            @endif
        </div>
    </div>
</div>
@endsection