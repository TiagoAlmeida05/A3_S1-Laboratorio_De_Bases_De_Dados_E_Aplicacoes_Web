<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\JobPosting;
use App\Models\Department;
use App\Models\Recruiter;
use App\Models\JobSeeker;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Events\PlatformAlert;

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
            
            if($viewMode !== 'staff') $viewMode = 'personal';
        }

        $companyStaff = null;
        $departments = collect();

        if($viewMode === 'staff' && $recruiter->is_company_manager){
            $companyId = $recruiter->department->company_id;
            $departments = Department::where('company_id', $companyId)->get();
            $companyStaff = Recruiter::whereHas('department', function($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })->with(['user', 'department'])->get();
        }

        return view('recruiter.dashboard', [
            'user' => $user,
            'job_postings' => $job_postings,
            'viewMode' => $viewMode,
            'companyStaff' => $companyStaff,
            'departments' => $departments
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

    public function promoteToRecruiter(Request $request){
        $manager = Auth::user()->recruiter;

        if(!$manager || !$manager->is_company_manager){
            return back()->with('error', 'Unauthorized action.');
        }

        $request->validate([
            'email' => 'required|email|exists:registered_user,email',
            'department_id' => 'required|exists:department,id'
        ]);

        $targetDepartment = Department::findOrFail($request->department_id);
        if($targetDepartment->company_id !== $manager->department->company_id){
            return back()->with('error', 'Invalid department selected..');
        }

        $targetUser = User::where('email', $request->email)->first();

        if($targetUser->administrator()->exists()){
            return back()->with('error', 'Cannot promote an Administrator.');
        }

        if($targetUser->recruiter()->exists()){
            return back()->with('error', 'User is already recruiter.');
        }

        try {
            DB::transaction(function () use ($targetUser, $request){
                $originalData = [
                    'name' => $targetUser->name,
                    'email' => $targetUser->email,
                    'password' => $targetUser->password,
                    'birthday' => $targetUser->birthday,
                    'age' => $targetUser->age,
                    'sign_up_date' => $targetUser->sign_up_date,
                    'status' => 'Active',
                ];
                
                $jobSeeker = $targetUser->jobSeeker;

                if($jobSeeker){
                    if($jobSeeker->cv) \Storage::disk('public')->delete($jobSeeker->cv);
                    if($jobSeeker->profile_photo) \Storage::disk('public')->delete($jobSeeker->profile_photo);

                    $jobSeeker->cv = null;
                    $jobSeeker->profile_photo = null;
                    $jobSeeker->about_me = null;
                    $jobSeeker->website = null;
                    $jobSeeker->show_cv = false;
                    $jobSeeker->city_id = null;
                    $jobSeeker->save();

                    $jobSeeker->experienceEntries()->delete();
                    $jobSeeker->educationEntries()->delete();
                    $jobSeeker->certifications()->delete();
                    $jobSeeker->awards()->delete();
                    $jobSeeker->socialMediaProfiles()->delete();
                    $jobSeeker->tags()->detach();

                    if(method_exists($jobSeeker, 'bookmarks')){
                        $jobSeeker->bookmarks()->detach();
                    }
                }
                
                $oldUserIdToLogOut = $targetUser->id;
                $targetUser->name = 'Deleted User ' . $targetUser->id;
                $targetUser->email = 'deleted_' . $targetUser->id . '@hireup.com';
                $targetUser->password = \Illuminate\Support\Facades\Hash::make(uniqid());
                $targetUser->status = 'Deleted';
                $targetUser->save();

                

                $newUser = User::create($originalData);

                Recruiter::create([
                    'registered_user_id' =>$newUser->id,
                    'department_id' => $request->department_id,
                    'is_company_manager' => false
                ]);
                try{
                    $message = 'Congratulations! You have been promoted to Recruiter.';
                    $notifId = DB::table('notification')->insertGetId([
                        'content' => $message,
                        'notification_type_id' => 1,
                        'registered_user_id' => $newUser->id,
                        'issue_date' => now(),
                    ]);

                    event(new PlatformAlert($message, $newUser->id, $notifId, 1));
            } catch (\Exception $e) {}
                
            });
            
            if($oldUserIdToLogOut){
                    try{
                        if(\Schema::hasTable('sessions')){
                            DB::table('sessions')->where('user_id', $oldUserIdToLogOut)->delete();
                        }                       
                    }catch(\Exception $e) {

                    }
                }
            
            return back()->with('success', "{$request->name} has been promoted to Recruiter.");      
        }catch(\Exception $e) {
            return back()->with('error', 'Error promoting user: ' . $e->getMessage());
        }
    }

    public function demoteToJobSeeker(Request $request, $recruiterId) {
        $manager = Auth::user()->recruiter;

        if(!$manager || !$manager->is_company_manager){
            return back()->with('error', 'Unauthorized action.');
        }

        $targetRecruiter = Recruiter::findOrFail($recruiterId);
        $targetUser = $targetRecruiter->user;

        if($targetRecruiter->department->company_id !== $manager->department->company_id){
            return back()->with('error', 'This recruiter does not belong to your company.');
        }
        if($targetRecruiter->registered_user_id === $manager->registered_user_id){
            return back()->with('error', 'You cannot demote yourself.');
        }

        try {
            DB::transaction(function () use ($targetRecruiter, $targetUser, $manager){
                JobPosting::where('recruiter_id', $targetRecruiter->registered_user_id)
                    ->update(['recruiter_id' => $manager->registered_user_id]);

                $targetRecruiter->delete();

                JobSeeker::firstOrcreate([
                    'registered_user_id' =>$targetUser->id,
                ]);
                try{
                    $message = 'Your role has been changed to Job Seeker. Your active jobs were transferred to the manager.';
                    $notifId = DB::table('notification')->insertGetId([
                        'content' => $message,
                        'notification_type_id' => 1,
                        'registered_user_id' => $targetUser->id,
                        'issue_date' => now(),
                    ]);

                    event(new PlatformAlert($message, $targetUser->id, $notifId, 1));
                } catch (\Exception $e) {}                
            });
            return back()->with('success', "{$targetUser->name} is now a Job Seeker.");
        }catch(\Exception $e) {
            return back()->with('error', 'Error demoting user: ' . $e->getMessage());
        }
    }
}
