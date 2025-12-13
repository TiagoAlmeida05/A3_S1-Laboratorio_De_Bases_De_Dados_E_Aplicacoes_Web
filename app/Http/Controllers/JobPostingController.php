<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJobPostingRequest;
use App\Http\Requests\SubmitApplicationSelectionRequest;
use Illuminate\Http\Request;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

use App\Models\JobPosting;
use App\Models\Application;
use App\Models\City;

class JobPostingController extends Controller {
    public function show(JobPosting $jobPosting): View {
        $jobPosting->load('recruiter.department.company', 'city');
        Gate::authorize('view', $jobPosting);

        $jobSeeker = null;
        $hasApplied = false;
        
        if (auth()->check()) {
            $jobSeeker = \App\Models\JobSeeker::where('registered_user_id', auth()->id())->first();

            if ($jobSeeker) {
                $hasApplied = Application::where('job_seeker_id', $jobSeeker->registered_user_id)
                    ->where('job_posting_id', $jobPosting->id)
                    ->exists();
            }
        }

        return view('pages.job_posting', [
            'job_posting' => $jobPosting,
            'jobSeeker' => $jobSeeker,
            'hasApplied' => $hasApplied
        ]);
    }

    public function index(Request $request)
    {
        $searchTerm = $request->input('search');

        $jobsQuery = JobPosting::with('city');
        $companies = collect();
        $jobSeekers = collect();

        if (!empty($searchTerm)) {
            if(strlen($searchTerm < 3)){
                $jobsQuery->where('title', 'ILIKE', "%{searchTerm}%")
                          ->orderBy('id', 'desc');

                $companies = \App\Models\Company::with('city')
                                ->where('name', 'ILIKE', "%{$searchTerm}%")
                                ->orderBy('name')
                                ->get();               
            }else{
                $words = explode(' ', trim($searchTerm));
                $prefixSearch = implode(':* & ', $words) . ':*';

                $jobsQuery->whereRaw(
                    "tsvectors @@ to_tsquery('english', ?)",
                    [$prefixSearch]
                )->selectRaw(
                    "*, ts_rank(tsvectors, to_tsquery('english', ?)) as rank",
                    [$prefixSearch]
                )->orderBy('rank', 'desc');

                $companies = \App\Models\Company::with('city')
                    ->whereRaw("tsvectors @@ to_tsquery('english', ?)", [$prefixSearch])
                    ->orderBy('name')
                    ->get();                
            }

            $searchWords = explode(' ', $searchTerm);
            $jobSeekers = \App\Models\JobSeeker::with(['registeredUser', 'city'])->where(function ($query) use ($searchWords) {
                    foreach ($searchWords as $word) {
                        $query->orWhereHas('registeredUser', function ($q) use ($word) {
                            $q->where('name', 'ILIKE', "%{$word}%");
                        })->orWhere('about_me', 'ILIKE', "%{$word}%")->orWhereHas('tags', function ($q) use ($word) {
                            $q->where('name', 'ILIKE', "%{$word}%");
                        });
                    }
                })->get();

        } else {
            $jobsQuery->orderBy('id');
            $companies = collect();
        }

        $jobPostings = $jobsQuery->get();

        return view('pages.job_postings', [
            'job_postings' => $jobPostings,
            'companies' => $companies,
            'jobSeekers' => $jobSeekers,
            'searchTerm' => $searchTerm
        ]);
    }

    public function create(): View {
        Gate::authorize('create-job-posting');

        $cities = City::all();
        return view('job_postings.create', [
            'cities' => $cities
        ]);
    }

    public function store(StoreJobPostingRequest $request) {
        Gate::authorize('create-job-posting');
        try {
            $validated = $request->validated();
    
            JobPosting::create([
                'title' => $request->title,
                'description' => $request->description,
                'deadline' => $request->deadline,
                'min_wage' => $request->min_wage,
                'max_wage' => $request->max_wage,
                'requirements' => $request->requirements,
                'status' => 'Pending',
                'recruiter_id' => Auth::user()->recruiter->registered_user_id,
                'city_id' => $request->city_id
            ]);
    
            return redirect()->route('recruiter-dashboard.index')->with('success', 'New job posting created successfully! :)');
        }
        catch (\Exception $e){
            return back()->with('error', "An error occurred while creating your new job posting. Please try again.");
        }
    }

