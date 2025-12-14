<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CompanyPolicy
{
    use HandlesAuthorization;

    public function update(User $user, Company $company)
    {
        if($user->isAdmin()){
            return true;
        }

        $recruiter = $user->recruiter;
        
        if (!$recruiter || !$recruiter->is_company_manager) {
            return false;
        }

        return $recruiter->department && $recruiter->department->company_id === $company->id;
    }
}