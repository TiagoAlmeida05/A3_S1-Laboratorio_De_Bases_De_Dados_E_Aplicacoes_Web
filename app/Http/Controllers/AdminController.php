<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JobPosting;
use Illuminate\Support\Facades\Auth;
use App\Models\Report;
use App\Models\WebsiteContent;
use App\Models\User;
use App\Models\Company;
use App\Models\City;
use Illuminate\Support\Facades\Storage;
use App\Models\JobSeeker;
use App\Models\Tag;
use App\Models\Administrator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Recruiter;
use App\Models\Department;
use Carbon\Carbon;
use Illuminate\Database\QueryException;

class AdminController extends Controller
{
    
    public function manageJobs() {
        $jobs = JobPosting::orderBy('id', 'asc')->paginate(4);
        return view('admin.jobs', ['jobs' => $jobs]);
    }

    public function deleteJob($id) {
        $job = JobPosting::findOrFail($id);
        $job->delete();
        return redirect()->route('admin.jobs')->with('success', 'Job Posting removed!');
    }

    public function manageUserReports() {
        $pendingReports = Report::where('solved', false)
            ->orderBy('date', 'desc')
            ->get()
            ->groupBy(function($report) {
                return $report->getReportType();
            });

        $solvedReports = Report::where('solved', true)
            ->orderBy('date', 'desc')
            ->get();

        return view('admin.user_reports', [
            'pendingReports' => $pendingReports,
            'solvedReports' => $solvedReports
        ]);
    }

    public function solveUserReport($id) {
        $report = Report::findOrFail($id);
        
        $report->solved = true;
        $report->handled_by_id = Auth::id();
        
        $report->save();

        return redirect()->route('admin.user_reports')->with('Report marked as resolved!');
    }

    public function editPages() {
        $pages = WebsiteContent::orderBy('id', 'asc')->get();
        return view('admin.pages.index', ['pages' => $pages]);
    }

    public function showPageForm($id) {
        $page = WebsiteContent::findOrFail($id);
        return view('admin.pages.edit', ['page' => $page]);
    }

    public function updatePage(Request $request, $id) {
        $page = WebsiteContent::findOrFail($id);
        
        $request->validate([
            'headings' => 'array',
            'headings.*' => 'nullable|string',
            'texts' => 'array',
            'texts.*' => 'nullable|string',
        ]);

        $contentBlocks = [];
        $headings = $request->input('headings', []);
        $texts = $request->input('texts', []);

        if (!empty($headings)) {
            foreach ($headings as $index => $heading) {
                $text = $texts[$index] ?? '';
                
                if (!empty(trim($heading)) || !empty(trim($text))) {
                    $contentBlocks[] = [
                        'heading' => $heading,
                        'text' => $text
                    ];
                }
            }
        }

        $page->content = json_encode($contentBlocks);;
        $page->last_edited_by = Auth::id();
        
        $page->save();

        $allUserIds = \App\Models\RegisteredUser::pluck('id');
        $adminId = Auth::id();
        $message = "System Update: The '{$page->name}' page has been updated.";

        foreach ($allUserIds as $userId){
            $notifId = DB::table('notification')->insertGetId([
                'content' => $message,
                'notification_type_id' => 1,
                'registered_user_id' => $userId,
                'issue_date' => now(),
            ]);

            event(new \App\Events\PlatformAlert($message, $userId, $notifId, 1));
        }

        return redirect()->route('admin.pages')->with('success', "{$page->name} updated successfully!");
    }

