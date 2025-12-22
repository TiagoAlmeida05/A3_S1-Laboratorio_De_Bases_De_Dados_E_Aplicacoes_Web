<?php

namespace App\Policies;

use App\Models\JobSeeker;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class JobSeekerPolicy
{
    use HandlesAuthorization;

    /**
     * Everyone can view a job seeker profile.
     */
    public function view(User $user = null, JobSeeker $jobSeeker): bool
    {
        return true;
    }

    /**
     * Only the owner of the account is able to edit/update the account.
     */
    public function update(User $user, JobSeeker $jobSeeker): bool
    {
        return $user->id === $jobSeeker->registered_user_id;
    }

    /**
     * Only the owner of the account is able to delete the account.
     */
    public function delete(User $user, JobSeeker $jobSeeker): bool
    {
        return $user->id === $jobSeeker->registered_user_id;
    }

    /*
     * Only recruiters can send messages to job seekers.
     */
    public function message(User $user, JobSeeker $jobSeeker): bool
    {
        return $user->isRecruiter() && $user->id !== $jobSeeker->registered_user_id;
    }
}
