<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\JobPosting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RecruiterController extends Controller {
    public function index(Request $request): View {
        $user = Auth::user();
        $recruiter = $user->recruiter;

        $viewMode = $request->get('view', 'personal');

        if($viewMode === 'company' && $recruiter->is_company_manager){
            $job_postings = JobPosting::forCompany($recruiter->department->company_id)
                ->with(['recruiter.department'])
                ->withCount('applications')
                ->orderBy('creation_date', 'desc')
                ->get();
        }else{
             $job_postings = $recruiter->job_postings()
                ->with(['recruiter.department'])
                ->withCount('applications')
                ->orderBy('creation_date', 'desc')
                ->get();
            
            $viewMode = 'personal';
        }

        return view('recruiter.dashboard', [
            'user' => $user,
            'job_postings' => $job_postings,
            'viewMode' => $viewMode
        ]);
    }

    public function statistics(): View {
        $user = Auth::user();
        $recruiter = $user->recruiter;
        $oneMonthAgo = now()->setTimezone('Europe/Lisbon')->subMonth();
        
        $allJobPostings = $recruiter->job_postings()->withCount('applications')->get();
        
        // ~~ Total job counts (non-deleted): all jobs, active, pending, expired, and closed ~~
        $totalJobs = $allJobPostings->count();
        $totalActiveJobs = $allJobPostings->where('status', 'Active')->count();
        $totalPendingJobs = $allJobPostings->where('status', 'Pending')->count();
        $totalExpiredJobs = $allJobPostings->where('status', 'Expired')->count();
        $totalClosedJobs = $allJobPostings->where('status', 'Closed')->count();
        
        // ~~ Total application counts ~~
        $allApplications = Application::whereIn('job_posting_id', $allJobPostings->pluck('id'))->get();
        
        $totalApplications = $allApplications->count();
        $totalAcceptances = $allApplications->where('accepted', true)->count();
        $acceptanceRate = $totalApplications > 0 ? round(($totalAcceptances / $totalApplications) * 100, 2) : 0;
        
        // ~~ Data from the past month (based on day, not on number of days before today, so from today/month - 1 to today/month)
        $newJobsCreatedThisMonth = $recruiter->job_postings()
            ->where('creation_date', '>=', $oneMonthAgo)
            ->get();
        
        $totalJobsCreatedThisMonth = $newJobsCreatedThisMonth->count();
        $activeJobsCreatedThisPastMonth = $newJobsCreatedThisMonth->where('status', 'Active')->count();
        $pendingJobsCreatedThisPastMonth = $newJobsCreatedThisMonth->where('status', 'Pending')->count();
        $expiredJobsCreatedThisPastMonth = $newJobsCreatedThisMonth->where('status', 'Expired')->count();
        $closedJobsCreatedThisPastMonth = $newJobsCreatedThisMonth->where('status', 'Closed')->count();
        
        $applicationsSubmittedThisPastMonth = Application::whereIn('job_posting_id', $allJobPostings->pluck('id'))
            ->where('date', '>=', $oneMonthAgo)
            ->get();
        
        $totalApplicationsSubmittedThisPastMonth = $applicationsSubmittedThisPastMonth->count();
        $acceptancesForApplicationsSubmittedThisMonth = $applicationsSubmittedThisPastMonth->where('accepted', true)->count();
        
        $currentlyActiveJobs = $allJobPostings->where('status', 'Active');
        $currentlyExpiredJobs = $allJobPostings->where('status', 'Expired');
        
        return view('recruiter.statistics', [
            'user' => $user,
            'recruiter' => $recruiter,
            'totalJobs' => $totalJobs,
            'totalActiveJobs' => $totalActiveJobs,
            'totalPendingJobs' => $totalPendingJobs,
            'totalExpiredJobs' => $totalExpiredJobs,
            'totalClosedJobs' => $totalClosedJobs,
            'totalApplications' => $totalApplications,
            'totalAcceptances' => $totalAcceptances,
            'acceptanceRate' => $acceptanceRate,
            'totalJobsCreatedThisMonth' => $totalJobsCreatedThisMonth,
            'activeJobsCreatedThisPastMonth' => $activeJobsCreatedThisPastMonth,
            'pendingJobsCreatedThisPastMonth' => $pendingJobsCreatedThisPastMonth,
            'expiredJobsCreatedThisPastMonth' => $expiredJobsCreatedThisPastMonth,
            'closedJobsCreatedThisPastMonth' => $closedJobsCreatedThisPastMonth,
            'totalApplicationsSubmittedThisPastMonth' => $totalApplicationsSubmittedThisPastMonth,
            'acceptancesForApplicationsSubmittedThisMonth' => $acceptancesForApplicationsSubmittedThisMonth,
            'currentlyActiveJobs' => $currentlyActiveJobs,
            'currentlyExpiredJobs' => $currentlyExpiredJobs,
        ]);
    }

    public function destroy(Request $request)
    {
        $user = Auth::user();

        if (!$user->recruiter) {
            return redirect('/')->with('error', 'Unauthorized action.');
        }

        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        DB::transaction(function () use ($user) {
            $recruiter = $user->recruiter;

            $recruiter->job_postings()
                ->whereIn('status', ['Active', 'Pending'])
                ->update(['status' => 'Closed']);

            $user->name = 'Deleted Recruiter ' . $user->id;
            $user->email = 'deleted_' . $user->id . '@hireup.com';
            $user->password = Hash::make(uniqid());
            $user->status = 'Deleted';
            $user->save();
        });

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Your recruiter account has been deleted and active jobs were closed.');
    }
}
