<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\JobSeeker;
use App\Models\Tag;
use App\Models\City;
use App\Models\ExperienceEntry;
use App\Models\EducationEntry;
use App\Models\CertificationEntry;
use App\Models\AwardEntry;
use App\Models\Application;
use App\Models\JobPosting;

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
            'tags.*' => 'exists:tag,id',
            'experience' => 'array',
            'experience.*.position_name' => 'nullable|string|max:255',
            'experience.*.employer' => 'nullable|string|max:255',
            'experience.*.start_date' => 'required|date',
            'experience.*.end_date' => 'required|date',
            'education' => 'array',
            'education.*.name' => 'nullable|string|max:255',
            'education.*.issued_by' => 'nullable|string|max:255',
            'education.*.start_date' => 'nullable|date',
            'education.*.end_date' => 'required|date',
            'certifications' => 'array',
            'certifications.*.name' => 'nullable|string|max:255',
            'certifications.*.issued_by' => 'nullable|string|max:255',
            'awards' => 'array',
            'awards.*.name' => 'nullable|string|max:255'
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
        
        if (isset($validated['experience'])) {
            $this->processExperience($jobSeeker, $validated['experience']);
        }

        if (isset($validated['education'])) {
            $this->processEducation($jobSeeker, $validated['education']);
        }

        if (isset($validated['certifications'])) {
            $this->processCertifications($jobSeeker, $validated['certifications']);
        }

        if (isset($validated['awards'])) {
            $this->processAwards($jobSeeker, $validated['awards']);
        }

        return redirect()->route('jobseeker.profile', $jobSeeker->registered_user_id)
            ->with('success', 'Profile updated!');
    }

    private function processExperience($jobSeeker, $experience)
    {
        $existingExperienceIds = $jobSeeker->experienceEntries->pluck('id')->toArray();
        $submittedIds = [];

        foreach ($experience as $expData) {
            if (empty($expData['position_name']) || empty($expData['employer']) || empty($expData['start_date']) || empty($expData['end_date'])) {
                continue;
            }

            if (isset($expData['id']) && in_array($expData['id'], $existingExperienceIds)) {
                $exp = ExperienceEntry::find($expData['id']);

                $exp->update([
                    'position_name' => $expData['position_name'],
                    'employer'      => $expData['employer'],
                    'start_date'    => $expData['start_date'],
                    'end_date'      => $expData['end_date'],
                ]);

                $submittedIds[] = $exp->id;
            }

            elseif (!isset($expData['id'])) {
                $newExp = ExperienceEntry::create([
                    'job_seeker_id' => $jobSeeker->registered_user_id,
                    'position_name' => $expData['position_name'],
                    'employer'      => $expData['employer'],
                    'start_date'    => $expData['start_date'],
                    'end_date'      => $expData['end_date'],
                ]);

                $submittedIds[] = $newExp->id;
            }
        }

        if (!empty($submittedIds)) {
            ExperienceEntry::where('job_seeker_id', $jobSeeker->registered_user_id)->whereNotIn('id', $submittedIds)->delete();
        }
    }

    private function processEducation($jobSeeker, $education)
    {
        $existingIds = $jobSeeker->educationEntries->pluck('id')->toArray();
        $submittedIds = [];

        foreach ($education as $eduData) {
            if (empty($eduData['name']) || empty($eduData['issued_by']) || empty($eduData['end_date'])) {
                continue;
            }

            if (isset($eduData['id']) && in_array($eduData['id'], $existingIds)) {
                $edu = EducationEntry::find($eduData['id']);

                $edu->update([
                    'name'          => $eduData['name'],
                    'issued_by'     => $eduData['issued_by'],
                    'start_date'    => !empty($eduData['start_date']) ? $eduData['start_date'] : null,
                    'end_date'      => $eduData['end_date'],
                ]);

                $submittedIds[] = $edu->id;
            }
            elseif (!isset($eduData['id'])) {
                $newEdu = EducationEntry::create([
                    'job_seeker_id' => $jobSeeker->registered_user_id,
                    'name'          => $eduData['name'],
                    'issued_by'     => $eduData['issued_by'],
                    'start_date'    => !empty($eduData['start_date']) ? $eduData['start_date'] : null,
                    'end_date'      => $eduData['end_date'],
                ]);

                $submittedIds[] = $newEdu->id;
            }
        }

        if (!empty($submittedIds)) {
            EducationEntry::where('job_seeker_id', $jobSeeker->registered_user_id)->whereNotIn('id', $submittedIds)->delete();
        }
    }


     private function processCertifications($jobSeeker, $certifications)
    {
        $existingIds = $jobSeeker->certifications->pluck('id')->toArray();
        $submittedIds = [];

        foreach ($certifications as $certData) {
            if (empty($certData['name'])) {
                continue;
            }

            if (isset($certData['id']) && in_array($certData['id'], $existingIds)) {
                $cert = CertificationEntry::find($certData['id']);
                $cert->update([
                    'name' => $certData['name'],
                    'issued_by' => $certData['issued_by'] ?? null,
                ]);
                $submittedIds[] = $cert->id;
            }
            elseif (!isset($certData['id'])) {
                $newCert = CertificationEntry::create([
                    'job_seeker_id' => $jobSeeker->registered_user_id,
                    'name' => $certData['name'],
                    'issued_by' => $certData['issued_by'] ?? null,
                ]);
                $submittedIds[] = $newCert->id;
            }
        }

        if (!empty($submittedIds)) {
            CertificationEntry::where('job_seeker_id', $jobSeeker->registered_user_id)->whereNotIn('id', $submittedIds)->delete();
        }
    }

    private function processAwards($jobSeeker, $awards)
    {
        $existingAwardIds = [];
        
        foreach ($awards as $awardData) {
            if (isset($awardData['id'])) {
                $award = AwardEntry::find($awardData['id']);
                if ($award && $award->job_seeker_id == $jobSeeker->registered_user_id) {
                    if (!empty($awardData['name'])) {
                        $award->update(['name' => $awardData['name']]);
                        $existingAwardIds[] = $award->id;
                    } else {
                        $award->delete();
                    }
                }
            } 
            elseif (!empty($awardData['name'])) {
                $newAward = AwardEntry::create([
                    'job_seeker_id' => $jobSeeker->registered_user_id,
                    'name' => $awardData['name']
                ]);
                $existingAwardIds[] = $newAward->id;
            }
        }

        $jobSeeker->awards()->whereNotIn('id', $existingAwardIds)->delete();
    }

    public function applyForm($jobPostingId)
    {
        $jobPosting = JobPosting::findOrFail($jobPostingId);
        
        return view('jobseeker.apply', compact('jobPosting'));
    }

    public function storeApplication(Request $request, $jobPostingId)
    {
        $validated = $request->validate([
            'cover_letter' => 'required|file|mimes:pdf,doc,docx|max:2048',
            'recommendation_letter' => 'required|file|mimes:pdf,doc,docx|max:2048',
        ]);

        Application::create([
            'job_seeker_id' => auth()->id(),
            'job_posting_id' => $jobPostingId,
            'cover_letter' => $validated['cover_letter'],
            'recommendation_letter' => $validated['recommendation_letter'],
            'date' => now(),
            'evaluated' => false,
            'accepted' => false,
        ]);

        return redirect()->route('pages.job_posting')->with('success', 'Application submitted successfully!');
    }
}
