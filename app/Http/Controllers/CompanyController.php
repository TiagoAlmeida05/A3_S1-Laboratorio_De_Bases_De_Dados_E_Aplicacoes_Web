<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\JobPosting;
use App\Models\Application;
use App\Models\City;
use App\Models\Tag;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

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

        $companyStatistics = $company->getCompanyStatistics();

        return view('pages.company', compact('company', 'companyStatistics'));
    }

    public function edit($company_id)
    {
        $company = Company::with([
            'city.country',
            'departments',
            'tags',
            'socialMediaProfiles.socialMediaType'
        ])->findOrFail($company_id);

        Gate::authorize('update', $company);

        $cities = City::with('country')->orderBy('name')->get();
        $tags = Tag::where('job_posting_exclusive', false)->orderBy('name')->get();

        return view('pages.company_edit', compact('company', 'cities', 'tags'));
    }

    public function update(Request $request, $company_id)
    {
        $company = Company::findOrFail($company_id);

        Gate::authorize('update', $company);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'website' => 'nullable|url|max:255',
            'about_us' => 'nullable|string',
            'city_id' => 'nullable|exists:city,id',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tag,id',
        ]);

        if ($request->hasFile('logo')) {
            if ($company->logo) {
                Storage::disk('public')->delete($company->logo);
            }
            
            $logoPath = $request->file('logo')->store('logos', 'public');
            $validated['logo'] = $logoPath;
        }

        $company->update($validated);

        if ($request->has('tags')) {
            $company->tags()->sync($request->tags);
        }

        return redirect()
            ->route('companies.show', $company->id)
            ->with('success', 'Company updated successfully!');
    }
}
