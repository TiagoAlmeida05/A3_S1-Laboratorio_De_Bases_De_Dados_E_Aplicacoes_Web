<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\JobSeeker;
use App\Models\Tag;
use App\Models\City;

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
            'socialMediaProfiles',
            'city'
        ])->findOrFail($registered_user_id);

        return view('jobseeker.profile', compact('jobSeeker'));
    }

    public function edit() 
    {
        $jobSeeker = JobSeeker::with(['registeredUser'])->findOrFail(Auth::id());
        $tags = Tag::all();
        $cities = City::all();

        return view('jobseeker.profile-edit', compact('jobSeeker', 'tags', 'cities'));
    }

    public function update(Request $request)
    {
        $jobSeeker = JobSeeker::findOrFail(Auth::id());
        $validated = $request->validate([
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'about_me' => 'nullable|string|max:1000',
            'website' => 'nullable|url|max:255',
            'cv' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'show_cv' => 'boolean',
            'city_id' => 'nullable|exists:city,id',
            'tags' => 'array',
            'tags.*' => 'exists:tag,id'
        ]);

        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $validated['profile_photo'] = $path;
        }

        if ($request->hasFile('cv')) {
            $path = $request->file('cv')->store('cvs', 'public');
            $validated['cv'] = $path;
        }

        $jobSeeker->update($validated);

        if (isset($validated['tags'])) {
            $jobSeeker->tags()->sync($validated['tags']);
        }

        return redirect()->route('jobseeker.profile', $jobSeeker->registered_user_id)
            ->with('success', 'Perfil atualizado com sucesso!');
    }
}
