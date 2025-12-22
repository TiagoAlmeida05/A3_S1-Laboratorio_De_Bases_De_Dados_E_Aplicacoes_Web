<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJobPostingRequest;
use App\Http\Requests\SubmitApplicationSelectionRequest;
use Illuminate\Http\Request;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;

use App\Models\JobPosting;
use App\Models\Application;
use App\Models\City;
use App\Models\Tag;
use App\Events\PlatformAlert;
use App\Models\JobSeeker;
use App\Models\Company;
use Carbon\Carbon;


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

        $recruiter = $jobPosting->recruiter;

        return view('pages.job_posting', [
            'job_posting' => $jobPosting,
            'jobSeeker' => $jobSeeker,
            'hasApplied' => $hasApplied,
            'recruiter' => $recruiter,
        ]);
    }

    public function index(Request $request)
    {
        $searchTerm = $request->input('search');

        $allCities = \App\Models\City::orderBy('name')->get();
        $allCompanies = \App\Models\Company::orderBy('name')->get();
        $allTags = \App\Models\Tag::orderBy('name')->get();

        $jobsQuery = JobPosting::with(['city', 'recruiter.department.company', 'tags'])->where('status', 'Active');

        $companies = collect();
        $jobSeekers = collect();

        if (!empty($searchTerm)) {
            if(strlen($searchTerm) < 3){
                $jobsQuery->where(function($q) use ($searchTerm) {
                    $q->where('title', 'ILIKE', "%{searchTerm}%")
                      ->orWhere('description', 'ILIKE', "%{searchTerm}%")
                      ->orWhere('requirements', 'ILIKE', "%{searchTerm}%");
                })->orderBy('id', 'desc');
                          
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
        }

        if ($request->filled('region')) {
            $jobsQuery->where('city_id', $request->input('region'));
        }
        if ($request->filled('company')) {
            $jobsQuery->whereHas('recruiter.department.company', function($q) use ($request) {
                $q->where('id', $request->input('company'));
            });
        }
        if ($request->filled('tag')) {
            $jobsQuery->whereHas('tags', function($q) use ($request) {
                $q->where('id', $request->input('tag'));
            });
        }
        if($request->filled('min_salary')){
            $minSalary = (int) $request->input('min_salary');
            if($minSalary > 0){
                $jobsQuery->where(function ($query) use ($minSalary){
                    $query->where('min_wage', '>=', $minSalary)
                          ->orwhere('max_wage', '>=', $minSalary);
                            
                });
            }
        }
        $jobPostings = $jobsQuery->get();

        return view('pages.job_postings', [
            'job_postings' => $jobPostings,
            'companies' => $companies ?? collect(),
            'jobSeekers' => $jobSeekers ?? collect(),
            'searchTerm' => $searchTerm,
            'filterCities' => $allCities,
            'filterCompanies' => $allCompanies,
            'filterTags' => $allTags,
        ]);
    }

    public function create(): View {
        Gate::authorize('create-job-posting');

        $cities = City::all();
        $tags = Tag::all();
        return view('job_postings.create', [
            'cities' => $cities,
            'tags' => $tags
        ]);
    }

    public function store(StoreJobPostingRequest $request) {
        Gate::authorize('create-job-posting');
        try {
            $validated = $request->validated();
            
            $recruiter = Auth::user()->recruiter;
            $isManager = $recruiter->is_company_manager;
            $initialStatus = $isManager ? 'Active' : 'Pending';

            DB::beginTransaction();

            $jobPosting = JobPosting::create([
                'title' => $request->title,
                'description' => $request->description,
                'deadline' => $request->deadline,
                'min_wage' => $request->min_wage,
                'max_wage' => $request->max_wage,
                'requirements' => $request->requirements,
                'status' => $initialStatus,
                'recruiter_id' => $recruiter->registered_user_id,
                'city_id' => $request->city_id
            ]);

            if ($request->has('tags')) $jobPosting->tags()->attach($request->tags);
            
            DB::commit();
        } catch (\Exception $e){
            DB::rollBack();
            return back()->with('error', "An error occurred while creating your new job posting. Please try again.");
        }    
        if($initialStatus === 'Active' && $request->has('tags')){
            $interestedSeekers = JobSeeker::whereHas('tags', function($query) use ($request){
                $query->whereIn('tag.id', $request->tags);
            })->get();

            foreach($interestedSeekers as $seeker){
                try{
                    $message = "New Job Alert: '{$jobPosting->title}' matches your interests!";

                    $notifId = DB::table('notification')->insertGetId([
                        'content'=>$message,
                        'notification_type_id' => 5,
                        'registered_user_id' => $seeker->registered_user_id,
                        'issue_date' => now(),
                    ]);

                    event(new PlatformAlert($message, $seeker->registered_user_id, $notifId, 5));
                } catch (\Exception $e) {
                    continue;
                }
                
            }
        }

        if($initialStatus === 'Pending'){
            $companyId = $recruiter->department->company_id;
            $manager = \App\Models\Recruiter::where('is_company_manager', true)
                ->whereHas('department', function($q) use ($companyId) {
                    $q->where('company_id', $companyId);
                })->first();

            if($manager){
                try{
                    $message = "Action Required: A new job posting '{$jobPosting->title}' is pending your approval.";
        
                    $notifId = DB::table('notification')->insertGetId([
                        'content'=>$message,
                        'notification_type_id' => 7,
                        'registered_user_id' => $manager->registered_user_id,
                        'issue_date' => now(),
                    ]);

                    event(new PlatformAlert($message, $manager->registered_user_id, $notifId, 7));
                } catch (\Exception $e) {}
            }
        }
        $message = $isManager
            ? 'Job posting created and published!'
            : 'Job posting created! It is now pending approval by your manager.';

        return redirect()->route('recruiter-dashboard.index')->with('success', $message);
        
    }

    public function edit(JobPosting $job_posting): View {
        Gate::authorize('update', $job_posting);

        $job_posting->load('tags');

        $cities = City::all();
        $tags = Tag::all();
        return view('job_postings.edit', [
            'job_posting' => $job_posting,
            'cities' => $cities,
            'tags' => $tags
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
                'city_id' => $request->city_id
            ]);

            $job_posting->tags()->sync($request->tags ?? []);
    
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
            
            $acceptedApplications = collect();

            if (!empty($selectedIds)) {
                $acceptedApplications = Application::whereIn('id', $selectedIds)
                    ->with('jobPosting')
                    ->get();                  

                Application::whereIn('id', $selectedIds)->update([
                    'evaluated' => true,
                    'accepted' => true
                ]);

                foreach($acceptedApplications as $app){
                    try{
                        $message = "Congratulations! Your application for '{$app->jobPosting->title}' has been accepted.";

                        $notifId = \DB::table('notification')->insertGetId([
                            'content' => $message,
                            'notification_type_id' => 4,
                            'registered_user_id' => $app->job_seeker_id,
                            'issue_date' => now(),
                        ]);

                        event(new PlatformAlert($message, $app->job_seeker_id, $notifId, 4));
                    }catch (\Exception $e) {}                
                }
            }
            
            $rejectedApplications = $job_posting->applications()
                ->whereNotIn('id', $selectedIds)
                ->with('jobPosting')
                ->get();

            foreach($rejectedApplications as $app){
                try{
                    $message = "Thank you for your interest. Unfortunately, your application for '{$app->jobPosting->title}' was not selected at this time.";

                    $notifId = \DB::table('notification')->insertGetId([
                        'content' => $message,
                        'notification_type_id' => 4,
                        'registered_user_id' => $app->job_seeker_id,
                        'issue_date' => now(),
                    ]);

                    event(new PlatformAlert($message, $app->job_seeker_id, $notifId, 4));
            }catch (\Exception $e) {}
                
            }
            
            $creationDate = Carbon::parse($job_posting->creation_date);
            $minDeadline = $creationDate->copy()->addDays(3);
            $finalDeadline = now()->lessThan($minDeadline) ? $minDeadline : now();

            JobPosting::where('id', $job_posting->id)->update([
                'status' => 'Closed',
                'deadline' => $finalDeadline,
            ]);
            
            return redirect()->route('recruiter-dashboard.index')->with('success', 'Applications evaluated and selected, and job posting closed successfully!');
        }
        catch (\Exception $e) {
            \Log::error($e->getMessage());
            return back()->with('error', 'An error occurred while processing your selection. Please try again.');
        }
    }

    public function close(JobPosting $job_posting) {
        Gate::authorize('close', $job_posting);
    
        try {
            $creationDate = Carbon::parse($job_posting->creation_date);
            $minDeadline = $creationDate->copy()->addDays(3);
            $finalDeadline = now()->lessThan($minDeadline) ? $minDeadline : now();

            JobPosting::where('id', $job_posting->id)->update([
                'status' => 'Closed',
                'deadline' => $finalDeadline,
            ]);
            return redirect()->route('recruiter-dashboard.index')->with('success', 'Job posting closed successfully! :)');
        }
        catch(\Exception $e) {
            dd($e->getMessage());
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

    public function approve(JobPosting $job_posting){
        Gate::authorize('update', $job_posting);

        if(!Auth::user()->recruiter->is_company_manager){
            abort(403, 'Unauthorized');
        }
        $job_posting->update(['status' => 'Active']);
        $jobTags = $job_posting->tags()->pluck('tag.id');

        if($jobTags->isNotEmpty()){
            $interestedSeeker = JobSeeker::whereHas('tags', function($query) use ($jobTags){
                $query->whereIn('tag.id', $jobTags);
            })->get();

            foreach($interestedSeeker as $seeker) {
                try{                
                    $message = "New Job Alert: '{$job_posting->title}' matches your interests!";

                    $notifId = DB::table('notification')->insertGetId([
                        'content'=>$message,
                        'notification_type_id' => 5,
                        'registered_user_id' => $seeker->registered_user_id,
                        'issue_date' => now(),
                    ]);

                    event(new PlatformAlert($message, $seeker->registered_user_id, $notifId, 5));
            }catch (\Exception $e) {}
            }
        }

        $creatorId = $job_posting->recruiter->registered_user_id;
        $managerId = Auth::id();

        if($creatorId !== $managerId){
            try{
                $message = "Good News: Your job posting '{$job_posting->title}' has been approved!";
        
                $notifId = DB::table('notification')->insertGetId([
                    'content'=>$message,
                    'notification_type_id' => 6,
                    'registered_user_id' => $creatorId,
                    'issue_date' => now(),
                ]);

                event(new PlatformAlert($message, $creatorId, $notifId, 6));
            }catch (\Exception $e) {}
            
        }

        return back()->with('success', 'Job posting approved successfully!');
    }
}