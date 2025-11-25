@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <div class="flex items-center mb-4">
        <img src="{{ asset($jobSeeker->profile_photo) }}" alt="Profile Photo" class="w-24 h-24 rounded-full mr-4">
        <h1 class="text-2xl font-bold">{{ $jobSeeker->registeredUser->name }}</h1>
    </div>

    <div class="mb-4">
        <h2 class="text-xl font-semibold">About Me</h2>
        <p>{{ $jobSeeker->about_me }}</p>
    </div>

    <div class="mb-4">
        <h2 class="text-xl font-semibold">Experience</h2>
        <ul>
            @foreach($jobSeeker->experienceEntries as $exp)
            <li>{{ $exp->position_name }} at {{ $exp->employer }} ({{ $exp->start_date }} - {{ $exp->end_date }})</li>
            @endforeach
        </ul>
    </div>

    <div class="mb-4">
        <h2 class="text-xl font-semibold">Education</h2>
        <ul>
            @foreach($jobSeeker->educationEntries as $edu)
            <li>{{ $edu->name }} from {{ $edu->issued_by }} ({{ $edu->start_date ?? 'N/A' }} - {{ $edu->end_date }})</li>
            @endforeach
        </ul>
    </div>

    <div class="mb-4">
        <h2 class="text-xl font-semibold">Certifications</h2>
        <ul>
            @foreach($jobSeeker->certifications as $cert)
            <li>{{ $cert->name }} by {{ $cert->issued_by }}</li>
            @endforeach
        </ul>
    </div>

    <div class="mb-4">
        <h2 class="text-xl font-semibold">Skills & Tags</h2>
        <ul class="flex flex-wrap gap-2">
            @foreach($jobSeeker->tags as $tag)
            <li class="bg-gray-200 px-2 py-1 rounded">{{ $tag->name }}</li>
            @endforeach
        </ul>
    </div>

    <div class="mb-4">
        <h2 class="text-xl font-semibold">Social Media</h2>
        <ul class="flex gap-2">
            @foreach($jobSeeker->socialMediaProfiles as $sm)
            <li><a href="{{ $sm->url }}" target="_blank">{{ $sm->socialMediaType->name }}</a></li>
            @endforeach
        </ul>
    </div>
</div>
@endsection
