<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;

class CompanyController extends Controller
{
    public function show($company_id)
    {
        $company = Company::with([
            'city.country',
            'departments.recruiters.user',
            'jobPostings.recruiter.user',
            'tags',
            'socialMediaProfiles.socialMediaType'
        ])->findOrFail($company_id);

        return view('pages.company', compact('company'));
    }
}