    public function edit(JobPosting $job_posting): View {
        Gate::authorize('update', $job_posting);

        $cities = City::all();
        return view('job_postings.edit', [
            'job_posting' => $job_posting,
            'cities' => $cities
        ]);
    }

    public function update(StoreJobPostingRequest $request, JobPosting $job_posting) {
        Gate::authorize('update', $job_posting);
        try {
            $validated = $request->validated();
    
            $job_posting->update([
                'title' => $request->title,
                'description' => $request->description,
                'deadline' => $request->deadline,
                'min_wage' => $request->min_wage,
                'max_wage' => $request->max_wage,
                'requirements' => $request->requirements,
                'status' => $request->status,
                'recruiter_id' => Auth::user()->recruiter->registered_user_id,
                'city_id' => $request->city_id
            ]);
    
            return redirect()->route('recruiter-dashboard.index')->with('success', 'Job posting updated successfully! :)');
        }
        catch(\Exception $e) {
            return back()->with('error', "An error occurred while editing your job posting. Please try again.");
        }
    }

    public function manageApplications(JobPosting $job_posting, Request $request) {
        Gate::authorize('close', $job_posting);
        
        $applications = $job_posting->applications()
            ->with(['jobSeeker.registeredUser'])
            ->orderBy('date', 'desc')
            ->get();
        
        return view('recruiter.manage_applications', [
            'job_posting' => $job_posting,
            'applications' => $applications,
            'selectMode' => $request->boolean('selectMode'),
        ]);
    }

    public function viewApplication(Application $application) {       
        $application->load(['jobSeeker.registeredUser', 'jobPosting']);
        Gate::authorize('view', $application);
        
        return view('applications.view-application', [
            'application' => $application
        ]);
    }

    public function viewApplicationOfClosedJob(Application $application) {   
        $application->load(['jobSeeker.registeredUser', 'jobPosting']);
        Gate::authorize('view', $application);

        return view('applications.view-application-closed-job', [
            'application' => $application
        ]);
    }

    public function viewApplicationsOfClosedJobs(JobPosting $job_posting) {
        Gate::authorize('viewApplicationsOfClosedJobs', $job_posting);
        
        $applications = $job_posting->applications()
            ->with(['jobSeeker.registeredUser'])
            ->orderBy('date', 'desc')
            ->get();
        
        return view('applications.view-applications-closed-job', [
            'job_posting' => $job_posting,
            'applications' => $applications
        ]);
    }

    public function submitApplicationSelection(SubmitApplicationSelectionRequest $request, JobPosting $job_posting) {
        Gate::authorize('close', $job_posting);
        
        try {
            $validated = $request->validated();
            $selectedIds = $validated['selected_applications'] ?? [];
                    
            $job_posting->applications()->update([
                'evaluated' => true,
                'accepted' => false
            ]);
            
            if (!empty($selectedIds)) {
                Application::whereIn('id', $selectedIds)->update([
                    'accepted' => true
                ]);
            }
            
            $job_posting->update([
                'status' => 'Closed',
                'deadline' => now()->setTimezone('Europe/Lisbon')->toDateString()
            ]);
            
            return redirect()->route('recruiter-dashboard.index')->with('success', 'Applications evaluated and selected, and job posting closed successfully!');
        }
        catch (\Exception $e) {
            return back()->with('error', 'An error occurred while processing your selection. Please try again.');
        }
    }

    public function close(JobPosting $job_posting) {
        Gate::authorize('close', $job_posting);
    
        try {
            $job_posting->update([
                'status' => 'Closed',
                'deadline' => now()->setTimezone('Europe/Lisbon')->toDateString()
            ]);

            return redirect()->route('recruiter-dashboard.index')->with('success', 'Job posting closed successfully! :)');
        }
        catch(\Exception $e) {
            return back()->with('error', "An error occurred while closing your job posting. Please try again.");
        }
    }

    public function delete($job_posting_id) {
        try {
            $job_posting = JobPosting::withCount('applications')->findOrFail($job_posting_id);
            Gate::authorize('delete', $job_posting);
    
            $job_posting->delete();
            return redirect()->route('recruiter-dashboard.index')->with('success', 'Job posting deleted successfully! :)');
        }
        catch (\Exception $e){
            return back()->with('error', "An error occurred while deleting your job posting. Please try again.");
        }
    }
}