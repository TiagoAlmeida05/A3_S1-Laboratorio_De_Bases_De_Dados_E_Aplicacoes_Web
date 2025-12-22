<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Department;
use App\Models\JobPosting;
use App\Models\Application;
use App\Models\City;
use App\Models\Tag;
use App\Models\User;
use App\Models\Recruiter;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;

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
        
        $manager = $company->recruiters->where('is_company_manager', true)->first();
        $isAdmin = Auth::check() && Auth::user()->isAdmin();
        if ($manager && $manager->user->status === 'Pending' && !$isAdmin){
            abort(404);
        }

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

    public function create()
    {
        $cities = City::with('country')->orderBy('name')->get();
        $tags = Tag::where('job_posting_exclusive', false)->orderBy('name')->get();

        return view('pages.company_create', compact('cities', 'tags'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'manager_email' => 'required|email|exists:registered_user,email',
            'website' => 'nullable|url|max:255',
            'about_us' => 'nullable|string',
            'city_id' => 'nullable|exists:city,id',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tag,id',
        ]);

        $targetUser = User::where('email', $request ->manager_email)->first();

        if($targetUser->administrator()->exists()){
            return back()->with('error', 'Cannot promote an Administrator to Company Manager.');
        }

        if($targetUser->recruiter()->exists()){
            return back()->with('error', 'User is already a recruiter for another company.');
        }

        try {
            $company = DB::transaction(function () use ($request, $validated, $targetUser) {
                if($request->hasFile('logo')){
                    $validated['logo'] =$request->file('logo')->store('logo', 'public');
                } 
                $companyData = collect($validated)->except(['manager_email', 'tags'])->toArray();
                $company = Company::create($companyData);

                if($request->has('tags')) {
                    $company->tags()->sync($request->tags);
                }

                $department = Department::create([
                    'name' =>'Management',
                    'company_id' => $company->id,
                ]);

                $originalData = [
                    'name' => $targetUser->name,
                    'email' => $targetUser->email,
                    'password' => $targetUser->password,
                    'birthday' => $targetUser->birthday,
                    'age' => $targetUser->age,
                    'sign_up_date' => $targetUser->sign_up_date,
                    'status' => 'Pending',
                ];

                $jobSeeker = $targetUser->jobSeeker;

                if($jobSeeker){
                    if($jobSeeker->cv) Storage::disk('public')->delete($jobSeeker->cv);
                    if($jobSeeker->profile_photo) Storage::disk('public')->delete($jobSeeker->profile_photo);

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
                $targetUser->password = Hash::make(uniqid());
                $targetUser->status = 'Deleted';
                $targetUser->save();

                $newUser = User::create($originalData);

                Recruiter::create([
                    'registered_user_id' => $newUser->id,
                    'department_id' => $department->id,
                    'is_company_manager' => true
                ]);

                try{
                    if(Schema::hasTable('sessions')){
                        DB::table('sessions')->where('user_id', $oldUserIdToLogOut)->delete();
                    }
                }catch (\Exception $e) {}
            });     
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()
                ->route('companies.show', $company->id)
                ->with('success', 'Company created successfully!');
        }catch (\Exception $e) {
            \Log::error('Company Creation Failed: ' . $e->getMessage());
            return back()->with('error', 'Error creating company: ' . $e->getMessage())->withInput();
        }
    }
}
