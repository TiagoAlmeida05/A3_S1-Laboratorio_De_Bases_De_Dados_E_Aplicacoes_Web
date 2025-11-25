<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJobPostingRequest;
use Illuminate\Http\Request;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

use App\Models\JobPosting;
use App\Models\City;

class JobPostingController extends Controller {
    public function show(JobPosting $jobPosting): View {
        Gate::authorize('view', $jobPosting);

        return view('pages.job_posting', [
            'job_posting' => $jobPosting
        ]);
    }

    public function index() {
        Gate::authorize('viewAny', JobPosting::class);
        $jobPostings = JobPosting::with('city')->orderBy('id')->get();

        return view('pages.job_postings', [
            'job_postings' => $jobPostings
        ]);
    }

    public function create() {
        $cities = City::all();
        return view('job_postings.create', [
            'cities' => $cities
        ]);
    }

    public function store(StoreJobPostingRequest $request) {
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
}
