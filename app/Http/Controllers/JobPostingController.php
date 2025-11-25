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

    public function index(Request $request) {
        $query = JobPosting::with('city');
        
        // Full-text search
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');

            $words = explode(' ', trim($searchTerm));
            $prefixSearch = implode(':* & ', $words) . ':*';

            $query->whereRaw(
                "tsvectors @@ to_tsquery('english', ?)",
                [$prefixSearch]
            )->selectRaw(
                "*, ts_rank(tsvectors, to_tsquery('english', ?)) as rank",
                [$prefixSearch]
            )->orderBy('rank', 'desc');
        } else {
            $query->orderBy('id');
        }
        
        $jobPostings = $query->get();
        
        return view('pages.job_postings', [
            'job_postings' => $jobPostings
        ]);
    }

}
