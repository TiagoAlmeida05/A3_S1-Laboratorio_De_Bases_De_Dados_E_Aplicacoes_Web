@extends('layouts.app')
@section('title', $company->name . ' | ' . config('app.name'))
@section('content')
<div class="container mx-auto p-4">
    {{-- Company Header --}}
    <div class="flex items-center mb-4 justify-between">
        <div class="flex items-center">
            @if($company->logo)
                <img src="{{ asset('storage/' . $company->logo) }}" alt="{{ $company->name }} Logo" class="w-24 h-24 rounded-full mr-4">
            @else
                <div class="w-24 h-24 bg-gray-200 rounded-full flex items-center justify-center mr-4">
                    <span class="text-gray-500 text-3xl font-bold">{{ substr($company->name, 0, 1) }}</span>
                </div>
            @endif
            <h1 class="text-2xl font-bold">{{ $company->name }}</h1>
        </div>
    </div>

    @auth
        @php
            $isCompanyManager = Auth::user()->isRecruiter() && Auth::user()->recruiter->is_company_manager && Auth::user()->recruiter->department->company_id == $company->id;
        @endphp
        @if(!Auth::user()->isAdmin() && !$isCompanyManager)
            <a href="{{ route('reports.create', ['type' => 'Company', 'entity_id' => $company->id]) }}" class="button btn btn-primary" style="background-color: #1c4eb1eb; padding: 0.5rem 0.4rem;">Report company</a>
        @endif
    @endauth

    <div class="mb-4">
        <h2 class="text-xl font-semibold">Company statistics</h2>
        <div>
                <p><strong>Total company job postings</strong>: {{ $companyStatistics['total_company_job_postings'] }}</p>
                <p><strong>Total applications</strong>: {{ $companyStatistics['total_company_applications'] }}</p>
                <p><strong>Total accepted applications</strong>: {{ $companyStatistics['total_company_accepted_applications'] }}</p>
        </div>
    </div>

    {{-- About Us --}}
    @if($company->about_us)
    <div class="mb-4">
        <h2 class="text-xl font-semibold">About Us</h2>
        <p>{{ $company->about_us }}</p>
    </div>
    @endif

    {{-- Location --}}
    @if($company->city)
    <div class="mb-4">
        <h2 class="text-xl font-semibold">Location</h2>
        <p>{{ $company->city->name }}@if($company->city->country), {{ $company->city->country->name }}@endif</p>
    </div>
    @endif

    {{-- Website --}}
    @if($company->website)
    <div class="mb-4">
        <h2 class="text-xl font-semibold">Website</h2>
        <p><a href="{{ $company->website }}" target="_blank" class="text-blue-600 hover:underline">{{ $company->website }}</a></p>
    </div>
    @endif

    {{-- Departments --}}
    <div class="mb-4">
        <h2 class="text-xl font-semibold">Departments</h2>
        @if($company->departments && $company->departments->count() > 0)
        <ul>
            @foreach($company->departments as $dept)
            <li>{{ $dept->name }}</li>
            @endforeach
        </ul>
        @else
        <p>No departments listed</p>
        @endif
    </div>

    {{-- Skills & Tags --}}
    <div class="mb-4">
        <h2 class="text-xl font-semibold">Skills & Tags</h2>
        @if($company->tags && $company->tags->count() > 0)
        <ul class="flex flex-wrap gap-2">
            @foreach($company->tags as $tag)
            <li class="bg-gray-200 px-2 py-1 rounded">{{ $tag->name }}</li>
            @endforeach
        </ul>
        @else
        <p>No tags available</p>
        @endif
    </div>

    {{-- Social Media --}}
    <div class="mb-4">
        <h2 class="text-xl font-semibold">Social Media</h2>
        @if($company->socialMediaProfiles && $company->socialMediaProfiles->count() > 0)
        <ul class="flex gap-2">
            @foreach($company->socialMediaProfiles as $sm)
            <li>
                <a href="{{ $sm->url }}" target="_blank" class="text-blue-600 hover:underline">
                    {{ $sm->socialMediaType->name }}
                </a>
            </li>
            @endforeach
        </ul>
        @else
        <p>No social media profiles</p>
        @endif
    </div>

    {{-- Job Postings --}}
    <div class="mb-4">
        <h2 class="text-xl font-semibold">Job Postings</h2>
        @if($company->jobPostings && $company->jobPostings->count() > 0)
        <ul>
            @foreach($company->jobPostings as $job)
            <li class="mb-3 pb-3 border-b">
                <h3 class="font-semibold">
                    <a href="{{ route('job_postings.show', $job->id) }}" class="text-blue-600 hover:underline text-sm">{{ $job->title }}</a>
                </h3>
                <p class="text-gray-600">{{ $job->description }}</p>
                <p class="text-sm text-gray-500">Deadline: {{ \Carbon\Carbon::parse($job->deadline)->format('M d, Y') }}</p>
            </li>
            @endforeach
        </ul>
        @else
        <p>No job postings available at this time</p>
        @endif
    </div>
</div>
@endsection