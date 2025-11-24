<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

use App\Models\JobPosting;

class JobPostingController extends Controller {
    public function show(JobPosting $jobPosting): View {
        Gate::authorize('view', $jobPosting);

        return view('pages.job_posting', [
            'job_posting' => $jobPosting
        ]);
    }

    public function index() {
        $jobPostings = JobPosting::with('city')->orderBy('id')->get();

        return view('pages.job_postings', [
            'job_postings' => $jobPostings
        ]);
    }

}
