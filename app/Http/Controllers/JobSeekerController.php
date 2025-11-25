<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JobSeeker;

class JobSeekerController extends Controller
{
    public function show($registered_user_id)
    {
        $jobSeeker = JobSeeker::with([
            'registeredUser',
            'applications.jobPosting',
            'certifications',
            'experienceEntries',
            'educationEntries',
            'awards',
            'tags',
            'socialMediaProfiles'
        ])->findOrFail($registered_user_id);

        return view('jobseeker.profile', compact('jobSeeker'));
    }
}
