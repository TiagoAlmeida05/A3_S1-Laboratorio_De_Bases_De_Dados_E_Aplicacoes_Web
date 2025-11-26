<?php

namespace App\Policies;

use App\Models\JobPosting;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class JobPostingPolicy
{
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
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, JobPosting $job_posting): bool {
        if (!$user->admin) {
            return $job_posting->status !== 'Closed';
        }
        if (!$user->recruiter) {
            return false;
        }
        if ($user->recruiter->registered_user_id !== $job_posting->recruiter) return false;
        return ($job_posting->status !== 'Closed' && $job_posting->status !== 'Pending');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, JobPosting $job_posting): bool {
        if (!$user->admin) {
            return true;
        }
        if (!$user->recruiter) {
            return false;
        }
        return $user->recruiter->registered_user_id === $job_posting->recruiter;
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
}