    //FR042-job seeker
    public function manageJobSeekers(Request $request) {
        $query = User::whereHas('jobSeeker')
                     ->where('status', '!=', 'Deleted');

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('email', 'ilike', "%{$search}%");
            });
        }

        $users = $query->orderBy('id', 'asc')->paginate(10);

        return view('admin.job_seekers', ['users' => $users]);
    }

    public function editJobSeeker($id) {
        $jobSeeker = JobSeeker::where('registered_user_id', $id)
            ->with(['user', 'experienceEntries', 'educationEntries', 'certifications', 'awards', 'tags'])
            ->firstOrFail();

        $cities = City::orderBy('name')->get();
        $tags = Tag::where('job_posting_exclusive', false)->orderBy('name')->get();

        return view('admin.job_seekers.edit', compact('jobSeeker', 'cities', 'tags'));
    }

    public function updateJobSeeker(Request $request, $id) {
        $jobSeeker = JobSeeker::where('registered_user_id', $id)->firstOrFail();
        $user = $jobSeeker->user;

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:registered_user,email,' . $user->id,
            'city_id' => 'nullable|exists:city,id',
            'about_me' => 'nullable|string',
            'website' => 'nullable|url',
        ]);

        $user->update([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
        ]);

        if ($request->hasFile('profile_photo')) {
            if ($jobSeeker->profile_photo) {
                Storage::disk('public')->delete($jobSeeker->profile_photo);
            }
            $photoPath = $request->file('profile_photo')->store('profile_photos', 'public');
            $jobSeeker->profile_photo = $photoPath;
        }

        if ($request->hasFile('cv')) {
            if ($jobSeeker->cv) {
                Storage::disk('public')->delete($jobSeeker->cv);
            }
            $cvPath = $request->file('cv')->store('cvs', 'public');
            $jobSeeker->cv = $cvPath;
        }

        $jobSeeker->update([
            'city_id' => $request->input('city_id'),
            'about_me' => $request->input('about_me'),
            'website' => $request->input('website'),
            'show_cv' => $request->has('show_cv'),
        ]);

        return redirect()->route('admin.job_seekers')->with('success', 'Job Seeker profile updated successfully.');
    }

    public function blockUser($id) {
        $user = User::findOrFail($id);

        if ($user->status === 'Suspended') {
            $user->status = 'Active';
            $msg = 'User successfully unblocked!';
        } else {
            $user->status = 'Suspended';
            $msg = 'User successfully blocked!';
        }

        $user->save();

        return back()->with('success', $msg);
    }    

    public function deleteJobSeeker($id) {
        $user = User::findOrFail($id);

        DB::transaction(function () use ($user) {
            
            $jobSeeker = $user->jobSeeker;

            if ($jobSeeker->cv) {
                Storage::disk('public')->delete($jobSeeker->cv);
            }
            if ($jobSeeker->profile_photo) {
                Storage::disk('public')->delete($jobSeeker->profile_photo);
            }
            
            $jobSeeker->cv = null;
            $jobSeeker->profile_photo = null;
            $jobSeeker->about_me = null;
            $jobSeeker->website = null;
            $jobSeeker->show_cv = false;
            $jobSeeker->city_id = null;
            $user->birthday = null;
            $user->age = null;
            $user->google_id = null; 
            $jobSeeker->save();

            $jobSeeker->experienceEntries()->delete();
            $jobSeeker->educationEntries()->delete();
            $jobSeeker->certifications()->delete(); 
            $jobSeeker->awards()->delete();
            $jobSeeker->socialMediaProfiles()->delete();
            $jobSeeker->tags()->detach();

            $user->name = 'Deleted User ' . $user->id;
            $user->email = 'deleted_' . $user->id . '@hireup.com';
            $user->password = Hash::make(uniqid());
            $user->status = 'Deleted';
            $user->save();
        });

        return redirect()->back()->with('success', 'Job Seeker account successfully deleted.');
    }

    //FR042-company
    public function manageCompanies() {
        $companies = Company::orderBy('id', 'asc')->paginate(10);
        return view('admin.company_management', ['companies' => $companies]);
    }

    public function editCompany($id) {
        $company = Company::findOrFail($id);
        $cities = City::with('country')->orderBy('name')->get();
        
        return view('admin.companies.edit', ['company' => $company, 'cities' => $cities]);
    }

    public function updateCompany(Request $request, $id) {
        $company = Company::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'website' => 'nullable|url|max:255',
            'about_us' => 'nullable|string',
            'city_id' => 'nullable|exists:city,id',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            // Apagar antigo se existir
            if ($company->logo) {
                Storage::disk('public')->delete($company->logo);
            }
            $validated['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $company->update($validated);

        return redirect()->route('admin.companies')->with('success', 'Company successfully updated.');
    }

    public function settings()
    {
        $user = Auth::user();
        return view('admin.settings', compact('user'));
    }

    public function destroy(Request $request)
    {
        $user = Auth::user();

        if (!$user->isAdmin()) {
            return redirect('/')->with('error', 'Unauthorized.');
        }

        $activeAdmins = Administrator::whereHas('user', function ($query) {
            $query->where('status', 'Active');
        })->count();

        if ($activeAdmins <= 1) {
            return back()->with('error', 'You cannot delete the only remaining active administrator account.');
        }

        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        DB::transaction(function () use ($user) {
            $user->name = 'Deleted Admin ' . $user->id;
            $user->email = 'deleted_admin_' . $user->id . '@hireup.com';
            $user->password = Hash::make(uniqid());
            $user->status = 'Deleted';
            
            $user->save();
        });

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Administrator account successfully deleted.');
    }

    public function create()
    {
        $companies = Company::with('departments')->orderBy('name')->get();
        return view('admin.create_user', compact('companies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:registered_user,email',
            'password' => 'required|min:8',
            'birthday' => 'required|date|before:-18 years',
            'user_type' => 'required|in:job_seeker,recruiter',
            
            'department_id' => 'required_if:user_type,recruiter|nullable|exists:department,id',
        ]);

        // 2. Calcular Idade
        $birthday = Carbon::parse($request->birthday);
        $age = $birthday->age;

        try {
            DB::transaction(function () use ($request, $age) {
                
                // A. Criar User Genérico
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'birthday' => $request->birthday,
                    'age' => $age,
                    'status' => 'Active',
                    'sign_up_date' => now(),
                ]);

                // B. Criar Tipo Específico
                if ($request->user_type === 'job_seeker') {
                    JobSeeker::create([
                        'registered_user_id' => $user->id,
                        'show_cv' => true, 
                    ]);
                } 
                elseif ($request->user_type === 'recruiter') {
                    Recruiter::create([
                        'registered_user_id' => $user->id,
                        'department_id' => $request->department_id,
                        'is_company_manager' => false, // Forçamos sempre a false aqui
                    ]);
                }
            });

            return redirect()->route('admin.job_seekers')->with('success', 'User successfully created!');

        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Error creating user: ' . $e->getMessage()]);
        }
    }

    public function manageRecruiters(Request $request) {
        $query = User::whereHas('recruiter')
                     ->with('recruiter.department.company')
                     ->where('status', '!=', 'Deleted');

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('email', 'ilike', "%{$search}%");
            });
        }

        $users = $query->orderBy('id', 'asc')->paginate(10);

        return view('admin.recruiters', ['users' => $users]);
    }

    public function promoteToManager($id) {
        $newManager = User::findOrFail($id);

        $department = $newManager->recruiter->department;
        $companyId = $department->company_id;

        // Encontrar o Manager ATUAL dessa empresa
        $currentManagerRecruiter = Recruiter::where('is_company_manager', true)
            ->whereHas('department', function($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })->first();

        try {
            DB::transaction(function () use ($newManager, $currentManagerRecruiter) {
                // Desligar Trigger temporariamente para permitir a troca
                DB::statement('ALTER TABLE recruiter DISABLE TRIGGER enforce_single_company_manager');

                if ($currentManagerRecruiter) {
                    $currentManagerRecruiter->update(['is_company_manager' => false]);
                }

                $newManager->recruiter->update(['is_company_manager' => true]);
                $newManager->update(['status' => 'Active']);

                // Ligar Trigger novamente
                DB::statement('ALTER TABLE recruiter ENABLE TRIGGER enforce_single_company_manager');
            });

            return back()->with('success', 'Manager role swapped successfully!');

        } catch (\Exception $e) {
            DB::statement('ALTER TABLE recruiter ENABLE TRIGGER enforce_single_company_manager');
            return back()->with('error', 'Error swapping managers: ' . $e->getMessage());
        }
    }

    public function deleteRecruiter($id) {
        $user = User::findOrFail($id);

        // Proteção: Não apagar Company Manager (Promover outro primeiro)
        if ($user->recruiter && $user->recruiter->is_company_manager) {
            return back()->with('error', 'Cannot delete a Company Manager. Promote someone else first.');
        }

        DB::transaction(function () use ($user) {
            if ($user->recruiter) {
                $user->recruiter->update(['is_company_manager' => false]);
            }

            $user->name = 'Deleted Recruiter';
            $user->email = 'deleted_rec_' . $user->id . '@hireup.com';
            $user->password = Hash::make(uniqid());
            $user->status = 'Deleted';
            $user->birthday = null;
            $user->age = null;
            $user->google_id = null;
            
            $user->save();
        });

        return back()->with('success', 'Recruiter account deleted successfully.');
    }

    public function approveManager($user_id)
    {
        $user = User::findOrFail($user_id);
        if ($user->status !== 'Pending' || !$user->recruiter || !$user->recruiter->is_company_manager) {
            return back()->with('error', 'Invalid approval request. This user is not a pending manager.');
        }

        $user->status = 'Active';
        $user->save();
        
        return back()->with('success', 'Company approved and Manager account activated successfully.');
    }

    public function manageCities(){
        $cities = City::with('country')->orderBy('name')->paginate(10);
        $countries = \App\Models\Country::orderBy('name')->get();
        
        return view('admin.cities', compact('cities', 'countries'));
    }

    public function storeCity(Request $request){
        $request->validate([
            'name' => 'required|string|max:255|unique:city,name',
            'country_id' => 'required|exists:country,id',
        ]);

        City::create([
            'name' => $request->name,
            'country_id' => $request->country_id
        ]);

        return back()->with('success', 'City added successfully!');
    }

    public function deleteCity($id){
        try {
            $city = City::findOrFail($id);
            $city->delete();
            return back()->with('success', 'City deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Cannot delete this city because it is currently in use.');
        }
    }

    public function manageTags(){
        $tags = Tag::orderBy('name')->paginate(10);
        return view('admin.tags', compact('tags'));
    }

    public function storeTag(Request $request){
        $request->validate([
            'name' => 'required|string|max:255|unique:tag,name',
            'job_posting_exclusive' => 'nullable|boolean',
        ]);

        Tag::create([
            'name' => $request->name,
            'job_posting_exclusive' => $request->has('job_posting_exclusive')
        ]);

        return back()->with('success', 'Tag added successfully!');
    }

    public function deleteTag($id){
        try {
            $tag = Tag::findOrFail($id);
            $tag->delete();
            return back()->with('success', 'Tag deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Cannot delete this tag because it is in use.');
        }
    }
    public function manageCountries() {
        $countries = \App\Models\Country::orderBy('name')->paginate(10);
        return view('admin.countries', compact('countries'));
    }

    public function storeCountry(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255|unique:country,name',
        ]);

        \App\Models\Country::create([
            'name' => $request->name
        ]);

        return back()->with('success', 'Country added successfully!');
    }

    public function deleteCountry($id) {
        try {
            $country = \App\Models\Country::findOrFail($id);
            $country->delete();
            return back()->with('success', 'Country deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Cannot delete this country because it has cities associated with it.');
        }
    }
    public function content()
    {
        $tags = \App\Models\Tag::orderBy('name')->get();
        $cities = \App\Models\City::with('country')->orderBy('name')->get();
        $countries = \App\Models\Country::orderBy('name')->get();

        return view('admin.content_hub', compact('tags', 'cities', 'countries'));
    }
}