<?php

namespace App\Policies;

use App\Models\JobPosting;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Ap\Models\Recruiter;

class JobPostingPolicy
{
    use HandlesAuthorization;
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, JobPosting $jobPosting): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isRecruiter();
    }

    public function viewApplicationsOfClosedJobs(User $user, JobPosting $job_posting): bool {
        if ($user->isAdmin()) return true;
        
        if($this->isManager($user, $job_posting)) return true;

        if (!$user->recruiter) return false;
        
        return $job_posting->recruiter_id === $user->id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, JobPosting $job_posting): bool {
        if ($user->isAdmin()) return $job_posting->status !== 'Closed';

        if ($job_posting->recruiter_id === $user->id) return $job_posting->status !== 'Closed';
        
        if($this->isManager($user, $job_posting)) return $job_posting->status !== 'Closed';

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, JobPosting $job_posting): bool {
        if ($user->isAdmin()) return true;

        if ($job_posting->recruiter_id === $user->id){
            if ($job_posting->status === 'Pending') return true;
            if (in_array($job_posting->status, ['Active', 'Expired'])) return $job_posting->applications_count === 0;
        }

        if($this->isManager($user, $job_posting)){
            return true;
        }

        return false;
    }

    public function close(User $user, JobPosting $job_posting): bool {
        if ($user->isAdmin()) return $job_posting->status !== 'Closed';

        $recruiter = $user->recruiter;

        if ($job_posting->recruiter_id === $user->id){
            return in_array($job_posting->status, ['Active', 'Expired']);
        }
        
        if($this->isManager($user, $job_posting)){
            return in_array($job_posting->status, ['Active', 'Expired']);
        }
        
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, JobPosting $jobPosting): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, JobPosting $jobPosting): bool
    {
        return false;
    }

    private function isManager(User $user, JobPosting $job): bool
    {
        if(!$user->isRecruiter()) return false;
        $recruiter = $user->recruiter;

        if(!$recruiter->is_company_manager) return false;

        $jobRecruiter = $job->recruiter;

        if(!$jobRecruiter){
            $jobRecruiter = Recruiter::where('registered_user_id', $job->recruiter_id)->first();
        }

        if(!$jobRecruiter) return false;

        $jobCompanyId = $job->recruiter->department->company_id ?? null;
        $myCompanyId = $recruiter->department->company_id ?? null;

        return $jobCompanyId && $myCompanyId && ($jobCompanyId == $myCompanyId);
    }

    public function apply(User $user, JobPosting $jobPosting): bool
    {
        if (!$user->isJobSeeker()) return false;
        if ($jobPosting->status !== 'Active') return false;

        $hasApplied = \App\Models\Application::where('job_seeker_id', $user->id)
            ->where('job_posting_id', $jobPosting->id)
            ->exists();

        return !$hasApplied;
    }
}
