<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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
        $jobSeeker = JobSeeker::where('registered_user_id', Auth::id())->firstOrFail();
        $validated = $request->validate([
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'about_me' => 'nullable|string|max:1000',
            'website' => 'nullable|url|max:255',
            'cv' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'show_cv' => 'required|boolean',
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

        if ($request->hasFile('profile_photo')){
            $validated['profile_photo'] = $request->file('profile_photo')->store('profile_photos', 'public');
        }

        if ($request->hasFile('cv')) {
            $validated['cv'] = $request->file('cv')->store('cvs', 'public');
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
        try {
            $validated = $request->validate([
                'cv' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
                'cover_letter' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
                'recommendation_letter' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            ]);

            $applicationData = [
                'job_seeker_id' => auth()->id(),
                'job_posting_id' => $jobPostingId,
                'date' => now(),
                'evaluated' => false,
                'accepted' => false,
            ];

            if ($request->hasFile('cv')) {
                $applicationData['cv'] = $request->file('cv')->store('application_cvs', 'public');
            }

            if ($request->hasFile('cover_letter')) {
                $applicationData['cover_letter'] = $request->file('cover_letter')->store('cover_letters', 'public');
            }

            if ($request->hasFile('recommendation_letter')) {
                $applicationData['recommendation_letter'] = $request->file('recommendation_letter')->store('recommendation_letters', 'public');
            }

            Application::create($applicationData);

            return redirect()->route('job_postings.show', $jobPostingId)->with('success', 'Application submitted successfully!');

        } catch (\Illuminate\Database\QueryException $e) {
            if (str_contains($e->getMessage(), 'has already applied to job posting')) {
                return redirect()->route('job_postings.show', $jobPostingId)
                    ->with('error', 'You have already applied to this job posting.');
            }
            
            throw $e;
        }
    }

    public function applications()
    {
        $applications = Application::with(['jobPosting.company', 'jobPosting.city'])
            ->where('job_seeker_id', Auth::id())
            ->orderBy('date', 'desc')
            ->paginate(10);
            
        return view('jobseeker.applications', compact('applications'));
    }

    public function editApplication($applicationId)
    {
        $application = Application::with(['jobPosting'])
            ->where('job_seeker_id', Auth::id())
            ->findOrFail($applicationId);
            
        return view('jobseeker.application-edit', compact('application'));
    }

    public function updateApplication(Request $request, $applicationId)
    {
        $application = Application::where('job_seeker_id', Auth::id())
            ->findOrFail($applicationId);
        
        $validated = $request->validate([
            'cv' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'cover_letter' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'recommendation_letter' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);
        
        if ($request->hasFile('cv')) {
            $validated['cv'] = $request->file('cv')->store('application_cvs', 'public');
        }
        
        if ($request->hasFile('cover_letter')) {
            $validated['cover_letter'] = $request->file('cover_letter')->store('cover_letters', 'public');
        }
        
        if ($request->hasFile('recommendation_letter')) {
            $validated['recommendation_letter'] = $request->file('recommendation_letter')->store('recommendation_letters', 'public');
        }
        
        $application->update($validated);
        
        return redirect()->route('jobseeker.applications')
            ->with('success', 'Application updated successfully!');
    }

    public function deleteApplicationFile($applicationId, $fileType)
    {
        $application = Application::where('job_seeker_id', Auth::id())
            ->findOrFail($applicationId);
        
        if ($fileType === 'cv' && $application->cv) {
            Storage::disk('public')->delete($application->cv);
            $application->cv = null;
        } elseif ($fileType === 'cover_letter' && $application->cover_letter) {
            Storage::disk('public')->delete($application->cover_letter);
            $application->cover_letter = null;
        } elseif ($fileType === 'recommendation_letter' && $application->recommendation_letter) {
            Storage::disk('public')->delete($application->recommendation_letter);
            $application->recommendation_letter = null;
        }
        
        $application->save();
        
        return back()->with('success', 'File deleted successfully!');
    }

    public function cancelApplication($applicationId)
    {
        $application = Application::where('job_seeker_id', Auth::id())
            ->findOrFail($applicationId);
            
        if ($application->cv) {
            Storage::disk('public')->delete($application->cv);
        }
        if ($application->cover_letter) {
            Storage::disk('public')->delete($application->cover_letter);
        }
        if ($application->recommendation_letter) {
            Storage::disk('public')->delete($application->recommendation_letter);
        }
        
        $application->delete();
        
        return redirect()->route('jobseeker.applications')
            ->with('success', 'Application cancelled successfully!');
    }

    public function searchJobSeekers(Request $request)
    {
        $search = $request->input('search');

        $jobSeekers = JobSeeker::with(['registeredUser', 'city', 'tags'])->when($search, function ($query, $search) {
                $searchTerms = explode(' ', $search);
                
                $query->where(function ($q) use ($searchTerms) {
                    foreach ($searchTerms as $term) {
                        $q->whereHas('registeredUser', function ($userQuery) use ($term) {
                            $userQuery->where('name', 'LIKE', "%{$term}%");
                        })->orWhere('about_me', 'LIKE', "%{$term}%")->orWhereHas('tags', function ($tagQuery) use ($term) {
                            $tagQuery->where('name', 'LIKE', "%{$term}%");
                        });
                    }
                });
            })->get();

        return view('partials.job_seeker_part', compact('jobSeekers'));
    }
}
