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

    public function index(Request $request)
    {
        $searchTerm = $request->input('search');

        $jobsQuery = JobPosting::with('city');

        if (!empty($searchTerm)) {
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
        } else {
            $jobsQuery->orderBy('id');
            $companies = collect();
        }

        $jobPostings = $jobsQuery->get();

        return view('pages.job_postings', [
            'job_postings' => $jobPostings,
            'companies' => $companies,
        ]);
    }

}
