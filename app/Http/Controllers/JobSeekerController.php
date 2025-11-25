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
            'experience.*.start_date' => 'nullable|date',
            'experience.*.end_date' => 'nullable|date',
            'education' => 'array',
            'education.*.name' => 'nullable|string|max:255',
            'education.*.issued_by' => 'nullable|string|max:255',
            'education.*.start_date' => 'nullable|date',
            'education.*.end_date' => 'nullable|date',
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
        $existingExperienceIds = [];
        
        foreach ($experience as $expData) {
            if (isset($expData['id'])) {
                $exp = ExperienceEntry::find($expData['id']);
                if ($exp && $exp->job_seeker_id == $jobSeeker->registered_user_id) {
                    if (!empty($expData['position_name']) && !empty($expData['employer'])) {
                        $exp->update([
                            'position_name' => $expData['position_name'],
                            'employer' => $expData['employer'],
                            'start_date' => $expData['start_date'],
                            'end_date' => $expData['end_date']
                        ]);
                        $existingExperienceIds[] = $exp->id;
                    } else {
                        $exp->delete();
                    }
                }
            } 
            elseif (!empty($expData['position_name']) && !empty($expData['employer'])) {
                $newExp = ExperienceEntry::create([
                    'job_seeker_id' => $jobSeeker->registered_user_id,
                    'position_name' => $expData['position_name'],
                    'employer' => $expData['employer'],
                    'start_date' => $expData['start_date'],
                    'end_date' => $expData['end_date']
                ]);
                $existingExperienceIds[] = $newExp->id;
            }
        }

        $jobSeeker->experienceEntries()->whereNotIn('id', $existingExperienceIds)->delete();
    }

    private function processEducation($jobSeeker, $education)
    {
        $existingEducationIds = [];
        
        foreach ($education as $eduData) {
            if (isset($eduData['id'])) {
                $edu = EducationEntry::find($eduData['id']);
                if ($edu && $edu->job_seeker_id == $jobSeeker->registered_user_id) {
                    if (!empty($eduData['name']) && !empty($eduData['issued_by'])) {
                        $edu->update([
                            'name' => $eduData['name'],
                            'issued_by' => $eduData['issued_by'],
                            'start_date' => $eduData['start_date'],
                            'end_date' => $eduData['end_date']
                        ]);
                        $existingEducationIds[] = $edu->id;
                    } else {
                        $edu->delete();
                    }
                }
            } 
            elseif (!empty($eduData['name']) && !empty($eduData['issued_by'])) {
                $newEdu = EducationEntry::create([
                    'job_seeker_id' => $jobSeeker->registered_user_id,
                    'name' => $eduData['name'],
                    'issued_by' => $eduData['issued_by'],
                    'start_date' => $eduData['start_date'],
                    'end_date' => $eduData['end_date']
                ]);
                $existingEducationIds[] = $newEdu->id;
            }
        }

        $jobSeeker->educationEntries()->whereNotIn('id', $existingEducationIds)->delete();
    }

    private function processCertifications($jobSeeker, $certifications)
    {
        $existingCertificationIds = [];
        
        foreach ($certifications as $certData) {
            if (isset($certData['id'])) {
                $cert = CertificationEntry::find($certData['id']);
                if ($cert && $cert->job_seeker_id == $jobSeeker->registered_user_id) {
                    if (!empty($certData['name']) && !empty($certData['issued_by'])) {
                        $cert->update([
                            'name' => $certData['name'],
                            'issued_by' => $certData['issued_by']
                        ]);
                        $existingCertificationIds[] = $cert->id;
                    } else {
                        $cert->delete();
                    }
                }
            } 
            elseif (!empty($certData['name']) && !empty($certData['issued_by'])) {
                $newCert = CertificationEntry::create([
                    'job_seeker_id' => $jobSeeker->registered_user_id,
                    'name' => $certData['name'],
                    'issued_by' => $certData['issued_by']
                ]);
                $existingCertificationIds[] = $newCert->id;
            }
        }

        $jobSeeker->certifications()->whereNotIn('id', $existingCertificationIds)->delete();
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
}
